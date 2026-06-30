<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

$email = $_SESSION['email'];
$orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($orderId <= 0 || empty($status)) {
    http_response_code(400);
    echo "Bad request.";
    exit;
}

// Ensure the order belongs to this customer
$checkQuery = "SELECT order_id FROM orders WHERE order_id = ? AND email = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param('is', $orderId, $email);
$stmt->execute();
$result = $stmt->get_result();
$orderExists = $result->num_rows > 0;
$stmt->close();

if (!$orderExists) {
    http_response_code(404);
    echo "Order not found.";
    exit;
}

// Update payment and order status
if ($status === 'success') {
    $paymentStatus = 'Successful';
    $orderStatus = 'Processing';
} elseif ($status === 'pending') {
    $paymentStatus = 'Pending';
    $orderStatus = 'Pending';
} else {
    $paymentStatus = 'Pending';
    $orderStatus = 'Pending';
}

$updateQuery = "UPDATE orders SET payment_status = ?, order_status = ? WHERE order_id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param('ssi', $paymentStatus, $orderStatus, $orderId);
if ($stmt->execute()) {
    echo "Success";
} else {
    http_response_code(500);
    echo "Error updating status: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
