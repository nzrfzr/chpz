<?php
session_start();
require 'db_connection.php';
require 'midtrans_config.php';

// Check if user is logged in
if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'];
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($orderId <= 0) {
    die("ID Pesanan tidak valid.");
}

// Fetch order details
$orderQuery = "SELECT * FROM orders WHERE order_id = ? AND email = ?";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param('is', $orderId, $email);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Request Midtrans Snap Token
$url = MIDTRANS_API_URL;
$server_key = MIDTRANS_SERVER_KEY;
$auth_header = "Basic " . base64_encode($server_key . ":");

$payload = [
    'transaction_details' => [
        'order_id' => 'CHEAPIZA-' . $orderId . '-' . time(),
        'gross_amount' => (int)$order['grand_total']
    ],
    'customer_details' => [
        'first_name' => $order['firstName'],
        'last_name' => $order['lastName'],
        'email' => $order['email'],
        'phone' => $order['phone']
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: ' . $auth_header
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);

$snapToken = '';
$errorMessage = '';

if ($http_code === 201 || $http_code === 200) {
    $responseData = json_decode($response, true);
    $snapToken = $responseData['token'] ?? '';
} else {
    $errorMessage = "Gagal menghubungi Midtrans (HTTP $http_code).";
    if (!empty($curl_error)) {
        $errorMessage .= " Error: " . $curl_error;
    } else {
        $responseData = json_decode($response, true);
        if (isset($responseData['error_messages']) && is_array($responseData['error_messages'])) {
            $errorMessage .= " Detail: " . implode(', ', $responseData['error_messages']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses Pembayaran - Cheapiza</title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fef2e6 0%, #feead4 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(251, 74, 54, 0.1);
            max-width: 450px;
            width: 90%;
            border: 1px solid rgba(251, 74, 54, 0.15);
        }
        .logo {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fb4a36;
            margin-bottom: 20px;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(251, 74, 54, 0.1);
            border-top-color: #fb4a36;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 30px auto;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        h2 {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        p {
            font-size: 0.95rem;
            color: #7f8c8d;
            line-height: 1.6;
        }
        .btn-retry {
            display: inline-block;
            background-color: #fb4a36;
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(251, 74, 54, 0.2);
            transition: all 0.2s ease;
        }
        .btn-retry:hover {
            background-color: #e03b29;
            transform: translateY(-2px);
        }
        .error-icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">Cheapiza</div>
    
    <?php if (!empty($snapToken)): ?>
        <h2>Menghubungkan ke Pembayaran</h2>
        <div class="spinner"></div>
        <p>Mohon tunggu sebentar, kami sedang membuka gerbang pembayaran online Midtrans...</p>
    <?php else: ?>
        <i class="fa-solid fa-circle-xmark error-icon"></i>
        <h2>Gagal Memproses Pembayaran</h2>
        <p><?php echo htmlspecialchars($errorMessage); ?></p>
        <p>Silakan coba beberapa saat lagi atau hubungi administrator jika masalah berlanjut.</p>
        <a href="cart.php" class="btn-retry">Kembali ke Keranjang</a>
    <?php endif; ?>
</div>

<?php if (!empty($snapToken)): ?>
    <!-- Load Snap JS -->
    <script src="<?php echo MIDTRANS_SNAP_URL; ?>" data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Trigger Midtrans Snap payment modal immediately
            snap.pay('<?php echo $snapToken; ?>', {
                onSuccess: function(result) {
                    updatePayment('success');
                },
                onPending: function(result) {
                    updatePayment('pending');
                },
                onError: function(result) {
                    alert("Terjadi kesalahan pada pembayaran. Silakan coba lagi.");
                    window.location.href = "cart.php";
                },
                onClose: function() {
                    alert("Anda menutup jendela pembayaran sebelum menyelesaikan transaksi.");
                    window.location.href = "orders.php";
                }
            });
            
            function updatePayment(status) {
                // Send AJAX notification to update the order status
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_payment_midtrans.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (status === 'success') {
                        window.location.href = "order_confirm.php?order_id=<?php echo $orderId; ?>";
                    } else {
                        window.location.href = "orders.php";
                    }
                };
                xhr.send('order_id=<?php echo $orderId; ?>&status=' + status);
            }
        });
    </script>
<?php endif; ?>

</body>
</html>
