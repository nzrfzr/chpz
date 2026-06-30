<?php
// Ensure database connection is available
if (!isset($conn)) {
    if (file_exists('db_connection.php')) {
        require_once 'db_connection.php';
    } elseif (file_exists('../db_connection.php')) {
        require_once '../db_connection.php';
    }
}

// Fetch Midtrans credentials dynamically from database settings table
$midtrans_settings = [];
if (isset($conn)) {
    $result = $conn->query("SELECT key_name, key_value FROM settings WHERE key_name IN ('midtrans_merchant_id', 'midtrans_client_key', 'midtrans_server_key')");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $midtrans_settings[$row['key_name']] = $row['key_value'];
        }
    }
}

define('MIDTRANS_MERCHANT_ID', $midtrans_settings['midtrans_merchant_id'] ?? '');
define('MIDTRANS_CLIENT_KEY', $midtrans_settings['midtrans_client_key'] ?? '');
define('MIDTRANS_SERVER_KEY', $midtrans_settings['midtrans_server_key'] ?? '');

// Automatically detect environment based on Server Key prefix
$is_production = (strpos(MIDTRANS_SERVER_KEY, 'SB-Mid-server-') === false && strpos(MIDTRANS_SERVER_KEY, 'Mid-server-') !== false);

if ($is_production) {
    define('MIDTRANS_IS_PRODUCTION', true);
    define('MIDTRANS_SNAP_URL', 'https://app.midtrans.com/snap/snap.js');
    define('MIDTRANS_API_URL', 'https://app.midtrans.com/snap/v1/transactions');
} else {
    define('MIDTRANS_IS_PRODUCTION', false);
    define('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/snap.js');
    define('MIDTRANS_API_URL', 'https://app.sandbox.midtrans.com/snap/v1/transactions');
}
?>
