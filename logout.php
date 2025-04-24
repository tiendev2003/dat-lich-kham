<?php
// Bắt đầu session
session_start();

// Include file functions
require_once 'includes/functions.php';

// Thực hiện đăng xuất
logout_user();

// Chuyển hướng về trang chủ
header('Location: index.php');
exit;
?>
