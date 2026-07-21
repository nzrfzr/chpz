<?php
session_start();
require 'db_connection.php';

// Check if the email session variable is set
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Add products into the cart table
    if (isset($_POST['pid']) && isset($_POST['pname']) && isset($_POST['pprice']) && (!isset($_POST['action']) || $_POST['action'] !== 'order_now')) {
        $pid    = $_POST['pid'];
        $pname  = $_POST['pname'];
        $pprice = $_POST['pprice'];
        $pimage = $_POST['pimage'];
        $pcode  = $_POST['pcode'];
        $pqty   = isset($_POST['pqty']) ? intval($_POST['pqty']) : 1;

        $total_price = $pprice * $pqty;

        $stmt = $conn->prepare('SELECT itemName FROM cart WHERE itemName=? AND email=?');
        $stmt->bind_param('ss', $pname, $email);
        $stmt->execute();
        $res  = $stmt->get_result();
        $r    = $res->fetch_assoc();
        $code = $r['itemName'] ?? '';

        if (!$code) {
            $query = $conn->prepare('INSERT INTO cart (itemName, price, image, quantity, total_price, catName, email) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $query->bind_param('sdsisss', $pname, $pprice, $pimage, $pqty, $total_price, $pcode, $email);
            $query->execute();

            $safe_name = htmlspecialchars($pname);
            echo '
            <div id="cartToast" style="
                position: fixed;
                top: 24px;
                right: 24px;
                z-index: 99999;
                min-width: 300px;
                max-width: 360px;
                background: #ffffff;
                border-radius: 14px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
                border-left: 5px solid #16a34a;
                padding: 16px 18px 16px 20px;
                display: flex;
                align-items: flex-start;
                gap: 14px;
                animation: cartToastIn 0.35s cubic-bezier(0.34,1.56,0.64,1) forwards;
                font-family: Poppins, sans-serif;
            ">
                <div style="width:38px;height:38px;flex-shrink:0;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="margin:0 0 3px 0;font-weight:700;font-size:14px;color:#0f172a;line-height:1.3;">Berhasil ditambahkan! 🎉</p>
                    <p style="margin:0;font-size:13px;color:#64748b;font-weight:400;line-height:1.4;">' . $safe_name . ' &times;' . $pqty . ' telah masuk ke keranjang.</p>
                </div>
                <button onclick="(function(){var t=document.getElementById(\'cartToast\');t.style.transition=\'opacity 0.3s,transform 0.3s\';t.style.opacity=0;t.style.transform=\'translateX(60px)\';setTimeout(function(){t.remove()},300)})()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;line-height:1;padding:0;flex-shrink:0;margin-top:-2px;" onmouseover="this.style.color=\'#475569\'" onmouseout="this.style.color=\'#94a3b8\'">&times;</button>
            </div>
            <style>
                @keyframes cartToastIn {
                    from { opacity:0; transform:translateX(60px) scale(0.95); }
                    to   { opacity:1; transform:translateX(0) scale(1); }
                }
            </style>
            <script>
                setTimeout(function(){
                    var t = document.getElementById("cartToast");
                    if(t){ t.style.transition="opacity 0.4s ease,transform 0.4s ease"; t.style.opacity=0; t.style.transform="translateX(60px)"; setTimeout(function(){if(t)t.remove();},400); }
                }, 3500);
            </script>';
        } else {
            $safe_name = htmlspecialchars($pname);
            echo '
            <div id="cartToast" style="
                position: fixed;
                top: 24px;
                right: 24px;
                z-index: 99999;
                min-width: 300px;
                max-width: 360px;
                background: #ffffff;
                border-radius: 14px;
                box-shadow: 0 8px 30px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
                border-left: 5px solid #dc2626;
                padding: 16px 18px 16px 20px;
                display: flex;
                align-items: flex-start;
                gap: 14px;
                animation: cartToastIn 0.35s cubic-bezier(0.34,1.56,0.64,1) forwards;
                font-family: Poppins, sans-serif;
            ">
                <div style="width:38px;height:38px;flex-shrink:0;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="margin:0 0 3px 0;font-weight:700;font-size:14px;color:#0f172a;line-height:1.3;">Sudah ada di keranjang</p>
                    <p style="margin:0;font-size:13px;color:#64748b;font-weight:400;line-height:1.4;">' . $safe_name . ' sudah ada di keranjang Anda.</p>
                </div>
                <button onclick="(function(){var t=document.getElementById(\'cartToast\');t.style.transition=\'opacity 0.3s,transform 0.3s\';t.style.opacity=0;t.style.transform=\'translateX(60px)\';setTimeout(function(){t.remove()},300)})()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;line-height:1;padding:0;flex-shrink:0;margin-top:-2px;" onmouseover="this.style.color=\'#475569\'" onmouseout="this.style.color=\'#94a3b8\'">&times;</button>
            </div>
            <style>
                @keyframes cartToastIn {
                    from { opacity:0; transform:translateX(60px) scale(0.95); }
                    to   { opacity:1; transform:translateX(0) scale(1); }
                }
            </style>
            <script>
                setTimeout(function(){
                    var t = document.getElementById("cartToast");
                    if(t){ t.style.transition="opacity 0.4s ease,transform 0.4s ease"; t.style.opacity=0; t.style.transform="translateX(60px)"; setTimeout(function(){if(t)t.remove();},400); }
                }, 3500);
            </script>';
        }
    }

    // Get no. of items available in the cart table
    if (isset($_GET['cartItem']) && $_GET['cartItem'] == 'cart_item') {
        $stmt = $conn->prepare('SELECT SUM(quantity) AS qty FROM cart WHERE email=?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();

        // Check if 'qty' is null and set to 0 if so
        $quantity = $row['qty'] !== null ? $row['qty'] : 0;

        echo $quantity;
    }

    // Order Now handler
    if (isset($_POST['action']) && $_POST['action'] == 'order_now') {
        header('Content-Type: application/json');
        $pid    = $_POST['pid'];
        $pname  = $_POST['pname'];
        $pprice = $_POST['pprice'];
        $pimage = $_POST['pimage'] ?? '';
        $pcode  = $_POST['pcode'] ?? '';
        $pqty   = isset($_POST['pqty']) ? intval($_POST['pqty']) : 1;
        $total_price = $pprice * $pqty;

        $stmt = $conn->prepare('SELECT id, quantity FROM cart WHERE itemName=? AND email=?');
        $stmt->bind_param('ss', $pname, $email);
        $stmt->execute();
        $res  = $stmt->get_result();
        $row  = $res->fetch_assoc();
        $stmt->close();

        $cart_id = 0;
        if ($row) {
            $new_qty   = $pqty;
            $new_total = $pprice * $new_qty;
            $cart_id   = $row['id'];
            $upd = $conn->prepare('UPDATE cart SET quantity=?, total_price=? WHERE id=?');
            $upd->bind_param('idi', $new_qty, $new_total, $cart_id);
            $upd->execute();
            $upd->close();
        } else {
            $ins = $conn->prepare('INSERT INTO cart (itemName, price, image, quantity, total_price, catName, email) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $ins->bind_param('sdsisss', $pname, $pprice, $pimage, $pqty, $total_price, $pcode, $email);
            $ins->execute();
            $cart_id = $ins->insert_id;
            $ins->close();
        }
        echo json_encode(['success' => true, 'cart_id' => $cart_id, 'quantity' => $pqty]);
        exit;
    }

}
?>
