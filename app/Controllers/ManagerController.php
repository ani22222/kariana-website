<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Core\Database;
use Core\Session;
use Core\Csrf;

/**
 * Operations & Accounts Manager Controller
 * Handles Accounts Ledger (আয়-ব্যয় হিসাব খাতা), Vouchers, Book/Quran Dispatches, and Courier Tracking
 */
class ManagerController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Guard: Require Manager Authentication
     */
    private function requireAuth(Request $request): ?Response
    {
        $mgr = Session::get('manager');
        if (empty($mgr) || empty($mgr['id'])) {
            return Response::redirect('/manager/login');
        }
        return null;
    }

    /**
     * Manager Login Page
     */
    public function login(Request $request): Response
    {
        $mgr = Session::get('manager');
        if (!empty($mgr) && !empty($mgr['id'])) {
            return Response::redirect('/manager/dashboard');
        }

        return new Response(View::render('manager/login', [
            'title' => 'অপারেশনস ও হিসাব ব্যবস্থাপক লগইন | কারিয়ানা কুরআন',
        ], 'layouts/main'));
    }

    /**
     * Handle Manager Login Form
     */
    public function handleLogin(Request $request): Response
    {
        $username = trim((string)$request->post('username', ''));
        $password = (string)$request->post('password', '');

        if ($username === '' || $password === '') {
            Session::flash('error', 'ব্যবহারকারী নাম এবং পাসওয়ার্ড প্রদান করুন।');
            return Response::redirect('/manager/login');
        }

        $stmt = $this->db->prepare("SELECT * FROM `managers` WHERE (`username` = :u OR `email` = :e OR `phone` = :p) AND `status` = 'active' LIMIT 1");
        $stmt->execute(['u' => $username, 'e' => $username, 'p' => $username]);
        $mgr = $stmt->fetch();

        if ($mgr && password_verify($password, $mgr['password'])) {
            Session::set('manager', [
                'id'          => $mgr['id'],
                'name'        => $mgr['name'],
                'username'    => $mgr['username'],
                'designation' => $mgr['designation'],
                'phone'       => $mgr['phone'],
            ]);
            Session::flash('success', 'স্বাগতম, ' . $mgr['name'] . '! অপারেশনস ও হিসাব পোর্টালে সফলভাবে প্রবেশ করেছেন।');
            return Response::redirect('/manager/dashboard');
        }

        Session::flash('error', 'ভুল ইউজারনেম অথবা পাসওয়ার্ড। আবার চেষ্টা করুন।');
        return Response::redirect('/manager/login');
    }

    /**
     * Manager Logout
     */
    public function logout(): Response
    {
        Session::remove('manager');
        Session::flash('info', 'আপনি সফলভাবে ব্যবস্থাপক পোর্টাল থেকে প্রস্থান করেছেন।');
        return Response::redirect('/manager/login');
    }

    /**
     * Manager Main Dashboard
     */
    public function dashboard(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $mgr = Session::get('manager');

        // Financial Stats
        $totalIncome = (float)$this->db->query("SELECT COALESCE(SUM(amount), 0) FROM `accounts_ledger` WHERE `type` = 'income'")->fetchColumn();
        $totalExpense = (float)$this->db->query("SELECT COALESCE(SUM(amount), 0) FROM `accounts_ledger` WHERE `type` = 'expense'")->fetchColumn();
        $balance = $totalIncome - $totalExpense;

        // Inventory & Distribution Stats
        $totalDispatched = (int)$this->db->query("SELECT COALESCE(SUM(quantity), 0) FROM `book_distributions` WHERE `delivery_status` IN ('dispatched', 'in_transit', 'delivered')")->fetchColumn();
        $pendingDispatches = (int)$this->db->query("SELECT COUNT(*) FROM `book_distributions` WHERE `delivery_status` = 'pending'")->fetchColumn();
        $dueAmount = (float)$this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM `book_distributions` WHERE `payment_status` = 'due'")->fetchColumn();

        // Recent Ledger Entries (5 entries)
        $recentLedger = $this->db->query("
            SELECT l.*, d.name as director_name, d.district_name 
            FROM `accounts_ledger` l 
            LEFT JOIN `directors` d ON l.related_director_id = d.id 
            ORDER BY l.transaction_date DESC, l.id DESC LIMIT 5
        ")->fetchAll();

        // Recent Book Dispatches (5 entries)
        $recentDistributions = $this->db->query("
            SELECT bd.*, b.title as book_title, d.name as director_name, d.district_name 
            FROM `book_distributions` bd 
            LEFT JOIN `books` b ON bd.book_id = b.id 
            LEFT JOIN `directors` d ON bd.director_id = d.id 
            ORDER BY bd.created_at DESC LIMIT 5
        ")->fetchAll();

        // Pending Book Requests from District Directors
        $directorRequests = $this->db->query("
            SELECT r.*, d.name as director_name, d.district_name, d.phone as director_phone 
            FROM `director_requests` r 
            JOIN `directors` d ON r.director_id = d.id 
            WHERE r.status = 'pending' 
            ORDER BY r.created_at DESC LIMIT 5
        ")->fetchAll();

        return new Response(View::render('manager/dashboard', [
            'title'               => 'অপারেশনস ও হিসাব ড্যাশবোর্ড | কারিয়ানা কুরআন',
            'manager'             => $mgr,
            'stats'               => [
                'totalIncome'       => $totalIncome,
                'totalExpense'      => $totalExpense,
                'balance'           => $balance,
                'totalDispatched'   => $totalDispatched,
                'pendingDispatches' => $pendingDispatches,
                'dueAmount'         => $dueAmount,
            ],
            'recentLedger'        => $recentLedger,
            'recentDistributions' => $recentDistributions,
            'directorRequests'    => $directorRequests,
        ], 'layouts/main'));
    }

    /**
     * Accounts Ledger Hub (আয়-ব্যয় হিসাব খাতা)
     */
    public function ledger(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $filterType = (string)$request->get('type', 'all');
        $filterCategory = (string)$request->get('category', 'all');

        $query = "
            SELECT l.*, d.name as director_name, d.district_name 
            FROM `accounts_ledger` l 
            LEFT JOIN `directors` d ON l.related_director_id = d.id 
            WHERE 1=1
        ";
        $params = [];

        if (in_array($filterType, ['income', 'expense'], true)) {
            $query .= " AND l.type = :type";
            $params['type'] = $filterType;
        }

        if ($filterCategory !== 'all' && $filterCategory !== '') {
            $query .= " AND l.category = :category";
            $params['category'] = $filterCategory;
        }

        $query .= " ORDER BY l.transaction_date DESC, l.id DESC LIMIT 50";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        // Aggregates
        $totalIncome = (float)$this->db->query("SELECT COALESCE(SUM(amount), 0) FROM `accounts_ledger` WHERE `type` = 'income'")->fetchColumn();
        $totalExpense = (float)$this->db->query("SELECT COALESCE(SUM(amount), 0) FROM `accounts_ledger` WHERE `type` = 'expense'")->fetchColumn();
        $balance = $totalIncome - $totalExpense;

        $directors = $this->db->query("SELECT `id`, `name`, `district_name` FROM `directors` WHERE `status` = 'active' ORDER BY `name` ASC")->fetchAll();

        return new Response(View::render('manager/ledger', [
            'title'          => 'আয়-ব্যয় হিসাব খাতা ও ভাউচার | কারিয়ানা কুরআন',
            'records'        => $records,
            'filterType'     => $filterType,
            'filterCategory' => $filterCategory,
            'totalIncome'    => $totalIncome,
            'totalExpense'   => $totalExpense,
            'balance'        => $balance,
            'directors'      => $directors,
        ], 'layouts/main'));
    }

    /**
     * Add Ledger Entry (Income or Expense Voucher)
     */
    public function saveLedger(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $mgr = Session::get('manager');
        $type = (string)$request->post('type', 'income');
        $category = trim((string)$request->post('category', 'বই বিক্রি'));
        $amount = (float)$request->post('amount', 0);
        $txDate = trim((string)$request->post('transaction_date', date('Y-m-d')));
        $voucherNo = trim((string)$request->post('voucher_no', 'VCH-' . date('Ymd-His')));
        $directorId = (int)$request->post('related_director_id', 0) ?: null;
        $description = trim((string)$request->post('description', ''));

        if ($amount <= 0 || $description === '') {
            Session::flash('error', 'টাকার পরিমাণ এবং বিবরণ সঠিকভাবে পূরণ করুন।');
            return Response::redirect('/manager/ledger');
        }

        $stmt = $this->db->prepare("
            INSERT INTO `accounts_ledger` 
            (`manager_id`, `type`, `category`, `amount`, `transaction_date`, `description`, `voucher_no`, `related_director_id`)
            VALUES (:mgr, :type, :cat, :amount, :tx_date, :desc, :vch, :dir)
        ");
        $stmt->execute([
            'mgr'     => $mgr['id'] ?? null,
            'type'    => in_array($type, ['income', 'expense'], true) ? $type : 'income',
            'cat'     => $category,
            'amount'  => $amount,
            'tx_date' => $txDate ?: date('Y-m-d'),
            'desc'    => $description,
            'vch'     => $voucherNo,
            'dir'     => $directorId,
        ]);

        Session::flash('success', "আলহামদুলিল্লাহ! ভাউচার '{$voucherNo}' সফলভাবে হিসাব খাতায় অন্তর্ভুক্ত হয়েছে।");
        return Response::redirect('/manager/ledger');
    }

    /**
     * Book Distributions & Dispatch Hub
     */
    public function distributions(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $distributions = $this->db->query("
            SELECT bd.*, b.title as book_title, d.name as director_name, d.district_name, d.phone as director_phone 
            FROM `book_distributions` bd 
            LEFT JOIN `books` b ON bd.book_id = b.id 
            LEFT JOIN `directors` d ON bd.director_id = d.id 
            ORDER BY bd.id DESC LIMIT 50
        ")->fetchAll();

        $books = $this->db->query("SELECT `id`, `title`, `price`, `discount_price` FROM `books` ORDER BY `sort_order` ASC")->fetchAll();
        $directors = $this->db->query("SELECT `id`, `name`, `district_name` FROM `directors` WHERE `status` = 'active' ORDER BY `name` ASC")->fetchAll();

        return new Response(View::render('manager/distributions', [
            'title'         => 'বই ও কুরআন বিতরণ এবং কুরিয়ার চালান ট্র্যাকিং | কারিয়ানা কুরআন',
            'distributions' => $distributions,
            'books'         => $books,
            'directors'     => $directors,
        ], 'layouts/main'));
    }

    /**
     * Dispatch Books / Qurans to Director or Customer
     */
    public function saveDistribution(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $mgr = Session::get('manager');
        $recipientType = (string)$request->post('recipient_type', 'director');
        $directorId = (int)$request->post('director_id', 0) ?: null;
        $customerName = trim((string)$request->post('customer_name', ''));
        $customerPhone = trim((string)$request->post('customer_phone', ''));
        $bookId = (int)$request->post('book_id', 0);
        $quantity = (int)$request->post('quantity', 1);
        $unitPrice = (float)$request->post('unit_price', 0);
        $paymentStatus = (string)$request->post('payment_status', 'paid');
        $deliveryStatus = (string)$request->post('delivery_status', 'dispatched');
        $courierName = trim((string)$request->post('courier_name', 'সুন্দরবন কুরিয়ার'));
        $trackingNumber = trim((string)$request->post('tracking_number', ''));
        $notes = trim((string)$request->post('notes', ''));

        if ($bookId <= 0 || $quantity <= 0) {
            Session::flash('error', 'অনুগ্রহ করে বই এবং সঠিক সংখ্যা নির্বাচন করুন।');
            return Response::redirect('/manager/distributions');
        }

        // Fetch book unit price if 0
        if ($unitPrice <= 0) {
            $bkStmt = $this->db->prepare("SELECT `price`, `discount_price` FROM `books` WHERE `id` = :id LIMIT 1");
            $bkStmt->execute(['id' => $bookId]);
            $bk = $bkStmt->fetch();
            $unitPrice = (float)($bk['discount_price'] ?? $bk['price'] ?? 0);
        }

        $totalAmount = $unitPrice * $quantity;

        $stmt = $this->db->prepare("
            INSERT INTO `book_distributions` 
            (`manager_id`, `recipient_type`, `director_id`, `customer_name`, `customer_phone`, `book_id`, `quantity`, `unit_price`, `total_amount`, `payment_status`, `delivery_status`, `courier_name`, `tracking_number`, `notes`)
            VALUES (:mgr, :rtype, :dir, :cname, :cphone, :book, :qty, :uprice, :tot, :pay_st, :del_st, :courier, :track, :notes)
        ");
        $stmt->execute([
            'mgr'    => $mgr['id'] ?? null,
            'rtype'  => in_array($recipientType, ['director', 'teacher', 'general_customer'], true) ? $recipientType : 'director',
            'dir'    => $directorId,
            'cname'  => $customerName ?: null,
            'cphone' => $customerPhone ?: null,
            'book'   => $bookId,
            'qty'    => $quantity,
            'uprice' => $unitPrice,
            'tot'    => $totalAmount,
            'pay_st' => in_array($paymentStatus, ['paid', 'due', 'partial', 'waived'], true) ? $paymentStatus : 'paid',
            'del_st' => in_array($deliveryStatus, ['pending', 'dispatched', 'in_transit', 'delivered', 'returned'], true) ? $deliveryStatus : 'dispatched',
            'courier'=> $courierName,
            'track'  => $trackingNumber ?: null,
            'notes'  => $notes ?: null,
        ]);

        // If paid or partial, also optionally record in accounts ledger as income
        if ($paymentStatus === 'paid' && $totalAmount > 0) {
            $ledgerVch = 'VCH-DIST-' . date('Ymd-His');
            $lDesc = "বই বিতরণ বাবদ আদায়: {$quantity} কপি (কুরিয়ার: {$courierName})";
            if ($directorId) {
                $dName = $this->db->query("SELECT `name` FROM `directors` WHERE `id` = {$directorId}")->fetchColumn();
                $lDesc .= " - প্রাপক পরিচালক: {$dName}";
            }
            $insL = $this->db->prepare("
                INSERT INTO `accounts_ledger` (`manager_id`, `type`, `category`, `amount`, `transaction_date`, `description`, `voucher_no`, `related_director_id`)
                VALUES (:mgr, 'income', 'বই বিক্রি', :amount, CURDATE(), :desc, :vch, :dir)
            ");
            $insL->execute([
                'mgr'    => $mgr['id'] ?? null,
                'amount' => $totalAmount,
                'desc'   => $lDesc,
                'vch'    => $ledgerVch,
                'dir'    => $directorId,
            ]);
        }

        // If sent to a director, increment their total_books_ordered
        if ($directorId) {
            $this->db->exec("UPDATE `directors` SET `total_books_ordered` = `total_books_ordered` + {$quantity} WHERE `id` = {$directorId}");
        }

        Session::flash('success', "বই চালানের বুকিং সফল হয়েছে! মোট {$quantity} কপি নির্ধারিত কুরিয়ারে হস্তান্তরের জন্য প্রস্তুত।");
        return Response::redirect('/manager/distributions');
    }
}
