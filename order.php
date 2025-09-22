<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $total    = isset($_POST['total']) ? trim($_POST['total']) : '0';
    $name     = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone    = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $delivery = isset($_POST['delivery']) ? trim($_POST['delivery']) : '';
    $comment  = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $cartJson = isset($_POST['cart']) ? $_POST['cart'] : '[]';

    $cart = json_decode($cartJson, true);
    if (!is_array($cart)) { $cart = []; }

    // --- соберём письмо ---
    $to = "sicron@ukr.net"; // <-- замените на ваш email
    $subject = "Новый заказ с сайта";
    $lines = [];
    $lines[] = "Сумма заказа: {$total} грн";
    $lines[] = "Имя: {$name}";
    $lines[] = "Телефон: {$phone}";
    $lines[] = "Доставка: {$delivery}";
    $lines[] = "Комментарий: {$comment}";
    $lines[] = "";
    $lines[] = "Товары:";

    foreach ($cart as $item) {
        $iname = isset($item['name']) ? $item['name'] : '';
        $qty   = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $price = isset($item['price']) ? (float)$item['price'] : 0;
        $sum   = $qty * $price;
        $lines[] = " - {$iname} x {$qty} = {$sum} грн";
    }

    $message = implode("\n", $lines);
    $headers = "Content-Type: text/plain; charset=UTF-8\r\nFrom: shop@yourdomain.com";

    // --- отправка ---
    if (@mail($to, $subject, $message, $headers)) {
        echo "OK";
    } else {
        http_response_code(500);
        echo "MAIL_ERROR";
    }
} else {
    http_response_code(405);
    echo "METHOD_NOT_ALLOWED";
}
