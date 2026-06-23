<?php
session_start();

// Include database connection file
include 'db_connection.php';

// Check if database connection was successful
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Prepare query to fetch popular items
$sql = "SELECT itemName, image, price FROM menuitem WHERE is_popular = 1";

// Check if query was successful
if ($result = $conn->query($sql)) {
  // Initialize array to store popular items
  $popularItems = [];

  // Fetch and store query results
  while ($row = $result->fetch_assoc()) {
    $popularItems[] = $row;
  }

  // Close query result
  $result->close();
} else {
  // Display error message if query fails
  echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--Bootstrap CSS-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <!--poppins-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!--Icon-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
  <link href="https://fonts.googleapis.com/css2?family=Allura&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Chewy Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
  <!-- AOS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="css/index.css">
  <title>Home</title>
</head>

<body>
  <?php
  if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
    include 'includes/nav-logged.php';
  } else {
    include 'includes/navbar.php';
  }
  ?>

  <div class="main">
    <section>
      <div class="container mt-3">
        <div class="row d-flex justify-content-start align-items-start main-container">
          <div class="col-md-5 col-sm-12 col-lg-5 reveal main-text mb-4 text-align-justify mt-5" data-aos="fade-up">
            <h2>Selamat datang di <span style="color: #fb4a36;"> Cheapiza</span></h2>
            <h4 style="color: gray; font-weight: 450;">"Tempat di mana cita rasa pedas bertemu dengan kenyamanan yang menyegarkan."</h4>
            <p style="font-size: 18px; text-align: justify;">
              Selami perayaan kuliner di mana setiap hidangan kaya akan cita rasa. Di Cheapiza, kami percaya dalam menjadikan setiap hidangan sebagai pengalaman yang tak terlupakan. Baik Anda datang untuk makan santai atau acara spesial, hidangan kami yang semarak akan meninggalkan kesan yang tak terlupakan.
            </p>
            <div class="buttondiv">
              <div>
                <a href="login.php">
                  <button class="button">
                    Mulai Pesan
                    <svg class="cartIcon" viewBox="0 0 576 512">
                      <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"></path>
                    </svg>
                  </button>
                </a>
              </div>
              <div>
                <a class="button1" href="menu.php">
                  <span class="button__icon-wrapper">
                    <svg width="10" class="button__icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 15">
                      <path fill="currentColor" d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"></path>
                    </svg>
                    <svg class="button__icon-svg button__icon-svg--copy" xmlns="http://www.w3.org/2000/svg" width="10" fill="none" viewBox="0 0 14 15">
                      <path fill="currentColor" d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"></path>
                    </svg>
                  </span>
                  Jelajahi Menu
                </a>
              </div>
            </div>
          </div>
          <div class="col-md-7 col-sm-12 col-lg-7 d-flex justify-content-center align-items-start slide-in-right main-image">
            <img src="images/Pizza.png" class="img" style=" width: 85%; height: 80%;">
          </div>
        </div>
        <div class="row">
  <!-- Why Choose Us Section  -->
  <section class="why-choose-us" id="why-choose-us">
    <div class="container">
      <div class="row why-us-content">
        <div class="col-md-12 col-lg-6 col-sm-12 col-xs-12 mt-5 reveal d-flex justify-content-start align-items-start" data-aos="fade-up">
          <img src="images/Why-Us.png" width="100%" height="auto" loading="lazy" alt="delivery boy" class="w-100 delivery-img" data-delivery-boy>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 d-flex flex-column justify-content-center reveal" data-aos="fade-up">
          <h1>MENGAPA <span>MEMILIH KAMI?</span></h1>
          <p class="content">Restoran kami menawarkan layanan pengiriman makanan terbaik dengan bahan-bahan segar dan berkualitas tinggi.</p>
          <ul class="why-choose-us-list">
            <li data-aos="fade-up">
              <div class="image-wrapper mt-1">
                <img src="icons/delivery-man.png" alt="Fast Delivery">
              </div>
              <div class="feature-content">
                <h4>Pengiriman Cepat</h4>
                <p>Nikmati pengiriman yang cepat dan andal langsung ke pintu Anda.</p>
              </div>
            </li>
            <li data-aos="fade-up">
              <div class="image-wrapper">
                <img src="icons/vegetables.png" alt="Fresh Ingredients">
              </div>
              <div class="feature-content">
                <h4>Bahan Segar</h4>
                <p>Kami hanya menggunakan bahan-bahan paling segar dan berkualitas tinggi.</p>
              </div>
            </li>
            <li data-aos="fade-up">
              <div class="image-wrapper">
                <img src="icons/waiter (1).png" alt="Friendly Service" class="why-us-image">
              </div>
              <div class="feature-content">
                <h4>Pelayanan Ramah</h4>
                <p>Rasakan layanan pelanggan yang hangat dan ramah.</p>
              </div>
            </li>
            <li data-aos="fade-up">
              <div class="image-wrapper">
                <img src="icons/tasty.png" alt="Exceptional Taste">
              </div>
              <div class="feature-content">
                <h4>Rasa Luar Biasa</h4>
                <p>Nikmati cita rasa yang benar-benar luar biasa.</p>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Top picks section -->
      <div class="popular reveal" data-aos="fade-up">
        <h1 class="text-center mt-3">PILIHAN <span>TERBAIK KAMI</span></h1>
        <P class="text-center" style="font-size: 1.3rem;">~Hidangan pilihan yang disukai semua orang.</P>

        <div id="cardCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="8000" data-aos="fade-up">
          <div class="carousel-inner">

            <div id="toast" class="toast">
              <button class="toast-btn toast-close">&times;</button>
              <span class="pt-3"><strong>Anda harus masuk terlebih dahulu untuk menambah item ke keranjang.</strong></span>
              <button class="toast-btn toast-ok">Oke</button>
            </div>
            <?php
            $chunkedItems = array_chunk($popularItems, 3); // Group items into chunks of 3
            $isActive = true; // To set the first carousel item as active

            foreach ($chunkedItems as $items) {
              echo '<div class="carousel-item' . ($isActive ? ' active' : '') . '" >';
              echo '<div class="d-flex justify-content-center">';

              foreach ($items as $item) {
                echo '<div class="card" >';
                echo '<img src="uploads/' . $item['image'] . '" class="card-img-top" alt="' . $item['itemName'] . '">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title text-center">' . $item['itemName'] . '</h5>';
                echo '<p class="card-text text-center">Rp ' . $item['price'] . '</p>';
                echo '<a class="button-cart" onclick="addToCart()">Tambah ke Keranjang</a>';
                echo '</div>';
                echo '</div>';
              }

              echo '</div>';
              echo '</div>';
              $isActive = false; // Only the first item should be active
            }
            ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#cardCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sebelumnya</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#cardCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Berikutnya</span>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- About Us section -->
  <div class="aboutus" id="About-Us" style="background-image: url(images/about-bg.png); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <section class="our-story-section p-5">
      <div class="container ">
        <div class="row" data-aos="fade-up">
          <h1 style="text-align: center;">TENTANG <span style="color: #fb4a36;">KAMI</span></h1>
          <h4 style="text-align: center;" class="mb-5">Menyajikan Hidangan yang Berkesan!</h4>
        </div>
        <div class="story-content row mb-2">
          <div class="story-text col-lg-6 col-md-6 col-sm-12 reveal mt-2" data-aos="fade-up" data-os-interval="300">
            <p>Di <strong>Cheapiza</strong>, kami sangat bersemangat merayakan makanan. Koki kami menghadirkan sentuhan kreativitas pada setiap hidangan, memanjakan seluruh indra Anda. Bergabunglah dengan kami untuk pengalaman bersantap luar biasa yang merayakan cita rasa dan kebahagiaan.</p>
            <p>Komitmen kami untuk menggunakan bahan-bahan paling segar, dipadukan dengan keahlian koki kami, telah membuahkan reputasi yang luar biasa. Kami percaya bahwa bersantap bukan sekadar makan, melainkan menikmati seni kuliner.</p>
            <p>Baik Anda mencari makan malam romantis, pertemuan keluarga, atau tempat untuk merayakan momen spesial, Cheapiza menawarkan suasana sempurna dan hidangan lezat. Pesan sekarang dan rasakan kelezatan cita rasa bersama kami!</p>
            <a href="menu.php" class="about_btn">
              <i class="fa-solid fa-burger"></i>Pesan Sekarang
            </a>
          </div>
          <div class="story-image col-lg-6 col-md-6 col-sm-12 d-flex justify-content-end align-items-start slide-in-right" data-aos="fade-up">
            <img src="images/Burger.png" alt="Menyajikan Hidangan yang Berkesan" style="width: 100%; height: auto;">
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Review  -->
  <section class="testimonial" id="review">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1">
          <div class="text-center mb-5" data-aos="fade-up">
            <h1>Apa Kata <span>Pelanggan Kami!</span></h1>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="clients-carousel owl-carousel" data-aos="fade-up">
          <div class="single-box">
            <div class="img-area"><img alt="" class="img-fluid" src="uploads/user-girl.png"></div>
            <div class="content">
              <p>"Makanannya segar, dan rasanya luar biasa. Saya sangat menyukai variasi menunya. Tempat yang bagus untuk makan malam keluarga."</p>
              <h4>-Ritika Singh</h4>
            </div>
          </div>
          <div class="single-box">
            <div class="img-area"><img alt="" class="img-fluid" src="uploads/user-boy.jpg"></div>
            <div class="content">
              <p>"Proses pemesanan online sangat lancar dan mudah dinavigasi. Makanan saya tiba dalam keadaan hangat dan tepat waktu. Layanan pengirimannya sangat profesional."</p>
              <h4>-Zidnan</h4>
            </div>
          </div>
          <div class="single-box">
            <div class="img-area"><img alt="" class="img-fluid" src="uploads/default.jpg"></div>
            <div class="content">
              <p>"Tempat yang luar biasa! Burgernya berair dan lezat, pizza-nya penuh dengan topping. Stafnya sangat ramah, dan pelayanannya cepat. Tempat favorit baru saya!"</p>
              <h4>-Dave Wood</h4>
            </div>
          </div>
          <div class="single-box">
            <div class="img-area"><img alt="" class="img-fluid" src="uploads/default.jpg"></div>
            <div class="content">
              <span class="rating-star"><i class="icofont-star"></i><i class="icofont-star"></i><i class="icofont-star"></i><i class="icofont-star"></i><i class="icofont-star"></i></span>
              <p>"Sistem pemesanan online sangat fantastis. Sangat mudah untuk menyesuaikan pesanan saya, dan pengirimannya selalu cepat. Makanannya tiba hangat dan lezat setiap saat."</p>
              <h4>-jimmy kimmel</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- footer -->
  <footer>
    <div class="footer-container">
      <div class="footer-row">
        <div class="footer-col" id="contact">
          <h4>Hubungi Kami</h4>
          <p>Aik Dareq, Kec. Batukliang, Kabupaten Lombok Tengah, Nusa Tenggara Bar. 83552</p>
          <p>Email: cheapiza@gmail.com</p>
          <p>Telp: +62 878 4502 1444</p>
        </div>
        <div class="footer-col">
          <h4>Ikuti Kami</h4>
          <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      <!--  
        <div class="footer-col">
          <h4>Berlangganan</h4>
          <form action="#">
            <input type="email" placeholder="Alamat email Anda" required style="background-color: #f9f9f9; color: #333; margin-top: 12px;">
            <button type="submit">Berlangganan</button>
          </form>
        </div>
      </div>
          -->
    </div>
  </footer>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js">
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js">
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js">
  </script>
  <!-- AOS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
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
  <script>
    $('.clients-carousel').owlCarousel({
      loop: true,
      nav: false,
      autoplay: true,
      autoplayTimeout: 5000,
      animateOut: 'fadeOut',
      animateIn: 'fadeIn',
      smartSpeed: 450,
      margin: 30,
      responsive: {
        0: {
          items: 1
        },
        768: {
          items: 2
        },
        991: {
          items: 2
        },
        1200: {
          items: 2
        },
        1920: {
          items: 2
        }
      }
    });
  </script>
  <script>
    function addToCart() {
      var userLoggedIn = <?php echo isset($_SESSION['userloggedin']) ? 'true' : 'false'; ?>;

      if (!userLoggedIn) {
        showToast();
      } else {
        // Add to cart logic goes here
      }
    }

    function showToast() {
      var toast = document.getElementById("toast");
      toast.className = "toast show";

      // Handle "Okay" button click
      document.querySelector('.toast-ok').onclick = function() {
        window.location.href = 'login.php'; // Redirect to login page
      };

      // Handle "Close (X)" button click
      document.querySelector('.toast-close').onclick = function() {
        toast.className = toast.className.replace("show", "hide");
      };
    }
  </script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const elements = document.querySelectorAll('.animate-on-scroll');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('reveal');
          }
        });
      }, {
        threshold: 0.1
      });

      elements.forEach(element => {
        observer.observe(element);
      });
    });
  </script>


</body>
</html>