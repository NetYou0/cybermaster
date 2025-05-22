<?php
// ملف process_payment.php

// تفعيل عرض الأخطاء لأغراض التطوير (يجب تعطيله في الإنتاج)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// التعامل مع طلبات POST فقط
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال البيانات من النموذج
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $course = $_POST['course'] ?? '';
    $payment_method = $_POST['payment'] ?? '';
    
    // بيانات الدفع (ستختلف حسب طريقة الدفع)
    $card_number = $_POST['cardNumber'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    // التحقق من البيانات المطلوبة
    if (empty($name) || empty($email) || empty($course) || empty($payment_method)) {
        die('الرجاء ملء جميع الحقول المطلوبة.');
    }

    // تحديد سعر الدورة (يمكن استبدالها بقاعدة بيانات)
    $course_prices = [
        'python' => 10,
        'cyber' => 149,
        'web' => 129,
        'hacking' => 199
    ];

    $price = $course_prices[$course] ?? 0;
    
    // معالجة الدفع حسب الطريقة
    if ($payment_method === 'credit') {
        // محاكاة عملية الدفع بالبطاقة (في الواقع ستستخدم بوابة دفع مثل Stripe)
        if (empty($card_number) || empty($expiry) || empty($cvv)) {
            die('الرجاء إدخال بيانات البطاقة كاملة.');
        }
        
        // هنا ستكون عملية الاتصال ببوابة الدفع الفعلية
        $payment_status = 'completed'; // محاكاة نجاح الدفع
    } 
    elseif ($payment_method === 'paypal') {
        // في الواقع ستوجه المستخدم لصفحة PayPal
        $payment_status = 'pending'; // لأن الدفع يتم خارج الموقع
    }

    // حفظ بيانات الطلب في قاعدة البيانات (هذا مثال بسيط)
    // في الواقع ستستخدم MySQLi أو PDO
    $order_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'course' => $course,
        'price' => $price,
        'payment_method' => $payment_method,
        'status' => $payment_status,
        'date' => date('Y-m-d H:i:s')
    ];

    // في الواقع: $db->insert('orders', $order_data);
    file_put_contents('orders_log.txt', print_r($order_data, true), FILE_APPEND);

    // إرسال بريد إلكتروني للتأكيد (مثال)
    $to = $email;
    $subject = "تم استلام طلبك في CYBERMASTERS";
    $message = "
    <html>
    <head>
        <title>تأكيد الطلب</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #3498db; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; }
            .footer { text-align: center; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>CYBERMASTERS</h2>
            </div>
            <div class='content'>
                <p>مرحباً $name،</p>
                <p>شكراً لتسجيلك في دورة <strong>$course</strong>.</p>
                <p>مبلغ الطلب: <strong>$$price</strong></p>
                <p>طريقة الدفع: <strong>$payment_method</strong></p>
                <p>حالة الطلب: <strong>$payment_status</strong></p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " CYBERMASTERS. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: info@cybermasters.com" . "\r\n";

    mail($to, $subject, $message, $headers);

    // توجيه المستخدم إلى صفحة الشكر
    header('Location: thank_you.html');
    exit();
} else {
    // إذا حاول الوصول إلى الملف مباشرة
    header('Location: payment.html');
    exit();
}