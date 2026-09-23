<?php
namespace App\Services;

use Core\Database;
use PDO;

/**
 * WhatsApp Gateway Client
 * Supports Meta Cloud API, UltraMsg, Baileys, and Custom WhatsApp Webhook Gateways.
 */
class WhatsAppGateway
{
    private ?PDO $db = null;
    private array $config = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->loadConfig();
    }

    /**
     * Load settings from `site_settings`
     */
    private function loadConfig(): void
    {
        $keys = ['whatsapp_enabled', 'whatsapp_gateway_url', 'whatsapp_api_token', 'whatsapp_verify_token'];
        $in = "'" . implode("','", $keys) . "'";
        $stmt = $this->db->query("SELECT `setting_key`, `setting_value` FROM `site_settings` WHERE `setting_key` IN ($in)");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $this->config = [
            'enabled'      => ($rows['whatsapp_enabled'] ?? '1') === '1',
            'gateway_url'  => !empty($rows['whatsapp_gateway_url']) ? $rows['whatsapp_gateway_url'] : 'http://localhost:3000/api/send',
            'api_token'    => $rows['whatsapp_api_token'] ?? '',
            'verify_token' => !empty($rows['whatsapp_verify_token']) ? $rows['whatsapp_verify_token'] : 'kariana_webhook_verify_2026',
            'bot_phone'    => !empty($rows['whatsapp_bot_phone']) ? $rows['whatsapp_bot_phone'] : '01717056816',
        ];
    }

    public function isEnabled(): bool
    {
        return $this->config['enabled'];
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Normalize Bangladesh and International Phone Numbers for WhatsApp
     * E.g. '01717056816' -> '8801717056816'
     */
    public static function formatWhatsAppNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '880')) {
            return $clean;
        }
        if (str_starts_with($clean, '01')) {
            return '88' . $clean;
        }
        return $clean;
    }

    /**
     * Send a WhatsApp message to a phone number
     */
    public function sendMessage(string $phone, string $message, array $extra = []): array
    {
        $to = self::formatWhatsAppNumber($phone);

        if (!$this->isEnabled()) {
            return [
                'ok'      => false,
                'status'  => 'disabled',
                'message' => 'WhatsApp Gateway is currently disabled in site settings.'
            ];
        }

        $payload = [
            'to'      => $to,
            'phone'   => $to,
            'message' => $message,
            'text'    => $message,
            'time'    => time(),
            'extra'   => $extra
        ];

        $ch = curl_init($this->config['gateway_url']);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->config['api_token'],
                'X-API-Key: ' . $this->config['api_token']
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        $result = json_decode($response ?: '', true);
        $isOk = ($httpCode >= 200 && $httpCode < 300) || (!empty($result['ok']) && $result['ok'] === true);

        // Audit log in sms_logs
        $this->logMessage($to, $message, $isOk ? 'sent' : 'failed', $response ?: $curlErr);

        return [
            'ok'        => $isOk,
            'http_code' => $httpCode,
            'response'  => $result ?: $response,
            'error'     => $curlErr ?: null
        ];
    }

    /**
     * Log message attempt in database
     */
    private function logMessage(string $phone, string $message, string $status, ?string $rawResponse): void
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO `sms_logs` (`recipient_type`, `recipient_phone`, `message`, `status`, `gateway_response`, `created_at`) 
                                       VALUES ('whatsapp', :p, :m, :s, :r, NOW())");
            $stmt->execute([
                ':p' => $phone,
                ':m' => mb_substr($message, 0, 500),
                ':s' => ($status === 'sent') ? 'sent' : 'failed',
                ':r' => mb_substr($rawResponse ?? '', 0, 1000)
            ]);
        } catch (\Throwable $e) {
            // Silently ignore log errors
        }
    }
}
