<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['adminloggedin']) || !$_SESSION['adminloggedin']) {
  header('Location: login.php');
  exit;
}

// Get the logged-in admin's email from the session
$admin_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (empty($admin_email)) {
  die("Email Admin tidak ditemukan di session.");
}

// Database connection
include 'db_connection.php';

// Function to retrieve admin info for sidebar
function getAdminInfoForSidebar($email)
{
  global $conn;
  $stmt = $conn->prepare("SELECT firstName, lastName, email, profile_image FROM staff WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($firstName, $lastName, $email, $profile_image);
  $stmt->fetch();
  $stmt->close();
  return [
    'firstName' => $firstName ?: '',
    'lastName' => $lastName ?: '',
    'email' => $email ?: '',
    'profile_image' => $profile_image ?: 'default.jpg'
  ];
}

$admin_info = getAdminInfoForSidebar($admin_email);

// Handle form submission
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $merchant_id = $_POST['midtrans_merchant_id'] ?? '';
  $client_key = $_POST['midtrans_client_key'] ?? '';
  $server_key = $_POST['midtrans_server_key'] ?? '';
  $environment = $_POST['midtrans_environment'] ?? 'sandbox';

  $keys = [
    'midtrans_merchant_id' => $merchant_id,
    'midtrans_client_key' => $client_key,
    'midtrans_server_key' => $server_key,
    'midtrans_environment' => $environment
  ];

  foreach ($keys as $key_name => $key_val) {
    $stmt = $conn->prepare("UPDATE settings SET key_value = ? WHERE key_name = ?");
    $stmt->bind_param("ss", $key_val, $key_name);
    $stmt->execute();
    $stmt->close();
  }
  
  $success_message = 'Pengaturan Midtrans berhasil diperbarui!';
}

// Fetch current Midtrans credentials from DB
$settings = [];
$result = $conn->query("SELECT key_name, key_value FROM settings WHERE key_name IN ('midtrans_merchant_id', 'midtrans_client_key', 'midtrans_server_key', 'midtrans_environment')");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['key_value'];
  }
}

$current_merchant_id = $settings['midtrans_merchant_id'] ?? '';
$current_client_key = $settings['midtrans_client_key'] ?? '';
$current_server_key = $settings['midtrans_server_key'] ?? '';
$current_environment = $settings['midtrans_environment'] ?? 'sandbox';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengaturan Midtrans</title>
  <!-- poppins font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/profile.css">
  <style>
    .form-group label {
      position: static;
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #333;
      text-align: left;
    }
    .form-group input[type="text"],
    .form-group select {
      text-align: left;
      font-family: monospace;
      padding: 12px 15px;
      width: 100%;
      border: 1px solid rgba(253, 108, 77, 0.8);
      background-color: rgba(253, 108, 77, 0.2);
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 15px;
    }
    .form-group select {
      font-family: 'Poppins', sans-serif;
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: url("data:image/svg+xml;utf8,<svg fill='black' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
      background-repeat: no-repeat;
      background-position: right 10px center;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      padding: 12px 20px;
      border-radius: 4px;
      margin-bottom: 20px;
      text-align: center;
      font-weight: 500;
    }
  </style>
</head>

<body>

  <div class="sidebar">
    <button class="close-sidebar" id="closeSidebar">&times;</button>

    <!-- Profile Section -->
    <div class="profile-section">
      <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile Picture">
      <div class="info">
        <h3>Selamat datang kembali!</h3>
        <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?></p>
      </div>
    </div>

    <!-- Navigation Items -->
    <ul>
      <li><a href="index.php"><i class="fas fa-chart-line"></i> Ringkasan</a></li>
      <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Manajemen Menu</a></li>
      <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
      <li><a href="payment_proofs.php"><i class="fas fa-receipt"></i> Bukti Transfer</a></li>
      <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservasi</a></li>
      <li><a href="users.php"><i class="fas fa-users"></i> Pengguna</a></li>
      <li><a href="reviews.php"><i class="fas fa-star"></i> Ulasan</a></li>
      <li><a href="staffs.php"><i class="fas fa-users"></i> Staf</a></li>
      <li><a href="profile.php"><i class="fas fa-user"></i> Pengaturan Profil</a></li>
      <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Pengaturan Midtrans</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
    </ul>
  </div>
  
  <div class="content">
    <div class="header">
      <button id="toggleSidebar" class="toggle-button">
        <i class="fas fa-bars"></i>
      </button>
      <h2><i class="fas fa-cog"></i> Pengaturan Midtrans</h2>
    </div>
    
    <div class="wrapper">
      <div class="container" style="width: 650px;">
        <h3 style="text-align: center; margin-bottom: 20px; color: #fb4a36;"><i class="fas fa-key"></i> Kunci Integrasi Midtrans</h3>
        <p style="font-size: 0.9rem; color: #666; margin-bottom: 25px; text-align: center; line-height: 1.5;">
          Masukkan kredensial Midtrans di bawah ini. Pengaturan ini disimpan secara aman di database lokal Anda dan tidak akan terunggah ke Git.
        </p>

        <?php if (!empty($success_message)): ?>
          <div class="alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
          </div>
        <?php endif; ?>

        <form action="settings.php" method="post">
          <div class="form-group" style="margin-bottom: 20px;">
            <label for="midtrans_environment">Environment Mode:</label>
            <select id="midtrans_environment" name="midtrans_environment">
              <option value="sandbox" <?php if ($current_environment === 'sandbox') echo 'selected'; ?>>Sandbox (Testing)</option>
              <option value="production" <?php if ($current_environment === 'production') echo 'selected'; ?>>Production (Live)</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 20px;">
            <label for="midtrans_merchant_id">Merchant ID:</label>
            <input type="text" id="midtrans_merchant_id" name="midtrans_merchant_id" value="<?php echo htmlspecialchars($current_merchant_id); ?>" required placeholder="Masukkan Merchant ID (Contoh: M062856260)">
          </div>

          <div class="form-group" style="margin-bottom: 20px;">
            <label for="midtrans_client_key">Client Key:</label>
            <input type="text" id="midtrans_client_key" name="midtrans_client_key" value="<?php echo htmlspecialchars($current_client_key); ?>" required placeholder="Masukkan Client Key (Contoh: Mid-client-...)">
          </div>

          <div class="form-group" style="margin-bottom: 25px;">
            <label for="midtrans_server_key">Server Key:</label>
            <input type="text" id="midtrans_server_key" name="midtrans_server_key" value="<?php echo htmlspecialchars($current_server_key); ?>" required placeholder="Masukkan Server Key (Contoh: Mid-server-...)">
          </div>

          <button type="submit"><i class="fas fa-save"></i> Simpan Pengaturan</button>
        </form>
      </div>
    </div>
  </div>

  <?php include_once ('includes/footer.html'); ?>
  <script src="js/sidebar.js"></script>
</body>

</html>

<?php $conn->close(); ?>
