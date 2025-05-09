<?php
// تأكد من تفعيل عرض الأخطاء
error_reporting(E_ALL);
ini_set('display_errors', 1);

// المفتاح السري من لوحة تحكم reCAPTCHA
$secret = "6Lfd0Z4pAAAAAAYveHV58d0aaBGBmOQQruKXPKgP";

// للحصول على الاستجابة الفعلية من POST (عند الاختبار الفعلي)
// $response = $_POST['g-recaptcha-response'];

// للاختبار الأولي يمكن استخدام قيمة تجريبية
$response = "test_token"; // سيسبب فشل متوقع

$url = "https://www.google.com/recaptcha/api/siteverify";
$data = [
    'secret' => $secret,
    'response' => $response,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

// استخدام cURL الأكثر موثوقية
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // مهلة 5 ثواني

$result = curl_exec($ch);
if (curl_errno($ch)) {
    die("Error: " . curl_error($ch));
}
curl_close($ch);

// تحويل النتيجة
$responseData = json_decode($result, true);

// عرض النتائج
echo "<h2>نتائج اختبار reCAPTCHA</h2>";
echo "<pre>";
print_r($responseData);
echo "</pre>";

// تحليل النتيجة
if ($responseData['success']) {
    echo "<p style='color:green'>✅ نجاح: التحقق صحيح</p>";
} else {
    echo "<p style='color:red'>❌ خطأ: " . 
         (isset($responseData['error-codes'][0]) ? 
         $responseData['error-codes'][0] : 'Unknown error') . 
         "</p>";
    
    echo "<h3>نصائح لحل المشكلة:</h3>";
    echo "<ul>";
    echo "<li>تأكد من صحة المفتاح السري</li>";
    echo "<li>تحقق من إضافة localhost في لوحة تحكم reCAPTCHA</li>";
    echo "<li>تأكد من أن السيرفر يتصل بالإنترنت</li>";
    echo "<li>جرب استخدام مفاتيح الاختبار أولاً</li>";
    echo "</ul>";
}
?>