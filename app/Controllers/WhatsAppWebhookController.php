<?php
namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Services\WhatsAppGateway;
use App\Services\UnifiedMessagingService;

/**
 * WhatsApp Webhook Controller
 * Handles incoming WhatsApp webhook events and bidirectional multi-role commands.
 */
class WhatsAppWebhookController
{
    private WhatsAppGateway $waGateway;
    private UnifiedMessagingService $messagingService;

    public function __construct()
    {
        $this->waGateway = new WhatsAppGateway();
        $this->messagingService = new UnifiedMessagingService();
    }

    /**
     * Webhook verification (GET /api/whatsapp/webhook)
     */
    public function verify(Request $request): Response
    {
        $query = $request->getQueryParams();
        $hubMode = $query['hub_mode'] ?? $query['hub.mode'] ?? '';
        $hubToken = $query['hub_verify_token'] ?? $query['hub.verify_token'] ?? '';
        $hubChallenge = $query['hub_challenge'] ?? $query['hub.challenge'] ?? '';

        $config = $this->waGateway->getConfig();

        if ($hubMode === 'subscribe' && !empty($hubChallenge)) {
            if ($hubToken === $config['verify_token']) {
                return new Response($hubChallenge, 200, ['Content-Type' => 'text/plain']);
            }
            return new Response('Forbidden', 403);
        }

        return Response::json([
            'status'           => 'active',
            'service'          => 'Kariana Quran WhatsApp Gateway Webhook',
            'gateway_enabled'  => $this->waGateway->isEnabled(),
            'timestamp'        => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Incoming Webhook Event Processor (POST /api/whatsapp/webhook)
     */
    public function receive(Request $request): Response
    {
        $body = $request->getBody();
        $raw = @file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true) ?: $request->all();

        // 1. Extract sender phone and message text across various providers
        $senderPhone = '';
        $messageText = '';

        // Format A: Meta Cloud API
        if (!empty($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            $msg = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $senderPhone = $msg['from'] ?? '';
            $messageText = $msg['text']['body'] ?? '';
        }
        // Format B: Baileys / WPPConnect / Generic Webhook
        elseif (!empty($data['from']) || !empty($data['phone'])) {
            $senderPhone = $data['from'] ?? $data['phone'] ?? '';
            $messageText = $data['message'] ?? $data['body'] ?? $data['text'] ?? '';
        }
        // Format C: UltraMsg format
        elseif (!empty($data['data']['from'])) {
            $senderPhone = $data['data']['from'] ?? '';
            $messageText = $data['data']['body'] ?? '';
        }

        $senderPhone = preg_replace('/[^0-9]/', '', $senderPhone);
        $messageText = trim($messageText);

        if (empty($senderPhone) || empty($messageText)) {
            return Response::json(['ok' => true, 'ignored' => 'Empty sender or message']);
        }

        // 2. Process message using UnifiedMessagingService
        $reply = $this->messagingService->processMessage('whatsapp', $senderPhone, $messageText);

        // 3. Dispatch reply back to the WhatsApp user
        $sendRes = [];
        if (!empty($reply['text'])) {
            $fullReply = $reply['text'];
            // If buttons exist, append them as numbered textual choices for WhatsApp
            if (!empty($reply['buttons'])) {
                $fullReply .= "\n\n📌 *বিকল্পসমূহ:*\n";
                foreach ($reply['buttons'] as $row) {
                    foreach ($row as $btn) {
                        $txt = $btn['text'] ?? '';
                        if (!empty($btn['url'])) {
                            $fullReply .= "• {$txt}: " . $btn['url'] . "\n";
                        } else {
                            $fullReply .= "• {$txt}\n";
                        }
                    }
                }
            }

            $sendRes = $this->waGateway->sendMessage($senderPhone, $fullReply);
        }

        return Response::json([
            'ok'        => true,
            'sender'    => $senderPhone,
            'processed' => true,
            'send_res'  => $sendRes
        ]);
    }

    /**
     * Send test message API (POST /api/whatsapp/send)
     */
    public function sendTest(Request $request): Response
    {
        $phone = $request->input('phone', '01717056816');
        $msg = $request->input('message', 'কারিয়ানা কুরআন টেস্ট মেসেজ');

        $res = $this->waGateway->sendMessage($phone, $msg);
        return Response::json($res);
    }
}
