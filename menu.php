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
                                            <div class="button-container mt-2 d-flex flex-column" style="gap: 12px; padding: 12px;">
                                                <div class="w-100 d-flex justify-content-between align-items-center">
                                                    <p class="card-text text-center m-0" style="font-weight: 700; color: #dc2626; font-size: 18px;">Rp&nbsp;<?= number_format($row['price']) ?>/-</p>
                                                    <div class="qty-selector d-flex align-items-center" style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; height: 36px; width: 100px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                                        <button type="button" class="btn minus-qty" style="flex: 1; border: none; background: transparent; padding: 0; font-weight: bold; color: #475569; height: 100%; box-shadow: none;">-</button>
                                                        <input type="text" class="form-control text-center itemQty" value="1" readonly style="flex: 1.2; border: none; background: transparent; padding: 0; font-weight: 600; color: #1e293b; height: 100%; box-shadow: none; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-radius: 0;">
                                                        <button type="button" class="btn plus-qty" style="flex: 1; border: none; background: transparent; padding: 0; font-weight: bold; color: #475569; height: 100%; box-shadow: none;">+</button>
                                                    </div>
                                                </div>
                                                <div class="w-100 d-flex justify-content-between" style="gap: 10px;">
                                                    <button class="addItemBtn <?= $buttonClass ?>" type="button" style="flex: 0 0 50px; height: 42px; display: flex; justify-content: center; align-items: center; border-radius: 8px; padding: 0; margin: 0; background-color: #dc2626; border: none; color: white;" title="Tambah ke Keranjang">
                                                        <i class="fas fa-cart-plus" style="font-size: 18px;"></i>
                                                    </button>
                                                    <button class="orderNowBtn <?= $buttonClass ?>" type="button" style="flex: 1; height: 42px; font-size: 15px; font-weight: 600; background-color: #dc2626; color: white; border: none; border-radius: 8px; display: flex; justify-content: center; align-items: center; padding: 0; transition: opacity 0.2s ease;">
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
        <span class="pt-3"><strong>Anda harus masuk terlebih dahulu.</strong></span><br>
        <button class="toast-btn toast-ok">Oke</button>
    </div>

    <!-- Payment Method Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
          <div class="modal-header" style="background-color: #fefce8; border-bottom: 1px solid rgba(234, 179, 8, 0.2);">
            <h5 class="modal-title" id="paymentModalLabel" style="font-weight: 700; color: #dc2626;">Pilih Metode Pembayaran</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" style="color: #64748b;">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="padding: 24px;">
            <p class="text-muted mb-4" style="font-size: 15px;">Silakan pilih metode pembayaran untuk pesanan Anda:</p>
            
            <div class="payment-options">
                <div class="custom-control custom-radio mb-3 p-3" style="border: 1px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc;">
                    <input type="radio" id="payTakeaway" name="modal_payment_mode" class="custom-control-input" value="Takeaway" checked>
                    <label class="custom-control-label w-100" for="payTakeaway" style="font-weight: 600; color: #1e293b; cursor: pointer;">Ambil di tempat</label>
                </div>
                <div class="custom-control custom-radio mb-3 p-3" style="border: 1px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc;">
                    <input type="radio" id="payCash" name="modal_payment_mode" class="custom-control-input" value="Cash">
                    <label class="custom-control-label w-100" for="payCash" style="font-weight: 600; color: #1e293b; cursor: pointer;">Tunai (Cash)</label>
                </div>
                <div class="custom-control custom-radio p-3" style="border: 1px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc;">
                    <input type="radio" id="payMidtrans" name="modal_payment_mode" class="custom-control-input" value="Midtrans">
                    <label class="custom-control-label w-100" for="payMidtrans" style="font-weight: 600; color: #1e293b; cursor: pointer;">Pembayaran Online (Midtrans)</label>
                </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top: none; padding: 20px 24px; background-color: #f8fafc;">
            <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; color: #64748b; border: 1px solid #cbd5e1;">Batal</button>
            <button type="button" class="btn btn-primary" id="confirmOrderBtn" style="border-radius: 8px; font-weight: 600; background-color: #fb4a36; border-color: #fb4a36; padding: 8px 24px;">Lanjutkan</button>
          </div>
        </div>
      </div>
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
                var currentVal = parseInt(input.val()) || 1;
                input.val(currentVal + 1);
            });
            
            $(".minus-qty").click(function() {
                var input = $(this).siblings(".itemQty");
                var val = parseInt(input.val()) || 1;
                if (val > 1) {
                    input.val(val - 1);
                }
            });

            var currentOrderData = null;

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
                
                currentOrderData = {
                    action: 'order_now',
                    pid: $form.find(".pid").val(),
                    pname: $form.find(".pname").val(),
                    pprice: $form.find(".pprice").val(),
                    pimage: $form.find(".pimage").val(),
                    pcode: $form.find(".pcode").val(),
                    pqty: $form.find(".itemQty").val() || 1,
                    email: email
                };

                $('#paymentModal').modal('show');
            });

            $("#confirmOrderBtn").click(function() {
                if (!currentOrderData) return;
                
                var paymentMode = $("input[name='modal_payment_mode']:checked").val();
                
                var $btn = $(this);
                $btn.prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: 'action.php',
                    method: 'post',
                    data: currentOrderData,
                    dataType: 'json',
                    success: function(response) {
                        $btn.prop('disabled', false).text('Lanjutkan');
                        if (response.success) {
                            $('#paymentModal').modal('hide');
                            
                            var selectedItems = [{
                                id: response.cart_id,
                                quantity: response.quantity
                            }];
                            
                            var form = $('<form action="order_review.php" method="POST"></form>');
                            form.append('<input type="hidden" name="selected_items" value=\'' + JSON.stringify(selectedItems) + '\'>');
                            form.append('<input type="hidden" name="payment_mode" value="' + paymentMode + '">');
                            $('body').append(form);
                            form.submit();
                        } else {
                            showToast();
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).text('Lanjutkan');
                        alert("Terjadi kesalahan. Silakan coba lagi.");
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