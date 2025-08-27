<?php
if (session_status() ===  PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic-CMS</title>
    <base href="/clinic-cms/">
    <link rel="stylesheet" href="/clinic-cms/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <header>
        <div class="header-top">
            <div class="header-left">
                <img class="heart" src="/clinic-cms/images/heart.jpg" alt="heart">
                <h1>Clinic-CMS</h1>
            </div>
            <div class="header-right">
                <p class="contact-us">Contact us (604) 778-5678 </p>
            </div>
        </div>
        <nav class="header-nav">
          <a href="/clinic-cms/index.php">Home</a>
    <a href="/clinic-cms/login.php">Login</a>
    <a href="/clinic-cms/logout.php">Logout</a>
        </nav>
    </header>
    <main>