<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name     = trim($data['name']     ?? '');
$address  = trim($data['address']  ?? '');
$whatsapp = trim($data['whatsapp'] ?? '');
$notes    = trim($data['notes']    ?? '');
$items    = $data['items']         ?? [];

if (!$name || !$address || !$whatsapp || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate & calculate total
$total = 0;
foreach ($items as $item) {
    $total += floatval($item['price']) * intval($item['qty']);
}

$orderCode = 'FH-' . strtoupper(substr(uniqid(), -6)) . '-' . date('ymd');

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO orders (order_code, customer_name, customer_address, customer_whatsapp, notes, total_amount)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$orderCode, $name, $address, $whatsapp, $notes, $total]);
    $orderId = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $qty      = intval($item['qty']);
        $price    = floatval($item['price']);
        $subtotal = $price * $qty;
        $itemStmt->execute([
            $orderId,
            intval($item['id']),
            $item['name'],
            $price,
            $qty,
            $subtotal
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success'    => true,
        'order_code' => $orderCode,
        'message'    => 'Order placed successfully'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()]);
}
