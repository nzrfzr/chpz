<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php'; // Make sure to include your database connection

$statusFilter = isset($_GET['statusFilter']) ? $_GET['statusFilter'] : '';
$searchOrderId = isset($_GET['searchOrderId']) ? $_GET['searchOrderId'] : '';

$query = "SELECT order_id, order_date, firstName, lastName, phone, grand_total, order_status, pmode, cancel_reason FROM orders";
$conditions = [];

if (!empty($statusFilter)) {
    $conditions[] = "order_status = '" . $conn->real_escape_string($statusFilter) . "'";
}

if (!empty($searchOrderId)) {
    $conditions[] = "order_id LIKE '%" . $conn->real_escape_string($searchOrderId) . "%'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$query .= " ORDER BY order_id DESC";

$result = $conn->query($query);

?>
<?php
include 'includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <!--poppins-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin_orders.css">
    <style>
  .content{
    margin-bottom: 40px;
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
        <h3>Selamat Datang Kembali!</h3>
        <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?></p>
      </div>
    </div>

    <!-- Navigation Items -->

        <ul>
            <li><a href="index.php" ><i class="fas fa-chart-line"></i> Ringkasan</a></li>
            <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Manajemen Menu</a></li>
            <li><a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
            <li><a href="payment_proofs.php"><i class="fas fa-receipt"></i> Bukti Transfer</a></li>
            <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservasi</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Pengguna</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Ulasan</a></li>
            <li><a href="staffs.php"><i class="fas fa-users"></i> Staf</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Pengaturan Profil</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="header">
            <button id="toggleSidebar" class="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h2><i class="fas fa-shopping-cart"></i> Pesanan</h2>
        </div>

        <div class="actions">
            <div>
            <button id="refreshButton" onclick="refreshPage()" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
           
            </div>
            
            <div class="filter-orders">
                <select id="statusFilter" name="statusFilter" onchange="filterByStatus()">
                    <option value="">Semua Pesanan</option>
                    <option value="Pending">Menunggu (Pending)</option>
                    <option value="Processing">Diproses (Processing)</option>
                    <option value="On the way">Sedang Dikirim (On the way)</option>
                    <option value="Completed">Selesai (Completed)</option>
                    <option value="Cancelled">Dibatalkan (Cancelled)</option>
                </select>
                <input type="text" id="searchOrderId" placeholder="Cari berdasarkan ID Pesanan" oninput="searchByOrderId()">
            </div>
        </div>
        <?php
        // Display orders in a table
        echo "<table>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Kontak</th>
                    <th>Total</th>
                    <th>Status Pesanan</th>
                    <th>Metode Pembayaran</th>
                    <th>Alasan Pembatalan</th>
                    <th>Aksi</th>
                </tr>";
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $statusClass = '';
                $statusText = $row['order_status'];
                switch ($row['order_status']) {
                    case 'Pending':
                        $statusClass = 'status-pending';
                        $statusText = 'Menunggu';
                        break;
                    case 'Processing':
                        $statusClass = 'status-processing';
                        $statusText = 'Diproses';
                        break;
                    case 'Completed':
                        $statusClass = 'status-completed';
                        $statusText = 'Selesai';
                        break;
                    case 'Cancelled':
                        $statusClass = 'status-cancelled';
                        $statusText = 'Dibatalkan';
                        break;
                    case 'On the way':
                        $statusClass = 'status-ontheway';
                        $statusText = 'Sedang Dikirim';
                        break;
                }

                $pmode_display = $row['pmode'];
                if ($pmode_display === 'Card') {
                    $pmode_display = 'Transfer (QRIS)';
                } elseif ($pmode_display === 'Takeaway') {
                    $pmode_display = 'Ambil di tempat';
                } elseif ($pmode_display === 'Cash') {
                    $pmode_display = 'Tunai (Cash)';
                }

                echo "<tr>
                    <td>" . $row['order_id'] . "</td>
                    <td>" . $row['firstName'] . " " . $row['lastName'] . "</td>
                    <td>" . $row['phone'] . "</td>
                    <td>" . 'Rp ' . number_format($row['grand_total']) . "</td>
                    <td><span class='status $statusClass'>" . $statusText . "</span></td>
                    <td>" . $pmode_display . "</td>
                    <td>" . ($row['order_status'] == 'Cancelled' ? $row['cancel_reason'] : '-') . "</td>
                    <td><button id='viewbtn' onclick=\"viewDetails(" . $row['order_id'] . ")\">Lihat Detail</button></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8' style='text-align: center;'>Pesanan Tidak Ditemukan</td></tr>";
        }

        echo "</table>";

        $conn->close();
        ?>
    </div>

    <?php
    include_once ('includes/footer.html');
    ?>
    <script src="js/sidebar.js"></script>
    <script>
                function viewDetails(orderId) {
            window.location.href = 'view_order.php?orderId=' + orderId;
        }
    const modal = document.querySelector('.modal');
    const buttons = document.querySelectorAll('.toggle-button');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.toggle('open');
        });
    });

    function filterByStatus() {
        var statusFilter = document.getElementById('statusFilter').value;
        var dateFilter = document.getElementById('dateFilter') ? document.getElementById('dateFilter').value : ''; // Optional date filter
        var searchOrderId = document.getElementById('searchOrderId').value.trim();
        window.location.href = 'admin_orders.php?statusFilter=' + encodeURIComponent(statusFilter) + '&dateFilter=' + encodeURIComponent(dateFilter) + '&searchOrderId=' + encodeURIComponent(searchOrderId);
    }

    function searchByOrderId() {
        filterByStatus(); // Call filterByStatus to update results based on search input
    }

    function refreshPage() {
        window.location.href = 'admin_orders.php'; // Reload the page
    }

    // Set the status filter select value based on the query parameter
    document.getElementById('statusFilter').value = "<?= isset($_GET['statusFilter']) ? $_GET['statusFilter'] : '' ?>";

    // Optional: Set the date filter value if you have a date filter
    if (document.getElementById('dateFilter')) {
        document.getElementById('dateFilter').value = "<?= isset($_GET['dateFilter']) ? $_GET['dateFilter'] : '' ?>";
    }

    // Set the search input value based on the query parameter
    document.getElementById('searchOrderId').value = "<?= isset($_GET['searchOrderId']) ? $_GET['searchOrderId'] : '' ?>";

    // Attach event listeners to filters
    document.getElementById('statusFilter').addEventListener('change', filterByStatus);
    if (document.getElementById('dateFilter')) {
        document.getElementById('dateFilter').addEventListener('change', filterByStatus);
    }
    document.getElementById('searchOrderId').addEventListener('input', searchByOrderId);
    document.getElementById('refreshButton').addEventListener('click', refreshPage);
</script>



</body>

</html>