<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$images = getImages();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://i.etsystatic.com/12346933/r/il/597c3d/1293789843/il_1080xN.1293789843_2ybp.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #5c2a4a;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
        }

        header {
            background-color: rgba(255, 192, 203, 0.8);
            color: #5c2a4a;
            padding: 1rem;
            text-align: center;
            border-bottom: 2px solid #e0a3b0;
        }

        footer {
            background-color: rgba(255, 192, 203, 0.8);
            color: #5c2a4a;
            text-align: center;
            padding: 1rem;
            margin-top: 5rem;
            border-top: 1px solid #e0a3b0;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        h1, h2 {
            text-align: center;
            color: #5c2a4a;
        }

        h1 {
            color:  #5c2a4a;
        }

        button {
            padding: 0.8rem 1.5rem;
            background-color: #ec407a;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #d81b60;
        }

        .card {
            margin-bottom: 2rem;
            background-color: #fff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card img {
            width: 100%;
            border-radius: 8px;
        }

        .card p {
            color: #555;
            margin-top: 0.5rem;
        }

        .comments {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f1f1f1;
            border-radius: 8px;
        }

        .comment-box {
            margin-bottom: 1rem;
            padding: 0.5rem;
            background-color: #e9ecef;
            border-radius: 8px;
        }

        .comment-box strong {
            color: #333;
        }

        .inline-form {
            display: inline-block;
            margin-left: 10px;
        }

        .btn-small {
            padding: 0.4rem 1rem;
            background-color: #dc3545;
            color:  #5c2a4a;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .btn-small:hover {
            background-color: #c82333;
        }

        .go-gallery-btn {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .go-gallery-btn a {
            padding: 0.8rem 1.5rem;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .go-gallery-btn a:hover {
            background-color: #218838;
        }

        .logout-btn {
            display: block;
            width: 100%;
            padding: 0.8rem 1.5rem;
            background-color: #dc3545;
            color: white;
            text-align: center;
            border-radius: 8px;
            margin-top: 2rem;
            cursor: pointer;
            font-size: 1rem;
        }

        .logout-btn:hover {
            background-color: #5c2a4a;
        }

        .error, .success {
            text-align: center;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>
</header>

<div class="container">
    <h2>All Images</h2>
    <?php foreach ($images as $image): ?>
        <div class="card">
            <img src="../uploads/<?= htmlspecialchars($image['path']) ?>" class="card-img">
            <p>Uploaded by: <strong><?= htmlspecialchars($image['username']) ?></strong></p>

            <!-- Delete Image -->
            <form action="../images/delete.php" method="post">
                <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete Image</button>
            </form>

            <!-- Comments -->
            <div class="comments">
                <h4>Comments:</h4>
                <?php foreach (getComments($image['id']) as $comment): ?>
                    <div class="comment-box">
                        <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                        <?= htmlspecialchars($comment['content']) ?>
                        <!-- Delete Comment -->
                        <form action="../images/delete_comment.php" method="post" class="inline-form">
                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                            <button type="submit" class="btn-small">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Logout Button -->
    <form action="../logout.php" method="post">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>

<footer>
    <p>&copy; 2025 My Snapshot Platform. All rights reserved.</p>
</footer>

</body>
</html>
