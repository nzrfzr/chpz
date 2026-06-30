<?php
require 'db_connection.php';
require 'midtrans_config.php';

// Retrieve HTTP POST request body
$jsonStr = file_get_contents('php://input');
$data = json_decode($jsonStr, true);

if (!$data) {
    http_response_code(400);
    echo "Empty or invalid notification data.";
    exit;
}

// Extract variables
$order_id = $data['order_id'] ?? '';
$status_code = $data['status_code'] ?? '';
$gross_amount = $data['gross_amount'] ?? '';
$signature_key = $data['signature_key'] ?? '';
$transaction_status = $data['transaction_status'] ?? '';
$fraud_status = $data['fraud_status'] ?? '';

// Validate signature key to prevent security fraud
$server_key = MIDTRANS_SERVER_KEY;
$local_signature = hash("sha512", $order_id . $status_code . $gross_amount . $server_key);

if ($local_signature !== $signature_key) {
    http_response_code(403);
    echo "Invalid signature key.";
    exit;
}

// Extract the local database Order ID from the Midtrans order_id string
// Format: CHEAPIZA-{orderId}-{timestamp}
$parts = explode('-', $order_id);
if (count($parts) >= 2 && $parts[0] === 'CHEAPIZA') {
    $dbOrderId = intval($parts[1]);
} else {
    $dbOrderId = intval($order_id);
}

if ($dbOrderId <= 0) {
    http_response_code(400);
    echo "Invalid order ID.";
    exit;
}

// Map transaction status to database states
$paymentStatus = 'Pending';
$orderStatus = 'Pending';

if ($transaction_status == 'capture') {
    if ($fraud_status == 'accept') {
        $paymentStatus = 'Successful';
        $orderStatus = 'Processing';
    } else {
        $paymentStatus = 'Pending';
        $orderStatus = 'Pending';
    }
} elseif ($transaction_status == 'settlement') {
    $paymentStatus = 'Successful';
    $orderStatus = 'Processing';
} elseif ($transaction_status == 'pending') {
    $paymentStatus = 'Pending';
    $orderStatus = 'Pending';
} elseif (in_array($transaction_status, ['deny', 'cancel', 'expire'])) {
    $paymentStatus = 'Rejected';
    $orderStatus = 'Cancelled';
}

// Update status in the database
$updateQuery = "UPDATE orders SET payment_status = ?, order_status = ? WHERE order_id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param('ssi', $paymentStatus, $orderStatus, $dbOrderId);
if ($stmt->execute()) {
    echo "OK";
} else {
    http_response_code(500);
    echo "Failed to update database status.";
}
$stmt->close();
$conn->close();
?>
