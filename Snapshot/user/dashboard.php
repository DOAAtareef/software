<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireLogin();

$images = getImages();
$error = '';
$success = false;

// معالجة رفع الصورة
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

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 1000000) {
                $fileNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = '../uploads/' . $fileNew;

                if (!is_dir('../uploads')) {
                    mkdir('../uploads', 0777, true);
                }

                if (move_uploaded_file($filetemp, $fileDestination)) {
                    try {
                        require_once '../includes/db.php';
                        $stmt = $conn->prepare("INSERT INTO images (user_id, path) VALUES (?, ?)");
                        $stmt->execute([$_SESSION['user_id'], $fileNew]);

                        $success = true;
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
        $error = "Only JPG, JPEG, and PNG files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('https://i.etsystatic.com/12346933/r/il/597c3d/1293789843/il_1080xN.1293789843_2ybp.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #5c2a4a;
    margin: 0;
    padding: 0;
}

header {
    background-color: rgba(255, 192, 203, 0.8);
    color: #5c2a4a;
    text-align: center;
    padding: 1rem;
    border-bottom: 2px solid #e0a3b0;
}

footer {
    background-color: rgba(255, 192, 203, 0.8);
    color: #5c2a4a;
    text-align: center;
    padding:1rem;
    margin-top: 11rem;
    border-top: 2px solid #e0a3b0;
}

.container {
    max-width: 700px;
    margin: 3rem auto;
    background: rgba(255, 255, 255, 0.95);
    padding: 2rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    border-radius: 20px;
    backdrop-filter: blur(5px);
}

h1, h2 {
    text-align: center;
    color: #c2185b;
}

form {
    margin-top: 1.5rem;
}

input[type="file"] {
    width: 100%;
    padding: 0.8rem;
    margin-bottom: 1rem;
    border: 1px solid #f8bbd0;
    border-radius: 12px;
    background-color: #fff0f6;
}

button {
    width: 100%;
    padding: 0.8rem;
    background-color: #ec407a;
    color: white;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #d81b60;
}

.error {
    color: red;
    text-align: center;
    margin-bottom: 1rem;
}

.success {
    color: green;
    text-align: center;
    margin-bottom: 1rem;
}

.go-gallery-btn {
    display: flex;
    justify-content: center;
    margin-top: 1rem;
}

.go-gallery-btn a {
    padding: 0.8rem 1.5rem;
    background-color: #ad1457;
    color: white;
    text-decoration: none;
    border-radius: 12px;
    transition: background-color 0.3s;
}

.go-gallery-btn a:hover {
    background-color: #880e4f;
}

    </style>
</head>
<body>
    <header>
        <h1>📸 Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
        <p>Share your moments and explore others!</p>
    </header>

    <div class="container">
        <section>
            <h2>Upload Your Image</h2>

            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success">✅ Image uploaded successfully!</div>
                
            <?php endif; ?>
            
            <form action="dashboard.php" method="post" enctype="multipart/form-data">
                <input type="file" name="file" accept="image/*" required>
                <button type="submit">Upload</button>
                <div class="go-gallery-btn">
                    <a href="gallery.php">Go to Gallery</a>
                </div>
            </form>
        </section>
    </div>

    <footer>
        &copy; 2025 My Snapshot Platform. All rights reserved.
    </footer>
</body>
</html>
