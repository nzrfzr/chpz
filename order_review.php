<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
  header('location:login.php');
  exit;
}

// Get the email from the session
$email = $_SESSION['email'];

// Fetch user data
$stmt = $conn->prepare('SELECT * FROM users WHERE email=?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Retrieve selected items from POST request
$selectedItems = json_decode($_POST['selected_items'], true);

// Fetch cart items from the database
$itemDetails = [];
foreach ($selectedItems as $item) {
  $stmt = $conn->prepare('SELECT * FROM cart WHERE id=? AND email=?');
  $stmt->bind_param('is', $item['id'], $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $itemDetails[] = $result->fetch_assoc();
}

// Calculate subtotal and total
$subtotal = 0;
$deliveryFee = 0;
foreach ($itemDetails as $item) {
  $itemPrice = $item['price'];
  $itemQuantity = $item['quantity'];
  $subtotal += $itemPrice * $itemQuantity;
}
// Fetch shipping fee from database settings
$shippingFee = 1000; // fallback default
$shippingQuery = $conn->query("SELECT key_value FROM settings WHERE key_name = 'shipping_fee'");
if ($shippingQuery && $row = $shippingQuery->fetch_assoc()) {
    $shippingFee = intval($row['key_value']);
}

$deliveryFee = ($_POST['payment_mode'] === 'Takeaway') ? 0 : $shippingFee;
$total = $subtotal + $deliveryFee;


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="css/order_review.css">
  <title>Selesaikan Pesanan</title>
</head>

<body>
  <?php include('includes/nav-logged.php'); ?>
  <div class="title mt-2">
    <h3>Halo <?php echo $user['firstName'] . " " . $user['lastName']; ?>, selesaikan pesanan Anda!</h3>
  </div>
  <div class=" main mt-4">
    <div class="order-fee">

      <h4>Detail Pesanan</h4>
      <hr>
      <form action="process_order.php" method="post" enctype="multipart/form-data" id="order-form">
        <input type="hidden" name="total" value="<?= $total ?>">
        <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
        <input type="hidden" name="order_id" value="<?= $orderId ?>">
        <input type="hidden" name="selected_items" value='<?= json_encode($selectedItems) ?>'>
        <input type="hidden" name="payment_mode" value="<?= htmlspecialchars($_POST['payment_mode']) ?>">
        <div class="form-group row">
          <div class="col">
            <label for="firstName">Nama Depan:</label>
            <input type="text" class="form-control" id="firstName" name="firstName" required>
          </div>
          <div class="col">
            <label for="lastName">Nama Belakang:</label>
            <input type="text" class="form-control" id="lastName" name="lastName" required>
          </div>
        </div>
        <div class="form-group row">
          <div class="col">
            <label for="contact">Kontak/Telepon:</label>
            <input type="text" class="form-control" id="contact" name="contact" maxlength="16" required>
          </div>
          <div class="col">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="order_note">Catatan Pesanan:</label>
          <textarea class="form-control" id="order_note" name="order_note" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label for="address">Alamat:</label>
          <p class="text-muted small mb-3" style="color: red;">*Untuk saat ini kami hanya melayani pengantaran di wilayah Desa Aik Dareq, Kec. Batukliang, Lombok Tengah</p>
          <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
        </div>



    </div>


    <div class="order-summary">
      <h4>Ringkasan Pesanan</h4>
      <hr>
      <div class="order-items mb-2">
        <?php foreach ($itemDetails as $item) : ?>
          <div class="order-item d-flex align-items-center">
            <?php if (!empty($item['image'])) : ?>
              <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="Item Image" class="ms-1">
            <?php else : ?>
              <span>Gambar tidak tersedia</span>
            <?php endif; ?>
            <div class="ms-1 row d-flex justify-content-between w-100">
              <div class="col d-flex flex-column justify-content-center ">
                <div class="d-flex flex-row mb-1"><strong><?= htmlspecialchars($item['itemName']) ?></strong></div>
                <div class="d-flex flex-row ">Jumlah: <?= htmlspecialchars($item['quantity']) ?></div>
              </div>
              <div class="col d-flex flex-column justify-content-center">
                <div class="d-flex flex-row justify-content-end align-items-center mt-2"> Rp <?= htmlspecialchars($item['price'], 0) ?> x <?= htmlspecialchars($item['quantity']) ?></div>
                <div class="d-flex flex-row justify-content-end align-items-start mb-2">
                  <span class="badge rounded-pill text-light p-2 mt-2 item-total-price" style="background-color: #fb4a36;">Rp <?= $item['total_price'] ?></span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <h4 class="mt-1 ">Biaya Pesanan</h4>
      <hr>
      <div class="summary-details">
        <div class="fee-details">
          <div><strong>Subtotal:</strong></div>
          <div>Rp <?= number_format($subtotal) ?></div>
        </div>
        <div class="fee-details">
          <div><strong>Metode Pembayaran:</strong></div>
          <div><?php 
            $pmode = $_POST['payment_mode'] ?? '';
            if ($pmode === 'Takeaway') echo 'Ambil di tempat';
            elseif ($pmode === 'Cash') echo 'Tunai (Cash)';
            elseif ($pmode === 'Midtrans') echo 'Pembayaran Online (Midtrans)';
            else echo htmlspecialchars($pmode);
          ?></div>
        </div>
        <div class="fee-details">
          <div><strong>Biaya Pengiriman:</strong></div>
          <div>Rp <?= number_format($deliveryFee) ?></div>
        </div>
        <div class="fee-details">
          <div><strong>Total:</strong></div>
          <div>Rp <?= number_format($total) ?></div>
        </div>
      </div>
      <hr>
      <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $payment_mode = $_POST['payment_mode'] ?? '';

        if ($payment_mode == 'Midtrans') {
          echo '<button type="submit" class="order-btn" style="background-color: #007bff; border-color: #007bff;">Bayar Sekarang</button>';
        } else {
          echo '<button type="submit" class="order-btn ">Buat Pesanan</button>';
        }
      }
      ?>

      </form>
    </div>


  </div>

  <?php
include_once ('includes/footer.html');
?>

  <!-- Bootstrap JS -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js'></script>
  

  <script>
    $(document).ready(function() {
      console.log('Page is ready. Calling load_cart_item_number.');
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