<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm - Giỏ hàng</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>HỆ THỐNG QUẢN LÝ SẢN PHẨM</h2>
    <hr>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['username'])): ?>
    <div class="user-info">
        ⚡ Tài khoản: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> | 
        <a href="logout.php" style="color: #d32f2f; font-weight: bold; text-decoration: none;">[Đăng xuất]</a>
    </div>
<?php endif; ?>
    




