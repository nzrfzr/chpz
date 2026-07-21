<?php
session_start();
include 'db_connection.php';

// Fetch all unique categories from the database
$categoryQuery = 'SELECT DISTINCT catName FROM menuitem';
$categoryResult = $conn->query($categoryQuery);

$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['catName'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--poppins-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/menu.css" />
    <title>Menu</title>
    <style>
        .disabled-button {
            background-color: gray;
            color: white;
            cursor: not-allowed;
            pointer-events: none;
        }

        .disabled-button i {
            color: white;
        }

        section:nth-child(odd) {
            background-color: #ffffff;
        }

        section:nth-child(even) {
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <?php

    if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
        include 'includes/nav-logged.php';
    } else {
        include 'includes/navbar.php';
    }
    ?>
    <div class="heading">
        <div class="row heading-title">Menu Kami</div>
        <div class="row heading-description">~Temukan kelezatan rasa dengan menu menarik kami!</div>
    </div>
    <?php foreach ($categories as $category): ?>
        <section id="<?= strtolower($category) ?>">
            <div id="message"></div>
            <div class="container-fluid">
                <h1 class="mt-1"> <?= strtoupper($catTranslations[$category] ?? $category) ?> </h1>
                <div class="row">
                    <?php
                    $stmt = $conn->prepare('SELECT * FROM menuitem WHERE catName = ?');
                    $stmt->bind_param('s', $category);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) :
                        $buttonClass = $row['status'] == 'Unavailable' ? 'disabled-button' : '';
                    ?>
                        <div class="col-md-6 col-lg-3 col-sm-12 menu-item col-xs-12">
                            <div class="card mt-4">
                                <img src="uploads/<?= $row['image'] ?>" alt="image" class="card-img-top" height="250">
                                <div class="card-body" style="height: auto; min-height: 320px; padding-bottom: 100px;">
                                    <h4 class="card-title text-center mt-3"><?= $row['itemName'] ?></h4>
                                    <p class="card-title text-center description ps-3 pe-3 pt-2 pb-3" style="font-weight: 500; font-size: 15px;"><?= $row['description'] ?></p>
                                    <?php if ($row['status'] == 'Unavailable') : ?>
                                        <p class="card-status" style="color: red; text-align: center; font-size: 1.3em;">Tidak Tersedia</p>
                                    <?php endif; ?>
                                    <div style="text-align: center;">
                                        <form action="" class="form-submit">
                                            <input type="hidden" class="pid" value='<?= $row['id'] ?>'>
                                            <input type="hidden" class="pname" value="<?= $row['itemName'] ?>">
                                            <input type="hidden" class="pprice" value="<?= $row['price'] ?>">
                                            <input type="hidden" class="pimage" value="<?= $row['image'] ?>">
                                            <input type="hidden" class="pcode" value="<?= $row['catName'] ?>">
                                            <div class="button-container mt-2 d-flex flex-column" style="gap: 10px;">
                                                <div class="w-100 d-flex justify-content-between align-items-center">
                                                    <p class="card-text text-center m-0" style="font-weight: 700; color: #dc2626; font-size: 16px;">Rp&nbsp;<?= number_format($row['price']) ?>/-</p>
                                                    <div class="d-flex align-items-center">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary minus-qty" style="padding: 0 8px;">-</button>
                                                        <input type="text" class="form-control text-center mx-1 itemQty" value="1" style="width: 40px; height: 30px; padding: 0;" readonly>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary plus-qty" style="padding: 0 8px;">+</button>
                                                    </div>
                                                </div>
                                                <div class="w-100 d-flex justify-content-between">
                                                    <button class="addItemBtn <?= $buttonClass ?>" type="button" style="flex: 1; margin-right: 5px; padding: 5px 10px;" title="Tambah ke Keranjang">
                                                        <i class="fas fa-cart-plus"></i>
                                                    </button>
                                                    <button class="btn btn-primary orderNowBtn <?= $buttonClass ?>" type="button" style="flex: 2; font-size: 14px; font-weight: 600; background-color: #fb4a36; border-color: #fb4a36; padding: 5px 10px;">
                                                        Pesan Sekarang
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
    <!-- Toast Notification Container -->
    <div id="toast" class="toast">
        <button class="toast-btn toast-close">&times;</button>
        <span class="pt-3"><strong>Anda harus masuk terlebih dahulu untuk menambah item ke keranjang.</strong></span><br>
        <button class="toast-btn toast-ok">Oke</button>
    </div>
    <!--Footer-->
    <?php
    include_once('includes/footer.html');
    ?>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script type="text/javascript">
        $(document).ready(function() {

            function userIsLoggedIn() {
                return <?php echo isset($_SESSION['userloggedin']) && $_SESSION['userloggedin'] === true ? 'true' : 'false'; ?>;
            }

            function showToast() {
                var toast = $('#toast');
                toast.addClass('show'); // Add the 'show' class to make the toast visible

                // Automatically hide the toast after 3 seconds
                setTimeout(function() {
                    toast.removeClass('show'); // Remove the 'show' class to hide the toast
                }, 5000);
            }

            function getUserEmail() {
                return "<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>";
            }

            $(".addItemBtn").click(function(e) {
                e.preventDefault(); // Prevent the default action

                if (!userIsLoggedIn()) {
                    showToast();
                    return;
                }

                // Check if the button has the 'disabled-button' class
                if ($(this).hasClass('disabled-button')) {
                    return; // Do nothing if the item is unavailable
                }

                var email = getUserEmail();

                var $form = $(this).closest(".form-submit");
                var pid = $form.find(".pid").val();
                var pname = $form.find(".pname").val();
                var pprice = $form.find(".pprice").val();
                var pimage = $form.find(".pimage").val();
                var pcode = $form.find(".pcode").val();
                var pqty = $form.find(".itemQty").val() || 1; 

                $.ajax({
                    url: 'action.php',
                    method: 'post',
                    data: {
                        pid: pid,
                        pname: pname,
                        pprice: pprice,
                        pqty: pqty,
                        pimage: pimage,
                        pcode: pcode,
                        email: email
                    },
                    success: function(response) {
                        $("#message").html(response);
                        window.scrollTo(0, 0);
                        load_cart_item_number();
                    }
                });
            });

            // Quantity buttons
            $(".plus-qty").click(function() {
                var input = $(this).siblings(".itemQty");
                input.val(parseInt(input.val()) + 1);
            });
            
            $(".minus-qty").click(function() {
                var input = $(this).siblings(".itemQty");
                var val = parseInt(input.val());
                if (val > 1) {
                    input.val(val - 1);
                }
            });

            $(".orderNowBtn").click(function(e) {
                e.preventDefault();

                if (!userIsLoggedIn()) {
                    showToast();
                    return;
                }

                if ($(this).hasClass('disabled-button')) {
                    return;
                }

                var email = getUserEmail();
                var $form = $(this).closest(".form-submit");
                var pid = $form.find(".pid").val();
                var pname = $form.find(".pname").val();
                var pprice = $form.find(".pprice").val();
                var pimage = $form.find(".pimage").val();
                var pcode = $form.find(".pcode").val();
                var pqty = $form.find(".itemQty").val() || 1;

                $.ajax({
                    url: 'action.php',
                    method: 'post',
                    data: {
                        action: 'order_now',
                        pid: pid,
                        pname: pname,
                        pprice: pprice,
                        pqty: pqty,
                        pimage: pimage,
                        pcode: pcode,
                        email: email
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            var selectedItems = [{
                                id: response.cart_id,
                                quantity: response.quantity
                            }];
                            
                            var form = $('<form action="order_review.php" method="POST"></form>');
                            form.append('<input type="hidden" name="selected_items" value=\'' + JSON.stringify(selectedItems) + '\'>');
                            form.append('<input type="hidden" name="payment_mode" value="Takeaway">');
                            $('body').append(form);
                            form.submit();
                        } else {
                            showToast();
                        }
                    }
                });
            });

            // Close button functionality
            $('.toast-close').click(function() {
                $('#toast').removeClass('show');
            });
            // Okay button redirection
            $('.toast-ok').click(function() {
                window.location.href = 'login.php'; // Redirect to login.php
            });

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