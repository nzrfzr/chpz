<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php';

// Fetch payment proofs with order details
$query = "SELECT bp.id, bp.order_id, bp.email, bp.image, bp.uploaded_at,
                 o.firstName, o.lastName, o.phone, o.address, o.grand_total, o.payment_status
          FROM bukti_pembayaran bp
          JOIN orders o ON bp.order_id = o.order_id
          ORDER BY bp.uploaded_at DESC";

$result = $conn->query($query);
?>
<?php
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bukti Transfer QRIS</title>
    <!--poppins-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="admin_orders.css">
    <style>
        .content {
            margin-bottom: 40px;
        }
        .proof-img {
            max-width: 80px;
            height: auto;
            border-radius: 4px;
            border: 1px solid #ffc9b3;
            cursor: zoom-in;
            transition: transform 0.2s;
        }
        .proof-img:hover {
            transform: scale(1.1);
        }
        .btn-confirm {
            background-color: #27ae60;
            border-color: #27ae60;
            padding: 5px 10px;
            font-size: 0.9rem;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn-confirm:hover {
            background-color: #219653;
            border-color: #219653;
            color: white;
        }
        .btn-reject {
            background-color: #e74c3c;
            border-color: #e74c3c;
            padding: 5px 10px;
            font-size: 0.9rem;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-reject:hover {
            background-color: #c0392b;
            border-color: #c0392b;
            color: white;
        }
        /* Modal Style for image zoom */
        .zoom-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 50px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.85);
        }
        .zoom-content {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
        }
        .zoom-close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }
        .zoom-close:hover,
        .zoom-close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            color: white;
            display: inline-block;
        }
        .badge-pending {
            background-color: #f39c12;
        }
        .badge-success {
            background-color: #27ae60;
        }
        .badge-rejected {
            background-color: #e74c3c;
        }
    </style>
</head>

<body>
    <!-- sidebar -->
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
            <li><a href="payment_proofs.php" class="active"><i class="fas fa-receipt"></i> Bukti Transfer</a></li>
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
            <h2><i class="fas fa-receipt"></i> Bukti Pembayaran QRIS</h2>
        </div>

        <div class="actions" style="margin-top: 20px;">
            <button id="refreshButton" onclick="window.location.reload();" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>

        <?php
        echo "<table>
                <tr>
                    <th>Order ID</th>
                    <th>Nama Pemesan</th>
                    <th>Kontak / Email</th>
                    <th>Alamat</th>
                    <th>Total Bayar</th>
                    <th>Bukti Unggah</th>
                    <th>Tanggal Unggah</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi Konfirmasi</th>
                </tr>";
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $statusBadge = '';
                switch ($row['payment_status']) {
                    case 'Pending':
                        $statusBadge = "<span class='badge badge-pending'>Pending</span>";
                        break;
                    case 'Successful':
                        $statusBadge = "<span class='badge badge-success'>Successful</span>";
                        break;
                    case 'Rejected':
                        $statusBadge = "<span class='badge badge-rejected'>Rejected</span>";
                        break;
                }

                $actions = "-";
                if ($row['payment_status'] === 'Pending') {
                    $actions = "<button class='btn-confirm' onclick='updateStatus(" . $row['order_id'] . ", \"Successful\")'><i class='fas fa-check'></i> Konfirmasi</button>" .
                               "<button class='btn-reject' onclick='updateStatus(" . $row['order_id'] . ", \"Rejected\")'><i class='fas fa-times'></i> Tolak</button>";
                }

                echo "<tr>
                    <td><a href='view_order.php?orderId=" . $row['order_id'] . "' style='color: #fb4a36; font-weight: 600; text-decoration: none;'>#" . $row['order_id'] . "</a></td>
                    <td>" . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . "</td>
                    <td>" . htmlspecialchars($row['phone']) . "<br><small class='text-muted'>" . htmlspecialchars($row['email']) . "</small></td>
                    <td style='max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;'>" . htmlspecialchars($row['address']) . "</td>
                    <td><strong>Rp " . number_format($row['grand_total']) . "</strong></td>
                    <td>
                        <img src='../uploads/bukti_transfer/" . htmlspecialchars($row['image']) . "' class='proof-img' onclick='zoomImage(this.src)' alt='Bukti Transfer'>
                    </td>
                    <td>" . date('d M Y H:i', strtotime($row['uploaded_at'])) . "</td>
                    <td>" . $statusBadge . "</td>
                    <td>" . $actions . "</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='9' style='text-align: center;'>Belum ada bukti pembayaran yang diunggah.</td></tr>";
        }
        echo "</table>";

        $conn->close();
        ?>
    </div>

    <!-- Image Zoom Modal -->
    <div id="imageZoomModal" class="zoom-modal" onclick="closeZoom()">
        <span class="zoom-close" onclick="closeZoom()">&times;</span>
        <img class="zoom-content" id="imgZoomTarget" alt="Zoomed Proof">
    </div>

    <?php
    include_once ('footer.html');
    ?>
    <script src="sidebar.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function zoomImage(src) {
            var modal = document.getElementById("imageZoomModal");
            var modalImg = document.getElementById("imgZoomTarget");
            modal.style.display = "block";
            modalImg.src = src;
        }

        function closeZoom() {
            var modal = document.getElementById("imageZoomModal");
            modal.style.display = "none";
        }

        function updateStatus(orderId, status) {
            var actionText = status === 'Successful' ? 'MENGONFIRMASI' : 'MENOLAK';
            if (confirm('Apakah Anda yakin ingin ' + actionText + ' bukti pembayaran untuk pesanan #' + orderId + '?')) {
                $.ajax({
                    url: 'update_payment_status.php',
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        payment_status: status
                    },
                    success: function(response) {
                        if (response.trim() === 'Success') {
                            alert('Status pembayaran pesanan #' + orderId + ' berhasil diperbarui!');
                            window.location.reload();
                        } else {
                            alert('Gagal memperbarui status: ' + response);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan koneksi.');
                    }
                });
            }
        }
    </script>
</body>

</html>
