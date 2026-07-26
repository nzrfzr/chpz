<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
  header("Location: ../login.php");
  exit();
}
include 'db_connection.php';
// Initialize search variable
$search = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
  $search = $conn->real_escape_string($_POST['search']);
}

?>
<?php
include 'includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Pengguna</title>
  <!-- Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
 
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/admin_user.css">
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
      <li><a href="index.php"><i class="fas fa-chart-line"></i>Ringkasan</a></li>
      <li><a href="admin_menu.php" ><i class="fas fa-utensils"></i>Manajemen Menu</a></li>
      <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i>Pesanan</a></li>
      <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i>Reservasi</a></li>
      <li><a href="users.php" class="active"><i class="fas fa-users"></i>Pengguna</a></li>
      <li><a href="reviews.php"><i class="fas fa-star"></i>Ulasan</a></li>
      <li><a href="staffs.php" ><i class="fas fa-users"></i>Staf</a></li>
      <li><a href="profile.php"><i class="fas fa-user"></i>Pengaturan Profil</a></li>
      <li><a href="settings.php"><i class="fas fa-cog"></i>Pengaturan Toko</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Keluar</a></li>
    </ul>
  </div>
  <div class="content">
    <div class="header">
      <button id="toggleSidebar" class="toggle-button">
        <i class="fas fa-bars"></i>
      </button>
      <h2><i class="fas fa-users"></i>&nbsp;Daftar Pengguna</h2>
    </div>



    <div class="actions">
      <button onclick="openaddUserModal()"><i class="fas fa-user-plus"></i> &nbsp; Tambah Pengguna</button>
      <form method="POST" id="searchForm" class="search-bar">
        <input type="text" name="search" placeholder="Cari..." value="<?php echo htmlspecialchars($search); ?>">
      </form>
      

    </div>

    <table id="userTable">
      <thead>
        <tr>
          <th>NO</th>
          <th>Tanggal Dibuat</th>
          <th>Email</th>
          <th>Nama Depan</th>
          <th>Nama Belakang</th>
          <th>Kontak</th>
          <th>Kata Sandi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Modify the SQL query to include search functionality
        $sql = "SELECT * FROM users";
        if (!empty($search)) {
          $sql .= " WHERE email LIKE '%$search%' OR firstName LIKE '%$search%' OR lastName LIKE '%$search%'";
        }
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          $counter = 1;
          while ($row = $result->fetch_assoc()) {
            $passwordMasked = str_repeat('*', strlen($row['password']));
            echo "<tr>
                      <td>{$counter}</td>
                      <td>{$row['dateCreated']}</td>
                      <td>{$row['email']}</td>
                      <td>{$row['firstName']}</td>
                      <td>{$row['lastName']}</td>
                      <td>{$row['contact']}</td>
                      <td>
                          <span class='password-masked'>{$passwordMasked}</span>
                          <span class='password-visible' style='display: none;'>{$row['password']}</span>
                          <i class='fas fa-eye-slash toggle-password' onclick='togglePassword(this)'></i>
                      </td>
                      <td>
                          <button id='editbtn' onclick='openEditUserModal(this)' data-email='{$row['email']}' data-firstname='{$row['firstName']}' data-lastname='{$row['lastName']}' data-contact='{$row['contact']}' data-password='{$row['password']}'><i class='fas fa-edit'></i></button>
                          <button id='deletebtn' onclick=\"deleteItem('{$row['email']}')\"><i class='fas fa-trash'></i></button>
                      </td>
                  </tr>";
            $counter++;
          }
        } else {
          echo "<tr><td colspan='8' style='text-align: center;'>Pengguna Tidak Ditemukan</td></tr>";
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </div>


  <!-- Modal for adding users -->
  <div id="addUserModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-container">
      <form id="addUserForm" method="POST" action="add_user.php">
        <div class="modal-header">
          <h2>Tambah Pengguna</h2>
          <span class="close-icon" onclick="closeaddUserModal()">&times;</span>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="email" name="email" id="email" class="input" required>
            <label for="email" class="label">Email</label>
          </div>
        </div>

        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="firstName" id="firstName" class="input" required>
            <label for="firstName" class="label">Nama Depan</label>
          </div>
        </div>

        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="lastName" id="lastName" class="input" required>
            <label for="lastName" class="label">Nama Belakang</label>
          </div>
        </div>

        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="contact" id="contact" class="input" required>
            <label for="contact" class="label">Kontak</label>
          </div>
        </div>

        <div class="modal-content">
          <div class="input-group">
            <input type="password" name="password" id="Password" class="input" required>
            <span class="toggle-password" onclick="togglePasswordVisibility()">
              <i class="fas fa-eye-slash" id="passwordIcon"></i>
            </span>
            <label for="password" class="label">Kata Sandi</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="button" onclick="closeaddUserModal()">Batal</button>
          <button type="submit" class="button">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal for editing users -->
  <div id="editUserModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-container">
      <form id="editUserForm" method="POST" action="edit_user.php">
        <div class="modal-header">
          <h2>Edit Pengguna</h2>
          <span class="close-icon" onclick="closeEditUserModal()">&times;</span>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="email" name="email" id="editEmail" class="input" required readonly>
            <label for="editEmail" class="label">Email</label>
          </div>
          <div class="input-group">
            <input type="text" name="firstName" id="editFirstName" class="input" required>
            <label for="editFirstName" class="label">Nama Depan</label>
          </div>
          <div class="input-group">
            <input type="text" name="lastName" id="editLastName" class="input" required>
            <label for="editLastName" class="label">Nama Belakang</label>
          </div>
          <div class="input-group">
            <input type="text" name="contact" id="editContact" class="input" required>
            <label for="editContact" class="label">Kontak</label>
          </div>
          <div class="input-group">
            <input type="password" name="password" id="editPassword" class="input" required>
            <span class="toggle-password" onclick="toggleEditPasswordVisibility()">
              <i class="fas fa-eye-slash" id="editPasswordIcon"></i>
            </span>
            <label for="editPassword" class="label">Kata Sandi</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="button" onclick="closeEditUserModal()">Batal</button>
          <button type="submit" class="button">Simpan</button>
        </div>
      </form>
    </div>
  </div>
  <?php
    include_once ('includes/footer.html');
    ?>
  <script src="js/sidebar.js"></script>
  <script>
    const modal = document.querySelector('.modal');
    const buttons = document.querySelectorAll('.toggleButton');

    buttons.forEach(button => {
      button.addEventListener('click', () => {
        modal.classList.toggle('open');
      });
    });

    function togglePassword(element) {
      const passwordMasked = element.previousElementSibling.previousElementSibling;
      const passwordVisible = element.previousElementSibling;
      if (passwordMasked.style.display === 'none') {
        passwordMasked.style.display = 'inline';
        passwordVisible.style.display = 'none';
        element.classList.remove('fa-eye');
        element.classList.add('fa-eye-slash');
      } else {
        passwordMasked.style.display = 'none';
        passwordVisible.style.display = 'inline';
        element.classList.remove('fa-eye-slash');
        element.classList.add('fa-eye');
      }
    }

    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('Password');
      const passwordIcon = document.getElementById('passwordIcon');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
      } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
      }
    }


    function openModal() {
      document.getElementById('addUserModal').classList.add('open');
    }

    function closeModal() {
      document.getElementById('addUserModal').classList.remove('open');
    }

    function openaddUserModal() {
      document.getElementById('addUserModal').classList.add('open');
    }

    function closeaddUserModal() {
      document.getElementById('addUserModal').classList.remove('open');
    }
   
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.querySelector('input[name="search"]');
      
      searchInput.addEventListener('keyup', function() {
          const searchQuery = searchInput.value;
          const xhr = new XMLHttpRequest();
          xhr.open('POST', 'search_users.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.onreadystatechange = function() {
              if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                  const response = xhr.responseText;
                  document.querySelector('#userTable tbody').innerHTML = response;
              }
          };
          xhr.send('search=' + encodeURIComponent(searchQuery));
      });
    });

    function deleteItem(email) {
      if (confirm('Apakah Anda yakin ingin menghapus Pengguna ini?')) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_user.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && xhr.status == 200) {
            location.reload();
          }
        };
        xhr.send("email=" + encodeURIComponent(email));
      }
    }

    function toggleEditPasswordVisibility() {
      const passwordField = document.getElementById('editPassword');
      const passwordIcon = document.getElementById('editPasswordIcon');
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
      } else {
        passwordField.type = 'password';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
      }
    }


    function openModal() {
      document.getElementById('editUserModal').classList.add('open');
    }

    function closeModal() {
      document.getElementById('editUserModal').classList.remove('open');
    }


    function openEditUserModal(button) {
      // Get user data from data attributes
      const email = button.getAttribute('data-email');
      const firstName = button.getAttribute('data-firstname');
      const lastName = button.getAttribute('data-lastname');
      const contact = button.getAttribute('data-contact');
      const password = button.getAttribute('data-password');

      // Set the values in the editUserForm
      document.getElementById('editEmail').value = email;
      document.getElementById('editFirstName').value = firstName;
      document.getElementById('editLastName').value = lastName;
      document.getElementById('editContact').value = contact;
      document.getElementById('editPassword').value = password;

      // Open the modal
      document.getElementById('editUserModal').classList.add('open');
    }


    function closeEditUserModal() {
      document.getElementById('editUserModal').classList.remove('open');
    }
  </script>

</body>

</html>