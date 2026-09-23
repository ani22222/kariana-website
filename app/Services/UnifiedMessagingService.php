<?php
namespace App\Services;

use Core\Database;
use PDO;

/**
 * Unified Messaging Service
 * Orchestrates multi-role bot authentication, routing, and message delivery
 * across Telegram and WhatsApp.
 */
class UnifiedMessagingService
{
    private PDO $db;
    private WhatsAppGateway $waGateway;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->waGateway = new WhatsAppGateway();
    }

    /**
     * Normalize Bangladesh phone number
     * Returns standard 11-digit representation (e.g. '01717056816')
     */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '8801')) {
            return substr($digits, 2);
        }
        if (str_starts_with($digits, '88001')) {
            return substr($digits, 3);
        }
        if (str_starts_with($digits, '1') && strlen($digits) === 10) {
            return '0' . $digits;
        }
        return $digits;
    }

    /**
     * Identify user across all tables by phone number
     */
    public function identifyUser(string $rawPhone): ?array
    {
        $phone = self::normalizePhone($rawPhone);
        if (empty($phone) || strlen($phone) < 10) {
            return null;
        }

        // 1. Founder Maulana Saddam Hossain (Super Admin Bypass)
        if ($phone === '01717056816') {
            return [
                'user_type'  => 'admin',
                'user_id'    => 1,
                'user_name'  => 'মাওলানা সাদ্দাম হোসেন',
                'role_title' => 'প্রধান পরিচালক ও প্রতিষ্ঠাতা',
                'phone'      => '01717056816',
                'meta'       => ['is_founder' => true, 'designation' => 'প্রধান পরিচালক']
            ];
        }

        // 2. Search in `directors` table (Phone or WhatsApp)
        $stmtDir = $this->db->prepare("SELECT id, name, designation, district_name, division_name, phone, whatsapp, status, total_books_ordered 
                                       FROM `directors` 
                                       WHERE `phone` LIKE :p1 OR `whatsapp` LIKE :p2 
                                       LIMIT 1");
        $likePhone = '%' . substr($phone, -10);
        $stmtDir->execute([':p1' => $likePhone, ':p2' => $likePhone]);
        $dir = $stmtDir->fetch(PDO::FETCH_ASSOC);
        if ($dir) {
            return [
                'user_type'  => 'director',
                'user_id'    => (int)$dir['id'],
                'user_name'  => $dir['name'],
                'role_title' => 'জেলা পরিচালক (' . ($dir['district_name'] ?: 'কেন্দ্রীয়') . ')',
                'phone'      => $phone,
                'meta'       => $dir
            ];
        }

        // 3. Search in `managers` table
        $stmtMgr = $this->db->prepare("SELECT id, name, designation, phone, status 
                                       FROM `managers` 
                                       WHERE `phone` LIKE :p LIMIT 1");
        $stmtMgr->execute([':p' => $likePhone]);
        $mgr = $stmtMgr->fetch(PDO::FETCH_ASSOC);
        if ($mgr) {
            return [
                'user_type'  => 'manager',
                'user_id'    => (int)$mgr['id'],
                'user_name'  => $mgr['name'],
                'role_title' => 'ম্যানেজার (' . ($mgr['designation'] ?: 'প্রশাসনিক') . ')',
                'phone'      => $phone,
                'meta'       => $mgr
            ];
        }

        // 4. Search in `teachers` table
        $stmtTea = $this->db->prepare("SELECT t.id, t.name, t.phone, t.director_id, t.total_students, t.area_name, t.status, d.name as director_name, d.phone as director_phone 
                                       FROM `teachers` t 
                                       LEFT JOIN `directors` d ON t.director_id = d.id 
                                       WHERE t.`phone` LIKE :p LIMIT 1");
        $stmtTea->execute([':p' => $likePhone]);
        $tea = $stmtTea->fetch(PDO::FETCH_ASSOC);
        if ($tea) {
            return [
                'user_type'  => 'teacher',
                'user_id'    => (int)$tea['id'],
                'user_name'  => $tea['name'],
                'role_title' => 'মুয়াল্লিম / শিক্ষক',
                'phone'      => $phone,
                'meta'       => $tea
            ];
        }

        // 5. Search in `users` table
        $stmtUser = $this->db->prepare("SELECT id, name, phone, role, district 
                                        FROM `users` 
                                        WHERE `phone` LIKE :p LIMIT 1");
        $stmtUser->execute([':p' => $likePhone]);
        $usr = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if ($usr) {
            $role = $usr['role'] ?? 'student';
            $title = match ($role) {
                'admin'    => 'অ্যাডমিন',
                'director' => 'পরিচালক',
                'teacher'  => 'শিক্ষক',
                'manager'  => 'ম্যানেজার',
                default    => 'শিক্ষার্থী / ইউজার'
            };
            return [
                'user_type'  => $role,
                'user_id'    => (int)$usr['id'],
                'user_name'  => $usr['name'],
                'role_title' => $title,
                'phone'      => $phone,
                'meta'       => $usr
            ];
        }

        return null;
    }

    /**
     * Get linked user for a specific channel and sender ID
     */
    public function getLinkedUser(string $channel, string $senderId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `bot_user_links` WHERE `channel` = :c AND `sender_id` = :s AND `is_active` = 1 LIMIT 1");
        $stmt->execute([':c' => $channel, ':s' => (string)$senderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        if (!empty($row['session_state'])) {
            $row['session_state'] = json_decode($row['session_state'], true) ?: [];
        } else {
            $row['session_state'] = [];
        }
        return $row;
    }

    /**
     * Link channel sender to a verified user profile
     */
    public function linkUser(string $channel, string $senderId, string $rawPhone): array
    {
        $userData = $this->identifyUser($rawPhone);
        if (!$userData) {
            return [
                'ok'      => false,
                'message' => "❌ দুঃখিত, '{$rawPhone}' নম্বরটি কারিয়ানা ডাটাবেজে পাওয়া যায়নি।\nঅনুগ্রহ করে ওয়েবসাইটে নিবন্ধিত মোবাইল নম্বর দিন অথবা প্রতিষ্ঠানে যোগাযোগ করুন।"
            ];
        }

        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("INSERT INTO `bot_user_links` 
            (`channel`, `sender_id`, `phone`, `user_type`, `user_id`, `user_name`, `role_title`, `is_active`, `created_at`, `updated_at`)
            VALUES (:channel, :sender_id, :phone, :user_type, :user_id, :user_name, :role_title, 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
            `phone` = VALUES(`phone`), 
            `user_type` = VALUES(`user_type`), 
            `user_id` = VALUES(`user_id`), 
            `user_name` = VALUES(`user_name`), 
            `role_title` = VALUES(`role_title`), 
            `is_active` = 1,
            `updated_at` = NOW()");

        $stmt->execute([
            ':channel'    => $channel,
            ':sender_id'  => (string)$senderId,
            ':phone'      => $userData['phone'],
            ':user_type'  => $userData['user_type'],
            ':user_id'    => $userData['user_id'],
            ':user_name'  => $userData['user_name'],
            ':role_title' => $userData['role_title']
        ]);

        return [
            'ok'        => true,
            'user_data' => $userData,
            'message'   => "✅ অভিনন্দন *{$userData['user_name']}*!\nআপনার কারিয়ানা অ্যাকাউন্ট সফলভাবে যুক্ত হয়েছে।\nপদবি: *{$userData['role_title']}*"
        ];
    }

    /**
     * Unlink user session
     */
    public function unlinkUser(string $channel, string $senderId): bool
    {
        $stmt = $this->db->prepare("UPDATE `bot_user_links` SET `is_active` = 0 WHERE `channel` = :c AND `sender_id` = :s");
        return $stmt->execute([':c' => $channel, ':s' => (string)$senderId]);
    }

    /**
     * Update session state for a user
     */
    public function updateSessionState(string $channel, string $senderId, array $state): void
    {
        $stmt = $this->db->prepare("UPDATE `bot_user_links` SET `session_state` = :st, `updated_at` = NOW() WHERE `channel` = :c AND `sender_id` = :s");
        $stmt->execute([':st' => json_encode($state, JSON_UNESCAPED_UNICODE), ':c' => $channel, ':s' => (string)$senderId]);
    }

    /**
     * Process incoming message for any linked user based on role
     */
    public function processMessage(string $channel, string $senderId, string $text, array $extra = []): array
    {
        $text = trim($text);
        $linked = $this->getLinkedUser($channel, $senderId);

        // 1. If not linked, check if user provided a phone number to login
        if (!$linked) {
            $extractedPhone = self::extractPhone($text);
            if (!empty($extra['contact_phone'])) {
                $extractedPhone = self::normalizePhone($extra['contact_phone']);
            }
            if (!$extractedPhone && preg_match('/(?:কানেক্ট|connect|start(?:_dir|_tea)?)\s*([0-9\+]+)/iu', self::toAsciiDigits($text), $m)) {
                $extractedPhone = self::normalizePhone($m[1]);
            }
            // WhatsApp Auto-Recognition: if senderId is a phone number that exists in DB, auto-link!
            if (!$extractedPhone && $channel === 'whatsapp' && !empty($senderId)) {
                $checkPhone = self::normalizePhone($senderId);
                if ($this->identifyUser($checkPhone)) {
                    $extractedPhone = $checkPhone;
                }
            }

            if ($extractedPhone) {
                $linkRes = $this->linkUser($channel, $senderId, $extractedPhone);
                if ($linkRes['ok']) {
                    $linked = $this->getLinkedUser($channel, $senderId);
                    $roleMenu = $this->buildDashboardForRole($linked['user_type'], $linked);
                    return [
                        'text'    => $linkRes['message'] . "\n━━━━━━━━━━━━━━━━━━━━\n" . $roleMenu['text'],
                        'buttons' => $roleMenu['buttons']
                    ];
                } else {
                    return [
                        'text'    => $linkRes['message'],
                        'buttons' => [
                            [['text' => '📱 ফোন নম্বর আবার দিন', 'callback_data' => 'action_auth_prompt']]
                        ]
                    ];
                }
            }

            // Prompt guest to link phone
            return [
                'text' => "🌿 *কারিয়ানা কুরআন — সমন্বিত ড্যাশবোর্ড ও বট*\n━━━━━━━━━━━━━━━━━━━━\n"
                        . "আসসালামু আলাইকুম! ড্যাশবোর্ড পরিচালনা করতে অনুগ্রহ করে আপনার নিবন্ধিত মোবাইল নম্বর লিখে পাঠান।\n\n"
                        . "📌 *উদাহরণ:* `01717056816`\nবা লিখুন: `কানেক্ট 01717xxxxxx`\n\n"
                        . "👑 মেইন অ্যাডমিন, জেলা পরিচালক, শিক্ষক ও ম্যানেজারগণ সরাসরি WhatsApp বা টেলিগ্রাম থেকেই ড্যাশবোর্ড নিয়ন্ত্রণ করতে পারেন।",
                'buttons' => [
                    [['text' => '🌐 কারিয়ানা ওয়েবসাইট ভিজিট করুন', 'url' => 'https://project.rasel.cloud/kariana/']]
                ]
            ];
        }

        // 2. User is linked - handle logout command
        if ($text === '/logout' || $text === '🚪 লগআউট' || $text === 'লগআউট') {
            $this->unlinkUser($channel, $senderId);
            return [
                'text' => "🚪 *সফলভাবে লগআউট করা হয়েছে!*\n\nঅন্য অ্যাকাউন্ট দিয়ে প্রবেশ করতে পুনরায় আপনার মোবাইল নম্বর লিখে পাঠান।",
                'buttons' => [
                    [['text' => '📱 পুনরায় লগইন করুন', 'callback_data' => 'action_auth_prompt']]
                ]
            ];
        }

        // 3. Dispatch to Role-Specific Command Processors
        return match ($linked['user_type']) {
            'admin'    => $this->handleAdminCommand($channel, $senderId, $linked, $text),
            'director' => $this->handleDirectorCommand($channel, $senderId, $linked, $text),
            'teacher'  => $this->handleTeacherCommand($channel, $senderId, $linked, $text),
            'manager'  => $this->handleManagerCommand($channel, $senderId, $linked, $text),
            default    => $this->handleStudentCommand($channel, $senderId, $linked, $text)
        };
    }

    /**
     * Process Callback Queries (Button Clicks)
     */
    public function processCallback(string $channel, string $senderId, string $data): array
    {
        $linked = $this->getLinkedUser($channel, $senderId);
        if (!$linked) {
            return [
                'text' => "অনুগ্রহ করে প্রথমে আপনার মোবাইল নম্বর দিয়ে লগইন করুন।",
                'buttons' => []
            ];
        }

        // Handle Logout
        if ($data === 'action_logout') {
            $this->unlinkUser($channel, $senderId);
            return [
                'text' => "🚪 *লগআউট সম্পন্ন হয়েছে!*\nপুনরায় লগইন করতে আপনার মোবাইল নম্বর লিখে পাঠান।",
                'buttons' => []
            ];
        }

        // Role-based callback routing
        return match ($linked['user_type']) {
            'admin'    => $this->handleAdminCallback($channel, $senderId, $linked, $data),
            'director' => $this->handleDirectorCallback($channel, $senderId, $linked, $data),
            'teacher'  => $this->handleTeacherCallback($channel, $senderId, $linked, $data),
            'manager'  => $this->handleManagerCallback($channel, $senderId, $linked, $data),
            default    => $this->buildDashboardForRole($linked['user_type'], $linked)
        };
    }

    // =========================================================================
    // ROLE: MAIN ADMIN HANDLERS
    // =========================================================================

    private function handleAdminCommand(string $channel, string $senderId, array $user, string $text): array
    {
        // Broadcast Command: /broadcast <message>
        if (str_starts_with($text, '/broadcast ') || str_starts_with($text, 'ব্রডকাস্ট ')) {
            $msg = trim(substr($text, strpos($text, ' ') + 1));
            return $this->executeBroadcast($msg, $user['user_name']);
        }

        if ($text === '📊 পরিসংখ্যান' || $text === '/stats') {
            return $this->getAdminStatsView();
        }

        if ($text === '👥 জেলা পরিচালকবৃন্দ' || $text === '/directors') {
            return $this->getAdminDirectorsSummary();
        }

        if ($text === '📚 শিক্ষক হাব' || $text === '/teachers') {
            return $this->getAdminTeachersSummary();
        }

        if ($text === '💬 ডেভেলপার বার্তা' || $text === '/inbox') {
            return $this->getAdminDeveloperInbox();
        }

        return $this->buildDashboardForRole('admin', $user);
    }

    private function handleAdminCallback(string $channel, string $senderId, array $user, string $data): array
    {
        return match ($data) {
            'adm_stats'     => $this->getAdminStatsView(),
            'adm_directors' => $this->getAdminDirectorsSummary(),
            'adm_teachers'  => $this->getAdminTeachersSummary(),
            'adm_inbox'     => $this->getAdminDeveloperInbox(),
            'adm_broadcast' => [
                'text' => "📢 *জরুরি ব্রডকাস্ট বার্তা প্রেরণের নিয়ম:*\n━━━━━━━━━━━━━━━━━━━━\nসকল জেলা পরিচালক ও শিক্ষকদের কাছে নোটিশ পাঠাতে এভাবে লিখুন:\n`/broadcast আপনার বার্তার বিবরণ`\n\nউদাহরণ:\n`/broadcast আগামী শুক্রবার কেন্দ্রীয় পরিচালকদের বিশেষ জুম মিটিং অনুষ্ঠিত হবে।`",
                'buttons' => [[['text' => '🔙 অ্যাডমিন ড্যাশবোর্ড', 'callback_data' => 'adm_dashboard']]]
            ],
            default => $this->buildDashboardForRole('admin', $user)
        };
    }

    private function getAdminStatsView(): array
    {
        $totDirs = (int)$this->db->query("SELECT COUNT(*) FROM `directors` WHERE `status` = 'active'")->fetchColumn();
        $totTeas = (int)$this->db->query("SELECT COUNT(*) FROM `teachers` WHERE `status` = 'active'")->fetchColumn();
        $totStus = (int)$this->db->query("SELECT COALESCE(SUM(total_students), 0) FROM `teachers`")->fetchColumn();
        $totOrders = (int)$this->db->query("SELECT COALESCE(SUM(total_books_ordered), 0) FROM `directors`")->fetchColumn();

        $text = "📊 *সারাদেশের সার্বিক অগ্রগতি ও পরিসংখ্যান*\n━━━━━━━━━━━━━━━━━━━━\n"
              . "🟢 সক্রিয় জেলা পরিচালক: *{$totDirs} জন*\n"
              . "👨‍🏫 নিবন্ধিত শিক্ষক (মুয়াল্লিম): *{$totTeas} জন*\n"
              . "📖 অধ্যায়নরত মোট শিক্ষার্থী: *{$totStus} জন*\n"
              . "📦 বিতরণকৃত কায়দা/আমপারা: *{$totOrders} কপি*\n\n"
              . "🔗 [ওয়েব অ্যাডমিন প্যানেল](https://project.rasel.cloud/kariana/admin)";

        return [
            'text'    => $text,
            'buttons' => [
                [['text' => '👥 পরিচালক তালিকা', 'callback_data' => 'adm_directors'], ['text' => '📚 শিক্ষক হাব', 'callback_data' => 'adm_teachers']],
                [['text' => '🔙 অ্যাডমিন ড্যাশবোর্ড', 'callback_data' => 'adm_dashboard']]
            ]
        ];
    }

    private function getAdminDirectorsSummary(): array
    {
        $stmt = $this->db->query("SELECT id, name, district_name, phone, status, total_books_ordered FROM `directors` ORDER BY id ASC LIMIT 8");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $text = "👥 *জেলা পরিচালকবৃন্দের সংক্ষিপ্ত তালিকা (প্রথম ৮ জন)*\n━━━━━━━━━━━━━━━━━━━━\n";
        foreach ($rows as $r) {
            $statusIcon = ($r['status'] === 'active') ? '🟢' : '🔴';
            $text .= "{$statusIcon} *{$r['name']}* ({$r['district_name']})\n   📱 `{$r['phone']}` | 📦 বই: {$r['total_books_ordered']} কপি\n";
        }
        $text .= "\n🌐 সম্পূর্ণ ৬০ জন পরিচালকের তালিকা ও পরিবর্তন অ্যাডমিন প্যানেলে রয়েছে।";

        return [
            'text'    => $text,
            'buttons' => [
                [['text' => '🌐 পূর্ণাঙ্গ তালিকা খুলুন', 'url' => 'https://project.rasel.cloud/kariana/admin#directors-hub']],
                [['text' => '🔙 অ্যাডমিন ড্যাশবোর্ড', 'callback_data' => 'adm_dashboard']]
            ]
        ];
    }

    private function getAdminTeachersSummary(): array
    {
        $stmt = $this->db->query("SELECT t.name, t.phone, t.total_students, d.district_name 
                                  FROM `teachers` t 
                                  LEFT JOIN `directors` d ON t.director_id = d.id 
                                  ORDER BY t.id DESC LIMIT 6");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $text = "📚 *সর্বশেষ নিবন্ধিত শিক্ষকবৃন্দ (মুয়াল্লিম)*\n━━━━━━━━━━━━━━━━━━━━\n";
        foreach ($rows as $r) {
            $text .= "• *{$r['name']}* ({$r['district_name']})\n   📱 `{$r['phone']}` | 👨‍🎓 শিক্ষার্থী: {$r['total_students']} জন\n";
        }

        return [
            'text'    => $text,
            'buttons' => [
                [['text' => '🌐 সারাদেশের শিক্ষক হাব খুলুন', 'url' => 'https://project.rasel.cloud/kariana/admin#teachers-hub']],
                [['text' => '🔙 অ্যাডমিন ড্যাশবোর্ড', 'callback_data' => 'adm_dashboard']]
            ]
        ];
    }

    private function getAdminDeveloperInbox(): array
    {
        $stmt = $this->db->query("SELECT sender_name, subject, message, created_at FROM `developer_messages` ORDER BY id DESC LIMIT 4");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $text = "💬 *ডেভেলপারকে প্রেরিত সর্বশেষ বার্তাসমূহ*\n━━━━━━━━━━━━━━━━━━━━\n";
        if (empty($rows)) {
            $text .= "কোনো নতুন বার্তা পাওয়া যায়নি।";
        } else {
            foreach ($rows as $r) {
                $time = date('d M, h:i A', strtotime($r['created_at']));
                $text .= "📩 *{$r['sender_name']}* ({$time})\n   বিষয়: {$r['subject']}\n   _\"" . mb_substr($r['message'], 0, 80) . "...\"_\n\n";
            }
        }

        return [
            'text'    => $text,
            'buttons' => [
                [['text' => '🔙 অ্যাডমিন ড্যাশবোর্ড', 'callback_data' => 'adm_dashboard']]
            ]
        ];
    }

    private function executeBroadcast(string $message, string $senderName): array
    {
        if (mb_strlen($message) < 5) {
            return [
                'text' => "⚠️ বার্তার বিষয়বস্তু অত্যন্ত সংক্ষিপ্ত। অনুগ্রহ করে বিস্তারিত বার্তা লিখুন।",
                'buttons' => []
            ];
        }

        // Fetch all directors with linked bots or phones
        $stmtDirs = $this->db->query("SELECT phone, whatsapp, name FROM `directors` WHERE `status` = 'active'");
        $dirs = $stmtDirs->fetchAll(PDO::FETCH_ASSOC);

        $sentCount = 0;
        $broadcastText = "📢 *কারিয়ানা কুরআন কেন্দ্রীয় বিজ্ঞপ্তি*\n━━━━━━━━━━━━━━━━━━━━\n"
                       . "প্রেরক: *{$senderName}*\n\n"
                       . "📝 *বার্তা:*\n{$message}\n\n"
                       . "⏰ " . date('d M Y, h:i A');

        // Deliver to linked telegram accounts
        $stmtLinks = $this->db->query("SELECT channel, sender_id FROM `bot_user_links` WHERE `user_type` IN ('director', 'teacher') AND `is_active` = 1");
        $links = $stmtLinks->fetchAll(PDO::FETCH_ASSOC);

        foreach ($links as $lnk) {
            if ($lnk['channel'] === 'telegram') {
                \tg_send($broadcastText); // Handled by bot daemon caller or helper
                $sentCount++;
            } elseif ($lnk['channel'] === 'whatsapp') {
                $this->waGateway->sendMessage($lnk['sender_id'], $broadcastText);
                $sentCount++;
            }
        }

        return [
            'text' => "✅ *ব্রডকাস্ট বার্তা সফলভাবে প্রেরণ করা হয়েছে!*\n━━━━━━━━━━━━━━━━━━━━\nমোট ডেলিভারি প্রাপক: *{$sentCount} জন*\n\nবার্তা:\n_{$message}_",
            'buttons' => [
                [['text' => '🔙 অ্যাডমিন ড্যাশবোর্ড', 'callback_data' => 'adm_dashboard']]
            ]
        ];
    }

    // =========================================================================
    // ROLE: DISTRICT DIRECTOR HANDLERS
    // =========================================================================

    private function handleDirectorCommand(string $channel, string $senderId, array $user, string $text): array
    {
        $clean = self::toAsciiDigits(mb_strtolower(trim($text)));

        // 1. Numerical & Keyword Shortcuts for WhatsApp
        if (in_array($clean, ['1', '১', 'শিক্ষক', 'আমার শিক্ষক', 'teachers', '/teachers'], true)) {
            return $this->getDirectorTeachersList($user['user_id']);
        }

        if (in_array($clean, ['2', '২', 'বই', 'বই রিকুইজিশন', 'অর্ডার', 'order', 'books', '/order'], true)) {
            return $this->getDirectorBookOrderMenu($user['user_id']);
        }

        // Direct Book Quantity Ordering via WhatsApp text e.g. "50", "৫০", "বই ১০০", "order 200"
        if (preg_match('/^(?:বই|order)?\s*(\d{2,4})$/iu', $clean, $m)) {
            $qty = (int)$m[1];
            if (in_array($qty, [50, 100, 200, 500, 1000]) || ($qty >= 10 && $qty <= 5000)) {
                return $this->executeDirectorBookOrder($user['user_id'], $qty, $user['user_name']);
            }
        }

        if (in_array($clean, ['3', '৩', 'সবক', 'সবক ক্লাস', 'sabak', '/sabak'], true)) {
            return $this->getDirectorSabakView($user['user_id']);
        }

        if (in_array($clean, ['4', '৪', 'সাদ্দাম হুজুর', 'প্রতিষ্ঠাতা', 'যোগাযোগ', 'founder', 'help', '/founder'], true)) {
            return $this->getFounderContactView();
        }

        if (in_array($clean, ['5', '৫', 'ওয়েব', 'ওয়েবসাইট', 'অ্যাপ', 'dashboard', 'web'], true)) {
            return [
                'text' => "🌐 *পরিচালক মোবাইল ওয়েব অ্যাপ লিংক:*\nhttps://project.rasel.cloud/kariana/director/dashboard",
                'buttons' => [[['text' => '🌐 অ্যাপে প্রবেশ করুন', 'url' => 'https://project.rasel.cloud/kariana/director/dashboard']]]
            ];
        }

        return $this->buildDashboardForRole('director', $user);
    }

    private function handleDirectorCallback(string $channel, string $senderId, array $user, string $data): array
    {
        if ($data === 'dir_teachers') {
            return $this->getDirectorTeachersList($user['user_id']);
        }

        if ($data === 'dir_books') {
            return $this->getDirectorBookOrderMenu($user['user_id']);
        }

        if (str_starts_with($data, 'dir_order_qty_')) {
            $qty = (int)substr($data, 14);
            return $this->executeDirectorBookOrder($user['user_id'], $qty, $user['user_name']);
        }

        if ($data === 'dir_sabak') {
            return $this->getDirectorSabakView($user['user_id']);
        }

        if ($data === 'dir_founder') {
            return $this->getFounderContactView();
        }

        return $this->buildDashboardForRole('director', $user);
    }

    private function getDirectorTeachersList(int $directorId): array
    {
        $stmt = $this->db->prepare("SELECT name, phone, area_name, total_students, status 
                                    FROM `teachers` 
                                    WHERE `director_id` = :d 
                                    ORDER BY id ASC");
        $stmt->execute([':d' => $directorId]);
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $text = "👨‍🏫 *আমার আওতাধীন শিক্ষকবৃন্দ (মুয়াল্লিম)*\n━━━━━━━━━━━━━━━━━━━━\n";
        if (empty($teachers)) {
            $text .= "আপনার জেলায় এখনো কোনো শিক্ষক নিবন্ধিত নেই। নতুন শিক্ষক যুক্ত করতে ওয়েবসাইটে লগইন করুন।";
        } else {
            $totStudents = 0;
            foreach ($teachers as $idx => $t) {
                $num = $idx + 1;
                $totStudents += (int)$t['total_students'];
                $text .= "{$num}. *{$t['name']}*\n   📱 `{$t['phone']}` | এলাকা: {$t['area_name']}\n   👨‍🎓 শিক্ষার্থী: *{$t['total_students']} জন*\n\n";
            }
            $text .= "📊 *মোট শিক্ষক:* " . count($teachers) . " জন\n👨‍🎓 *মোট শিক্ষার্থী:* {$totStudents} জন";
        }

        return [
            'text'    => $text,
            'buttons' => [
                [['text' => '📦 বই রিকুইজিশন দিন', 'callback_data' => 'dir_books']],
                [['text' => '🔙 পরিচালক ড্যাশবোর্ড', 'callback_data' => 'dir_dashboard']]
            ]
        ];
    }

    private function getDirectorBookOrderMenu(int $directorId): array
    {
        $stmt = $this->db->prepare("SELECT total_books_ordered FROM `directors` WHERE `id` = :d");
        $stmt->execute([':d' => $directorId]);
        $ordered = (int)$stmt->fetchColumn();

        $text = "📦 *কারিয়ানা কায়দা ও আমপারা বই রিকুইজিশন*\n━━━━━━━━━━━━━━━━━━━━\n"
              . "আপনার জেলায় এযাবৎ সরবরাহকৃত মোট বই: *{$ordered} কপি*\n\n"
              . "আপনার আওতাধীন মাদরাসা/শিক্ষকদের জন্য প্রয়োজনীয় কপির সংখ্যা নির্বাচন করুন (১-ট্যাপে অর্ডার):";

        return [
            'text'    => $text,
            'buttons' => [
                [
                    ['text' => '📦 ৫০ কপি', 'callback_data' => 'dir_order_qty_50'],
                    ['text' => '📦 ১০০ কপি', 'callback_data' => 'dir_order_qty_100']
                ],
                [
                    ['text' => '📦 ২০০ কপি', 'callback_data' => 'dir_order_qty_200'],
                    ['text' => '📦 ৫০০ কপি', 'callback_data' => 'dir_order_qty_500']
                ],
                [['text' => '🔙 পরিচালক ড্যাশবোর্ড', 'callback_data' => 'dir_dashboard']]
            ]
        ];
    }

    private function executeDirectorBookOrder(int $directorId, int $qty, string $directorName): array
    {
        // 1. Update total_books_ordered in directors table
        $stmt = $this->db->prepare("UPDATE `directors` SET `total_books_ordered` = `total_books_ordered` + :qty WHERE `id` = :d");
        $stmt->execute([':qty' => $qty, ':d' => $directorId]);

        // 2. Insert into book_distributions / director_requests if table exists
        try {
            $stmtDist = $this->db->prepare("INSERT INTO `book_distributions` (`director_id`, `quantity`, `status`, `notes`, `created_at`) 
                                            VALUES (:d, :q, 'pending', 'Ordered via Bot', NOW())");
            $stmtDist->execute([':d' => $directorId, ':q' => $qty]);
        } catch (\Throwable $e) {}

        // 3. Notify Admin in developer_messages
        try {
            $stmtMsg = $this->db->prepare("INSERT INTO `developer_messages` (`sender_name`, `sender_phone`, `subject`, `message`, `is_read`, `created_at`) 
                                           VALUES (:name, 'Bot', 'নতুন বই রিকুইজিশন', :msg, 0, NOW())");
            $stmtMsg->execute([
                ':name' => $directorName,
                ':msg'  => "পরিচালক {$directorName} টেলিগ্রাম বটের মাধ্যমে {$qty} কপি বইয়ের রিকুইজিশন পাঠিয়েছেন।"
            ]);
        } catch (\Throwable $e) {}

        return [
            'text' => "🎉 *বই রিকুইজিশন সফলভাবে গৃহীত হয়েছে!*\n━━━━━━━━━━━━━━━━━━━━\n"
                    . "অর্ডারকৃত সংখ্যা: *{$qty} কপি*\n"
                    . "স্ট্যাটাস: *কেন্দ্রীয় অনুমোদনের অপেক্ষায় ⏳*\n\n"
                    . "প্রধান পরিচালক মাওলানা সাদ্দাম হোসেন সাহেবের নিকট নোটিফিকেশন পৌঁছে গেছে। শীঘ্রই কেন্দ্রীয় দপ্তর থেকে যোগাযোগ করা হবে।",
            'buttons' => [
                [['text' => '📦 আরো অর্ডার করুন', 'callback_data' => 'dir_books']],
                [['text' => '🔙 পরিচালক ড্যাশবোর্ড', 'callback_data' => 'dir_dashboard']]
            ]
        ];
    }

    private function getDirectorSabakView(int $directorId): array
    {
        $text = "🎓 *সবক ক্লাস উদ্বোধন ও পরীক্ষণ*\n━━━━━━━━━━━━━━━━━━━━\n"
              . "আপনার আওতাধীন শিক্ষকগণের নতুন ব্যাচ ও সবক উদ্বোধনের তালিকা এখানে সরাসরি দেখা যাবে।\n\n"
              . "কোনো নতুন আবেদন আসলে আপনাকে স্বয়ংক্রিয়ভাবে মেসেজ পাঠিয়ে জানানো হবে।";

        return [
            'text'    => $text,
            'buttons' => [
                [['text' => '🔙 পরিচালক ড্যাশবোর্ড', 'callback_data' => 'dir_dashboard']]
            ]
        ];
    }

    private function getFounderContactView(): array
    {
        $text = "👑 *প্রতিষ্ঠাতা ও প্রধান পরিচালক যোগাযোগ*\n━━━━━━━━━━━━━━━━━━━━\n"
              . "নাম: *মাওলানা সাদ্দাম হোসেন*\n"
              . "পদবি: প্রতিষ্ঠাতা ও প্রধান পরিচালক\n"
              . "মোবাইল: `01717056816`\n"
              . "হোয়াটসঅ্যাপ: `01717056816`\n\n"
              . "জরুরি সাংগঠনিক প্রয়োজনে সরাসরি কল অথবা হোয়াটসঅ্যাপে যোগাযোগ করুন।";

        return [
            'text'    => $text,
            'buttons' => [
                [['text' => '📞 কল করুন (01717056816)', 'url' => 'tel:01717056816']],
                [['text' => '💬 হোয়াটসঅ্যাপ বার্তা', 'url' => 'https://wa.me/8801717056816']],
                [['text' => '🔙 পূর্বের মেনু', 'callback_data' => 'dir_dashboard']]
            ]
        ];
    }

    // =========================================================================
    // ROLE: TEACHER / MUALLIM HANDLERS
    // =========================================================================

    private function handleTeacherCommand(string $channel, string $senderId, array $user, string $text): array
    {
        $cleanText = self::toAsciiDigits(mb_strtolower(trim($text)));

        // 1. Numerical & Keyword Shortcuts for WhatsApp
        if (in_array($cleanText, ['1', '১', 'পরিচালক', 'আমার পরিচালক', 'director', '/director'], true)) {
            return $this->getTeacherDirectorContact($user['user_id']);
        }

        // Check if teacher sent a number to update student count e.g. "35", "ছাত্র ৪০"
        if (preg_match('/^(?:ছাত্র|শিক্ষার্থী|student|students|সংখ্যা)?\s*(\d{1,4})$/iu', $cleanText, $m)) {
            $newCount = (int)$m[1];
            return $this->executeTeacherStudentCountUpdate($user['user_id'], $newCount);
        }

        if (in_array($cleanText, ['2', '২', 'বই', 'বইয়ের চাহিদা', 'books', '/books'], true)) {
            return [
                'text' => "📦 *কিতাবের প্রয়োজনীয়তা:* আপনার মক্তব/মাদরাসার জন্য প্রয়োজনীয় বইয়ের সংখ্যা আপনার জেলা পরিচালককে জানান অথবা সরাসরি কল দিন।\n\nপরিচালকের সাথে যোগাযোগ করতে লিখুন: ১",
                'buttons' => [
                    [['text' => '👤 আমার পরিচালকের নম্বর', 'callback_data' => 'tea_director']],
                    [['text' => '🔙 শিক্ষক ড্যাশবোর্ড', 'callback_data' => 'tea_dashboard']]
                ]
            ];
        }

        if (in_array($cleanText, ['3', '৩', 'সবক', 'সবক ক্লাস', 'আবেদন', 'sabak'], true)) {
            return [
                'text' => "🎓 *সবক ক্লাস উদ্বোধন আবেদন:* নতুন ব্যাচের সবক উদ্বোধনের জন্য শিক্ষক মোবাইল অ্যাপে প্রবেশ করে আবেদন জমা দিন।\n\n🌐 অ্যাপ লিংক:\nhttps://project.rasel.cloud/kariana/teacher/dashboard",
                'buttons' => [
                    [['text' => '🌐 শিক্ষক অ্যাপ খুলুন', 'url' => 'https://project.rasel.cloud/kariana/teacher/dashboard']]
                ]
            ];
        }

        if (in_array($cleanText, ['4', '৪', 'ওয়েব', 'ওয়েবসাইট', 'অ্যাপ', 'dashboard', 'web'], true)) {
            return [
                'text' => "🌐 *শিক্ষক মোবাইল ওয়েব অ্যাপ লিংক:*\nhttps://project.rasel.cloud/kariana/teacher/dashboard",
                'buttons' => [[['text' => '🌐 অ্যাপে প্রবেশ করুন', 'url' => 'https://project.rasel.cloud/kariana/teacher/dashboard']]]
            ];
        }

        return $this->buildDashboardForRole('teacher', $user);
    }

    private function handleTeacherCallback(string $channel, string $senderId, array $user, string $data): array
    {
        return match ($data) {
            'tea_director' => $this->getTeacherDirectorContact($user['user_id']),
            'tea_students' => [
                'text' => "📈 *শিক্ষার্থী সংখ্যা হালনাগাদ:*\nআপনার বর্তমান ছাত্র-ছাত্রীর সংখ্যা লিখে মেসেজ পাঠান (যেমন: `৩৫`)।",
                'buttons' => [[['text' => '🔙 শিক্ষক ড্যাশবোর্ড', 'callback_data' => 'tea_dashboard']]]
            ],
            default => $this->buildDashboardForRole('teacher', $user)
        };
    }

    private function getTeacherDirectorContact(int $teacherId): array
    {
        $stmt = $this->db->prepare("SELECT t.name, t.total_students, d.name as director_name, d.phone as director_phone, d.whatsapp as director_whatsapp, d.district_name 
                                    FROM `teachers` t 
                                    LEFT JOIN `directors` d ON t.director_id = d.id 
                                    WHERE t.id = :id");
        $stmt->execute([':id' => $teacherId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['director_name'])) {
            $text = "⚠️ আপনার সাথে কোনো জেলা পরিচালক এখনো নির্ধারিত হয়নি। কেন্দ্রীয় দপ্তরে যোগাযোগ করুন।";
            return ['text' => $text, 'buttons' => []];
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $row['director_phone'] ?? '');
        $cleanWa = preg_replace('/[^0-9]/', '', $row['director_whatsapp'] ?? $cleanPhone);

        $text = "👤 *আপনার দায়িত্বপ্রাপ্ত জেলা পরিচালক*\n━━━━━━━━━━━━━━━━━━━━\n"
              . "নাম: *{$row['director_name']}*\n"
              . "জেলা: *{$row['district_name']}*\n"
              . "মোবাইল: `{$row['director_phone']}`\n\n"
              . "কিতাব সংগ্রহ, সবক উদ্বোধন বা ক্লাসের নির্দেশনার জন্য পরিচালকের সাথে যোগাযোগ রাখুন।";

        return [
            'text'    => $text,
            'buttons' => [
                [
                    ['text' => '📞 কল করুন', 'url' => 'tel:' . $cleanPhone],
                    ['text' => '💬 হোয়াটসঅ্যাপ', 'url' => 'https://wa.me/88' . $cleanWa]
                ],
                [['text' => '🔙 শিক্ষক ড্যাশবোর্ড', 'callback_data' => 'tea_dashboard']]
            ]
        ];
    }

    private function executeTeacherStudentCountUpdate(int $teacherId, int $newCount): array
    {
        $stmt = $this->db->prepare("UPDATE `teachers` SET `total_students` = :cnt, `updated_at` = NOW() WHERE `id` = :id");
        $stmt->execute([':cnt' => $newCount, ':id' => $teacherId]);

        return [
            'text' => "✅ *শিক্ষার্থী সংখ্যা সফলভাবে আপডেট হয়েছে!*\n━━━━━━━━━━━━━━━━━━━━\n"
                    . "বর্তমান মোট শিক্ষার্থী: *{$newCount} জন*\n"
                    . "তথ্যটি আপনার জেলা পরিচালকের ড্যাশবোর্ড এবং কেন্দ্রীয় সার্ভারে সংরক্ষিত হয়েছে।",
            'buttons' => [
                [['text' => '🔙 শিক্ষক ড্যাশবোর্ড', 'callback_data' => 'tea_dashboard']]
            ]
        ];
    }

    // =========================================================================
    // ROLE: MANAGER HANDLERS
    // =========================================================================

    private function handleManagerCommand(string $channel, string $senderId, array $user, string $text): array
    {
        if ($text === '📦 স্টক ও কিতাব' || $text === '/stock') {
            $totOrders = (int)$this->db->query("SELECT COALESCE(SUM(total_books_ordered), 0) FROM `directors`")->fetchColumn();
            return [
                'text' => "📦 *কারিয়ানা প্রকাশনী ও স্টক বিবরণ*\n━━━━━━━━━━━━━━━━━━━━\n"
                        . "মোট বিতরণকৃত বই: *{$totOrders} কপি*\n\n"
                        . "নতুন বইয়ের চাহিদা ও চালানের বিস্তারিত তথ্যের জন্য ওয়েব প্যানেলে প্রবেশ করুন।",
                'buttons' => [
                    [['text' => '🌐 ওয়েব পোর্টাল', 'url' => 'https://project.rasel.cloud/kariana/login']],
                    [['text' => '🔙 ম্যানেজার ড্যাশবোর্ড', 'callback_data' => 'mgr_dashboard']]
                ]
            ];
        }

        return $this->buildDashboardForRole('manager', $user);
    }

    private function handleManagerCallback(string $channel, string $senderId, array $user, string $data): array
    {
        return $this->buildDashboardForRole('manager', $user);
    }

    // =========================================================================
    // ROLE: STUDENT HANDLERS
    // =========================================================================

    private function handleStudentCommand(string $channel, string $senderId, array $user, string $text): array
    {
        return $this->buildDashboardForRole('student', $user);
    }

    // =========================================================================
    // DASHBOARD MENU BUILDER PER ROLE
    // =========================================================================

    public function buildDashboardForRole(string $role, array $user): array
    {
        $name = $user['user_name'] ?? 'ব্যবহারকারী';
        $title = $user['role_title'] ?? 'সদস্য';

        return match ($role) {
            'admin' => [
                'text' => "👑 *মেইন অ্যাডমিন কন্ট্রোল সেন্টার*\n━━━━━━━━━━━━━━━━━━━━\n"
                        . "আসসালামু আলাইকুম *{$name}*!\nপদবি: *{$title}*\n\n"
                        . "টেলিগ্রাম থেকে আপনি সারাদেশের সকল শিক্ষক, পরিচালক এবং কিতাব বিতরণ পরিচালনা করতে পারবেন।",
                'buttons' => [
                    [
                        ['text' => '📊 সারাদেশের পরিসংখ্যান', 'callback_data' => 'adm_stats'],
                        ['text' => '👥 জেলা পরিচালকবৃন্দ', 'callback_data' => 'adm_directors']
                    ],
                    [
                        ['text' => '📚 শিক্ষক হাব', 'callback_data' => 'adm_teachers'],
                        ['text' => '💬 ডেভেলপার বার্তা', 'callback_data' => 'adm_inbox']
                    ],
                    [
                        ['text' => '📢 জরুরি ব্রডকাস্ট বার্তা', 'callback_data' => 'adm_broadcast']
                    ],
                    [
                        ['text' => '🌐 পূর্ণাঙ্গ ওয়েব প্যানেল', 'url' => 'https://project.rasel.cloud/kariana/admin'],
                        ['text' => '🚪 লগআউট', 'callback_data' => 'action_logout']
                    ]
                ]
            ],
            'director' => [
                'text' => "📋 *জেলা পরিচালক কন্ট্রোল সেন্টার*\n━━━━━━━━━━━━━━━━━━━━\n"
                        . "আসসালামু আলাইকুম *{$name}*!\nদায়িত্ব: *{$title}*\n\n"
                        . "পরিচালনা করতে নিচের যেকোনো অপশন বেছে নিন বা সংখ্যা লিখে পাঠান:\n"
                        . "[১] 👨‍🏫 আমার শিক্ষকবৃন্দ\n"
                        . "[২] 📦 বই রিকুইজিশন (৫০ / ১০০ / ২০০ কপি)\n"
                        . "[৩] 🎓 সবক ক্লাসের আবেদন\n"
                        . "[৪] 📞 সাদ্দাম হুজুর / কেন্দ্রীয় হেল্পলাইন\n"
                        . "[৫] 🌐 পরিচালক মোবাইল ওয়েব অ্যাপ\n\n"
                        . "💡 WhatsApp-এ শুধু সংখ্যাটি লিখে পাঠালেই উত্তর পাবেন (যেমন: ১ অথবা ২)।",
                'buttons' => [
                    [
                        ['text' => '👨‍🏫 আমার শিক্ষকবৃন্দ', 'callback_data' => 'dir_teachers'],
                        ['text' => '📦 বই রিকুইজিশন', 'callback_data' => 'dir_books']
                    ],
                    [
                        ['text' => '🎓 সবক ক্লাস আবেদন', 'callback_data' => 'dir_sabak'],
                        ['text' => '📞 সাদ্দাম হুজুর হেল্পলাইন', 'callback_data' => 'dir_founder']
                    ],
                    [
                        ['text' => '🌐 মোবাইল ওয়েব অ্যাপ', 'url' => 'https://project.rasel.cloud/kariana/director/dashboard'],
                        ['text' => '🚪 লগআউট', 'callback_data' => 'action_logout']
                    ]
                ]
            ],
            'teacher' => [
                'text' => "📚 *মুয়াল্লিম / শিক্ষক কন্ট্রোল হাব*\n━━━━━━━━━━━━━━━━━━━━\n"
                        . "আসসালামু আলাইকুম *{$name}*!\nভূমিকা: *{$title}*\n\n"
                        . "পরিচালনা করতে নিচের যেকোনো সংখ্যা লিখে পাঠান:\n"
                        . "[১] 👤 আমার পরিচালক (কল ও যোগাযোগ)\n"
                        . "[২] 📦 কিতাবের প্রয়োজনীয়তা\n"
                        . "[৩] 🎓 সবক ক্লাস উদ্বোধন আবেদন\n"
                        . "[৪] 🌐 শিক্ষক মোবাইল ওয়েব অ্যাপ\n\n"
                        . "💡 ছাত্র সংখ্যা হালনাগাদ করতে সরাসরি সংখ্যা লিখে পাঠান (যেমন: ৩৫ বা ছাত্র ৪০)।",
                'buttons' => [
                    [
                        ['text' => '👤 আমার পরিচালক', 'callback_data' => 'tea_director'],
                        ['text' => '📈 ছাত্র সংখ্যা আপডেট', 'callback_data' => 'tea_students']
                    ],
                    [
                        ['text' => '📦 বইয়ের চাহিদা', 'callback_data' => 'tea_books'],
                        ['text' => '🌐 শিক্ষক ওয়েব অ্যাপ', 'url' => 'https://project.rasel.cloud/kariana/teacher/dashboard']
                    ],
                    [
                        ['text' => '🚪 লগআউট', 'callback_data' => 'action_logout']
                    ]
                ]
            ],
            'manager' => [
                'text' => "💼 *ম্যানেজার কন্ট্রোল হাব*\n━━━━━━━━━━━━━━━━━━━━\n"
                        . "আসসালামু আলাইকুম *{$name}*!\nদায়িত্ব: *{$title}*\n\n"
                        . "দাপ্তরিক হিসাব ও ব্যবস্থাপনা পরিচালনা করুন:",
                'buttons' => [
                    [
                        ['text' => '📦 স্টক ও কিতাব', 'callback_data' => 'mgr_stock'],
                        ['text' => '🌐 ওয়েব পোর্টাল', 'url' => 'https://project.rasel.cloud/kariana/login']
                    ],
                    [
                        ['text' => '🚪 লগআউট', 'callback_data' => 'action_logout']
                    ]
                ]
            ],
            default => [
                'text' => "👤 *শিক্ষার্থী প্রোফাইল ও সহায়ক হাব*\n━━━━━━━━━━━━━━━━━━━━\n"
                        . "স্বাগতম *{$name}*!\n\n"
                        . "কুরআন শিক্ষা ও ইসলামিক টুলসের জন্য নিচের লিংকগুলো ব্যবহার করুন:",
                'buttons' => [
                    [
                        ['text' => '📖 কুরআন রিডার', 'url' => 'https://project.rasel.cloud/kariana/quran-app'],
                        ['text' => '🕌 নামাজের সময়', 'url' => 'https://project.rasel.cloud/kariana/prayer-times']
                    ],
                    [
                        ['text' => '👤 ব্যক্তিগত প্রোফাইল', 'url' => 'https://project.rasel.cloud/kariana/profile'],
                        ['text' => '🚪 লগআউট', 'callback_data' => 'action_logout']
                    ]
                ]
            ]
        };
    }

    /**
     * Dispatch notification to a user via Telegram and/or WhatsApp
     */
    public function sendNotificationToUser(string $userType, int $userId, string $message): array
    {
        $phone = '';
        $name = '';
        $results = ['telegram' => false, 'whatsapp' => false];

        if ($userType === 'director') {
            $stmt = $this->db->prepare("SELECT name, phone, whatsapp FROM `directors` WHERE `id` = :id");
            $stmt->execute([':id' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $phone = !empty($row['whatsapp']) ? $row['whatsapp'] : $row['phone'];
                $name = $row['name'];
            }
        } elseif ($userType === 'teacher') {
            $stmt = $this->db->prepare("SELECT name, phone FROM `teachers` WHERE `id` = :id");
            $stmt->execute([':id' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $phone = $row['phone'];
                $name = $row['name'];
            }
        } elseif ($userType === 'admin') {
            $phone = '01717056816';
            $name = 'মাওলানা সাদ্দাম হোসেন';
        }

        // 1. Send via WhatsApp Gateway if phone exists
        if (!empty($phone)) {
            $waRes = $this->waGateway->sendMessage($phone, $message);
            $results['whatsapp'] = $waRes['ok'] ?? false;
        }

        // 2. Send via Telegram if linked
        $stmtTg = $this->db->prepare("SELECT sender_id FROM `bot_user_links` WHERE `user_type` = :ut AND `user_id` = :uid AND `channel` = 'telegram' AND `is_active` = 1 LIMIT 1");
        $stmtTg->execute([':ut' => $userType, ':uid' => $userId]);
        $tgChatId = $stmtTg->fetchColumn();

        if ($tgChatId && function_exists('tg_send')) {
            \tg_send($message);
            $results['telegram'] = true;
        }

        return $results;
    }

    /**
     * Convert Bengali digits to ASCII English digits
     */
    public static function toAsciiDigits(string $text): string
    {
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($bn, $en, $text);
    }

    /**
     * Helper to extract 11-digit phone number from freeform text
     */
    private static function extractPhone(string $text): ?string
    {
        $text = self::toAsciiDigits($text);
        if (preg_match('/(?:01|\+?8801)[3-9]\d{8}/', $text, $m)) {
            return self::normalizePhone($m[0]);
        }
        return null;
    }
}
