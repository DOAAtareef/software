<?php
session_start();
// echo "hhhh";
if (isset($_POST["upload"])) {
    $file = $_FILES['file'];
    // print_r($file);
    $fileName = $_FILES['file']['name'];
    $filetemp = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileType = $_FILES['file']['type'];
    $fileError = $_FILES['file']['error'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $allowed = array('jpg', 'png', 'jpeg');
    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 1000000) {
                $fileNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = 'upload/' . $fileNew;
                if (!is_dir('upload')) {
                    mkdir('upload', 0777, true);
                }
                move_uploaded_file($filetemp, $fileDestination);

                header("location:login.php");
            } else echo "Your file is too big!.";
        } else echo 'There was an error uploading your file!';
    } else {
        echo "YOU cannot upload file of those type!";
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
        <p>Please upload an image file with either JPG or PNG extension.</p>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit" name="upload">Upload</button>
        </form>

        <!-- إضافة الزر لانتقال المستخدم إلى صفحة عرض الصور -->
        <div class="container" style="margin-top: 20px;">
            <a href="view_images.php" class="button" style="padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">View All Images</a>
        </div>
    </div>

    <footer>
        &copy; 2025 My Snapshot Platform. All rights reserved.
    </footer>
</body>
</html>
