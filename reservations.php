<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "cheapiza";
$password = "cheapz2026";
$dbname = "cheapiza_fastfood";

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establishing connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
date_default_timezone_set('Asia/Colombo');

// Prefill data if logged in
$prefilledName = "";
$prefilledEmail = "";
$prefilledContact = "";

if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin'] === true) {
    $user_email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT firstName, lastName, email, contact FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName, $email, $contact);
    if ($stmt->fetch()) {
        $prefilledName = trim($firstName . ' ' . $lastName);
        $prefilledEmail = $email;
        $prefilledContact = $contact;
    }
    $stmt->close();
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data
    $email = $_POST['email'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $noOfGuests = intval($_POST['noOfGuests']);
    $reservedTime = $_POST['reservedTime']; // Input format is 'HH:MM'
    $reservedDate = $_POST['reservedDate']; // Input format is 'YYYY-MM-DD'

    // Process reservedTime to ensure it includes seconds
    $reservedTimeWithSeconds = date('H:i:s', strtotime($reservedTime));
    
    // Prepare SQL statement to insert data into reservations table
    $sql = "INSERT INTO reservations (email, name, contact, noOfGuests, reservedTime, reservedDate) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssiis", $email, $name, $contact, $noOfGuests, $reservedTimeWithSeconds, $reservedDate);

    // Execute the statement
    if ($stmt->execute()) {
        echo '<script>alert("Reservasi meja berhasil! Silakan tunggu konfirmasi dari kami."); window.location.href="index.php";</script>';
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservasi Meja - Cheapiza</title>
  <!-- Google Fonts & FontAwesome -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="css/reservations.css">
</head>

<body>
  <?php
  if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin'] === true) {
    include 'includes/nav-logged.php';
  } else {
    include 'includes/navbar.php';
  }
  ?>

  <div class="wrapper">
    <div class="form-container">
      <h2>Reservasi Meja</h2>
      <p class="sub-title">Nikmati hidangan spesial di <span>Cheapiza</span> bersama orang terdekat Anda.</p>
      
      <form action="reservations.php" method="POST">
        <div class="form-row">
          <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($prefilledName); ?>" placeholder="Masukkan nama lengkap Anda" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($prefilledEmail); ?>" placeholder="nama@email.com" required>
          </div>
          <div class="form-group">
            <label for="contact">No. Telepon / WhatsApp</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($prefilledContact); ?>" placeholder="Contoh: 0878..." required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="noOfGuests">Jumlah Tamu</label>
            <select id="noOfGuests" name="noOfGuests" required>
              <option value="1">1 Orang</option>
              <option value="2" selected>2 Orang</option>
              <option value="3">3 Orang</option>
              <option value="4">4 Orang</option>
              <option value="5">5 Orang</option>
              <option value="6">6 Orang</option>
              <option value="7">7-10 Orang</option>
              <option value="12">Lebih dari 10 Orang (Acara/Grup)</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="reservedDate">Tanggal Reservasi</label>
            <input type="date" id="reservedDate" name="reservedDate" min="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="form-group">
            <label for="reservedTime">Waktu Kedatangan</label>
            <input type="time" id="reservedTime" name="reservedTime" required>
          </div>
        </div>

        <button type="submit">Buat Reservasi Sekarang</button>
      </form>
    </div>
  </div>

  <?php
  include_once('includes/footer.html');
  ?>

  <!-- Bootstrap & jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    $(document).ready(function() {
      load_cart_item_number();

      function load_cart_item_number() {
        $.ajax({
          url: 'action.php',
          method: 'get',
          data: {
            cartItem: "cart_item"
          },
          success: function(response) {
            $("#cart-item").html(response);
          }
        });
      }
    });
  </script>
</body>

</html>
<?php $conn->close(); ?>
