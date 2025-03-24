<?php
session_start();
include_once 'core.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мой сайт</title>
    <link rel="stylesheet" href="/css/btns.css">
    <link rel="stylesheet" href="/css/header_footer.css">
    <link rel="stylesheet" href="../../css/cr.css">

</head>
<body>

    <header>
        <div class="header_top">
            <div class="top_box">
                <img src="/img/pngwing 19.png" alt="" class="img1">
                <h3>Работаем по всей России</h3>
            </div>

            <a href="page/7.Delivery/Delivery.php">
                <div class="top_box">
                    <img src="/img/pngwing 13.png" alt="" class="img2">
                    <h3>Больше способов доставки</h3>
                </div>
            </a>

            <a href="page/8.Delivery2/Delivery2.php">
                <div class="top_box">
                    <img src="/img/pngwing 6.png" alt="" class="img3">
                    <h3>Магазины Nike</h3>
                </div>
            </a>

            <nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="../../page/16.admin/admin.php" class="btn">Админ-панель</a>
        <?php endif; ?>
        <a href="../../page/15.profile/profile.php" class="btn">Личный кабинет</a>
        <a href="../../functions/logout.php" class="btn">Выйти</a>
    <?php else: ?>
        <a href="../../page/14.auth/auth.php" class="btn">Вход</a>
    <?php endif; ?>
</nav>

        </div>

    </header>


