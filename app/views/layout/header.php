<?php
// views/layout/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TikTok Clone</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #fe2c55;
            --black: #000000;
            --white: #ffffff;
            --gray: #16182308;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--white);
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 240px;
            padding: 20px;
            background-color: var(--white);
            border-right: 1px solid var(--gray);
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
        }

        .video-feed {
            max-width: 600px;
            margin: 0 auto;
        }

        .video-card {
            margin-bottom: 20px;
            border-bottom: 1px solid var(--gray);
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- Navigation menu -->
        <div class="logo">
            <img src="/assets/logo.png" alt="TikTok" height="42">
        </div>
        <nav>
            <a href="/" class="nav-item active">
                <i class="fas fa-home"></i>
                For You
            </a>
            <a href="/following" class="nav-item">
                <i class="fas fa-users"></i>
                Following
            </a>
            <a href="/upload" class="nav-item">
                <i class="fas fa-plus-square"></i>
                Upload
            </a>
            <a href="/profile" class="nav-item">
                <i class="fas fa-user"></i>
                Profile
            </a>
        </nav>
    </div>

