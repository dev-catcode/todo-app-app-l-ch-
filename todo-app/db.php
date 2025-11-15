<?php
/* =========================================
 * FILE KẾT NỐI CSDL (SỬ DỤNG PDO)
 * ========================================= */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// 1. Thông tin CSDL
// Thường là 'root' và mật khẩu rỗng nếu dùng XAMPP
define('DB_HOST', 'localhost');
define('DB_NAME', 'project_todo'); // Tên CSDL bạn đã tạo
define('DB_USER', 'root');
define('DB_PASS', ''); // Mật khẩu của bạn

// 2. Cấu hình DSN (Data Source Name)
// Chuỗi này mô tả loại CSDL, host, tên CSDL và (quan trọng) bộ ký tự
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

// 3. Cấu hình các Tùy chọn (Options) cho PDO
$options = [
    // Báo lỗi: Khi có lỗi CSDL, PDO sẽ "ném" ra một ngoại lệ (Exception)
    // Điều này giúp chúng ta bắt lỗi dễ dàng bằng try...catch
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Kiểu lấy dữ liệu: Mặc định là lấy về dạng mảng kết hợp (tên cột => giá trị)
    // Ví dụ: $row['username'] thay vì $row[0]
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Tùy chọn bảo mật: Tắt chế độ mô phỏng Prepared Statements của PDO
    // Chúng ta muốn MySQL tự xử lý Prepared Statements để đảm bảo an toàn 100%
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 4. Tạo đối tượng PDO (Đây là lúc kết nối thực sự diễn ra)
try {
    // $pdo là biến mà chúng ta sẽ dùng ở mọi nơi để truy vấn CSDL
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Nếu kết nối thất bại (sai pass, sai tên CSDL...),
    // hiển thị lỗi và dừng chương trình
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// 5. Khởi động Session
// Chúng ta đặt ở đây vì BẤT KỲ file nào cần CSDL (include 'db.php')
// thì cũng có thể sẽ cần dùng đến Session (để lưu user_id)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>