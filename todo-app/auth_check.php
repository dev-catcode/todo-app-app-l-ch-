<?php
/* =========================================
 * FILE "BẢO VỆ" (AUTH CHECK)
 * ========================================= */

// 1. Include file db.php
// (file db.php đã tự động gọi session_start())
require_once 'db.php';

// 2. Kiểm tra "vé vào cửa" (Session)
if (!isset($_SESSION['user_id'])) {
    // Nếu không có vé, đá về trang đăng nhập
    header("Location: login.php");
    exit; // Dừng thực thi script ngay lập tức
}

// 3. Nếu qua được cửa, lấy thông tin người dùng
// Chúng ta sẽ dùng 2 biến này ở tất cả các trang
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
?>