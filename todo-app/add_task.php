<?php
/* =========================================
 * FILE XỬ LÝ THÊM CÔNG VIỆC (add_task.php)
 * ========================================= */

// 1. GỌI "BẢO VỆ"
// Bắt buộc phải đăng nhập mới được thêm
// File này cũng cung cấp cho chúng ta $pdo và $current_user_id
require_once 'auth_check.php';

// 2. Chỉ xử lý nếu phương thức là POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Lấy dữ liệu từ form
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = trim($_POST['due_date']); // Ngày hết hạn

    // 4. Validate dữ liệu
    if (empty($title)) {
        // Nếu tiêu đề rỗng, tạo thông báo lỗi và quay lại
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Tiêu đề công việc là bắt buộc.'
        ];
        header("Location: index.php");
        exit;
    }

    // 5. Xử lý dữ liệu (chuẩn bị cho CSDL)
    
    // Nếu mô tả rỗng, đặt là NULL
    $description_to_db = !empty($description) ? $description : NULL;
    
    // Nếu ngày hết hạn rỗng, đặt là NULL
    $due_date_to_db = !empty($due_date) ? $due_date : NULL;
    
    // Trạng thái mặc định
    $status = 'pending';

    // 6. Thêm vào CSDL
    try {
        // Dùng Prepared Statement để chống SQL Injection
        $stmt = $pdo->prepare(
            "INSERT INTO tasks (user_id, title, description, due_date, status) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        $stmt->execute([
            $current_user_id,  // Lấy từ auth_check.php
            $title,
            $description_to_db,
            $due_date_to_db,
            $status
        ]);

        // 7. Thêm thành công, tạo thông báo thành công
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Đã thêm công việc mới thành công!'
        ];

    } catch (PDOException $e) {
        // 8. Nếu lỗi CSDL, tạo thông báo lỗi
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Lỗi CSDL khi thêm công việc: ' . $e->getMessage()
        ];
    }

} else {
    // Nếu ai đó truy cập file này trực tiếp (GET)
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Yêu cầu không hợp lệ.'
    ];
}

// 9. Luôn luôn chuyển hướng về trang chủ
header("Location: index.php");
exit;
?>