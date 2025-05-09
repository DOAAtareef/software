<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireLogin();

$images = getImages();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            color: #333;
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

    
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('https://i.etsystatic.com/12346933/r/il/597c3d/1293789843/il_1080xN.1293789843_2ybp.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #5c2a4a;
    margin: 0;
    padding: 0;
  }
        header h1 {
            margin: 0;
            font-size: 2.5rem;
        }

        header nav {
            margin-top: 0.5rem;
        }

        header nav a {
            color:  #5c2a4a;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            background-color: #007bff;
            border-radius: 8px;
        }

        header nav a:hover {
            background-color: #5c2a4a;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #e0a3b0;
            box-shadow: 0 4px 12px rgba(226, 163, 163, 0.1);
            border-radius: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #5c2a4a;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .image-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(236, 180, 180, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .image-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(214, 144, 144, 0.2);
        }

        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid #ddd;
        }

        .image-card p {
            padding: 1rem;
            margin: 0;
            font-size: 1rem;
            color: #555;
        }

        button {
            padding: 0.6rem 1rem;
            background-color: #C2185B;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 0.5rem;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #444;
        }

        button:focus {
            outline: none;
        }

        .comments {
            margin-top: 1rem;
            padding: 0 1rem;
            background-color: #f7f7f7;
            border-radius: 8px;
        }

        textarea {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        footer {
    background-color: rgba(255, 192, 203, 0.8);
    color: #5c2a4a;
    text-align: center;
    padding: 1rem;
    margin-top: 2rem;
    border-top: 2px solid #e0a3b0;
  }

        footer .back-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #C2185B;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        footer .back-btn:hover {
            background-color: #5c2a4a;
        }

    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="container">
            <h1>📷 Gallery</h1>
        </div>
    </header>

    <!-- Main content -->
    <div class="container">
        <h2>Users' favorite moments</h2>

        <?php if (empty($images)): ?>
            <p>No images uploaded yet.</p>
        <?php else: ?>
            <div class="gallery">
                <?php foreach ($images as $image): ?>
                    <div class="image-card">
                        <img src="../uploads/<?= htmlspecialchars($image['path']) ?>" alt="User Image">
                        <p><strong>By:</strong> <?= htmlspecialchars($image['username']) ?></p>

                        <form action="../images/like.php" method="post">
                            <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                            <button type="submit">
                                <?= hasLiked($_SESSION['user_id'], $image['id']) ? 'Unlike' : 'Like' ?>
                            </button>
                            <span><?= getLikesCount($image['id']) ?> likes</span>
                        </form>

                        <div class="comments">
                            <?php foreach (getComments($image['id']) as $comment): ?>
                                <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                                    <?= htmlspecialchars($comment['content']) ?></p>
                            <?php endforeach; ?>
                        </div>

                        <form action="../images/comment.php" method="post">
                            <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                            <textarea name="content" placeholder="Add a comment..." required></textarea>
                            <button type="submit">Post Comment</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
            <a href="../logout.php" class="back-btn">Logout</a>
            <p style="flex-basis: 100%; text-align: center; margin-top: 1rem;">&copy; 2025 My Snapshot Platform</p>
        </div>
    </footer>

</body>
</html>
