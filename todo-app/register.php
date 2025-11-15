<?php

require_once 'db.php';


$errors = [];

$username = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

  
    if (empty($username)) {
        $errors[] = "Username là bắt buộc.";
    }
    

    if (empty($email)) {
        $errors[] = "Email là bắt buộc.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  
        $errors[] = "Email không hợp lệ.";
    }


    if (empty($password)) {
        $errors[] = "Mật khẩu là bắt buộc.";
    } elseif (strlen($password) < 6) {

        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Mật khẩu xác nhận không khớp.";
    }
    
 
    if (empty($errors)) {
        try {
     
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
       
                $errors[] = "Username hoặc Email đã tồn tại. Vui lòng chọn tên khác.";
            } else {
           
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt_insert = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt_insert->execute([$username, $email, $hashed_password]);

                header("Location: login.php?register=success");
                exit; 
            }

        } catch (PDOException $e) {
       
            $errors[] = "Lỗi CSDL: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - ToDo App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 1rem; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="text-center mb-0">Đăng ký tài khoản</h2>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-0"><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($username); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu (ít nhất 6 ký tự)</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">Đăng ký</button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <p class="mb-0">Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>