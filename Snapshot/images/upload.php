<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rootPath = 'C:/Program Files/Ampps/www/snap/Snapshotrr/';
require_once $rootPath . 'includes/db.php';
require_once $rootPath . 'includes/auth.php';

// التحقق من تسجيل الدخول (إلغاء التعليق إذا كنت تستخدم نظام المصادقة)
// requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $filetemp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];
    $fileError = $file['error'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $allowed = array('jpg', 'png', 'jpeg');
    
    // التحقق من نوع الملف
    if (in_array($fileActualExt, $allowed)) {
        // التحقق من عدم وجود أخطاء
        if ($fileError === 0) {
            // التحقق من حجم الملف (1MB كحد أقصى)
            if ($fileSize < 1000000) {
                $fileNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = 'uploads/' . $fileNew; // تغيير المسار إلى 'uploads/'
                
                // إنشاء المجلد إذا لم يكن موجودًا
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                
                // نقل الملف إلى المجلد
                if (move_uploaded_file($filetemp, $fileDestination)) {
                    // إدخال بيانات الصورة في قاعدة البيانات
                    try {
                        $stmt = $conn->prepare("INSERT INTO images (user_id, path) VALUES (?, ?)");
                        // إذا كنت تستخدم نظام المصادقة، استخدم $_SESSION['user_id']
                        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // 1 كقيمة افتراضية
                        $stmt->execute([$user_id, $fileNew]);
                        
                        // توجيه المستخدم بعد الرفع الناجح
                        header("Location: view_images.php"); // أو أي صفحة أخرى تريدها
                        exit();
                    } catch(PDOException $e) {
                        $error = "Database error: " . $e->getMessage();
                    }
                } else {
                    $error = "There was an error uploading your file!";
                }
            } else {
                $error = "Your file is too big! Maximum size is 1MB.";
            }
        } else {
            $error = "There was an error uploading your file!";
        }
    } else {
        $error = "You cannot upload files of this type! Only JPG, JPEG, and PNG are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>Upload Image</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #2d2d2d;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .login-container {
            max-width: 400px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-container input[type="text"],
        .login-container input[type="password"],
        .login-container input[type="file"],
        .login-container textarea {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .g-recaptcha {
            margin: 1rem 0;
        }
        .login-container button {
            width: 100%;
            padding: 0.8rem;
            background-color: #2d2d2d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-container button:hover {
            background-color: #444;
        }
        .signup-link {
            text-align: center;
            margin-top: 1rem;
        }
        .signup-link a {
            color: #2d2d2d;
            text-decoration: none;
            font-weight: bold;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
        footer {
            background-color: #2d2d2d;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }
        .container {
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>📸 My Snapshot Platform</h1>
        <p>Share your best moments with others</p>
    </header>

    <div class="login-container">
        <h2>Upload Your Image</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <p>Please upload an image file with either JPG or PNG extension (Max 1MB).</p>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit" name="upload">Upload</button>
        </form>

        <div class="container" style="margin-top: 20px;">
            <a href="view_images.php" class="button" style="padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">View All Images</a>
        </div>
    </div>

    <footer>
        &copy; 2025 My Snapshot Platform. All rights reserved.
    </footer>
</body>
</html>