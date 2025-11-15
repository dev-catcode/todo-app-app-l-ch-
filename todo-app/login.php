<?php
/* =========================================
 * TRANG ĐĂNG NHẬP (login.php)
 * ========================================= */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Include file kết nối CSDL
// Biến $pdo và session_start() đã có sẵn
require_once 'db.php';

// 2. Nếu người dùng đã đăng nhập (đã có session)
// thì chuyển thẳng về trang chủ (index.php)
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// 3. Khởi tạo biến $error để chứa lỗi
$error = '';

// 4. Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 5. Kiểm tra dữ liệu rỗng
    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập cả Username và Mật khẩu.";
    } else {
        // 6. Dữ liệu hợp lệ, truy vấn CSDL
        try {
            // 6a. Tìm người dùng bằng username
            // Dùng Prepared Statement
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(); // Lấy 1 hàng kết quả

            // 6b. Kiểm tra xem $user có tồn tại VÀ mật khẩu có khớp không
            // Đây là yêu cầu bảo mật BẮT BUỘC
            if ($user && password_verify($password, $user['password'])) {
                
                // === Đăng nhập thành công! ===
                
                // 7. Lưu thông tin quan trọng vào Session
                // Chúng ta sẽ dùng $_SESSION['user_id'] ở mọi nơi
                // để biết ai đang đăng nhập
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // 8. Chuyển hướng người dùng đến trang chủ
                header("Location: index.php");
                exit;
            } else {
                // 6c. Sai username hoặc mật khẩu
                $error = "Username hoặc Mật khẩu không chính xác.";
            }

        } catch (PDOException $e) {
            $error = "Lỗi CSDL: " . $e->getMessage();
        }
    }
}
// Kết thúc logic PHP
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - ToDo App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 1rem; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="text-center mb-0">Đăng nhập</h2>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($_GET['register']) && $_GET['register'] == 'success'): ?>
                            <div class="alert alert-success">
                                Đăng ký tài khoản thành công! Vui lòng đăng nhập.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">Đăng nhập</button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <p class="mb-0">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>