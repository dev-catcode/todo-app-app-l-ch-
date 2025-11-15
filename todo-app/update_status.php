<?php
/* =========================================
 * FILE XỬ LÝ CẬP NHẬT TRẠNG THÁI (update_status.php)
 * ========================================= */

// 1. GỌI "BẢO VỆ"
// Bắt buộc phải đăng nhập
require_once 'auth_check.php';

// 2. Kiểm tra xem ID và Status có được gửi lên không
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Yêu cầu không hợp lệ.'
    ];
    header("Location: index.php");
    exit;
}

// 3. Lấy dữ liệu và làm sạch
$task_id = (int)$_GET['id'];
$new_status = trim($_GET['status']);

// 4. Validate trạng thái mới (Rất quan trọng)
// Chỉ cho phép 3 giá trị mà chúng ta đã định nghĩa trong CSDL (ENUM)
$allowed_statuses = ['pending', 'in_progress', 'completed'];

if (!in_array($new_status, $allowed_statuses)) {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Trạng thái mới không hợp lệ.'
    ];
    header("Location: index.php");
    exit;
}

// 5. Cập nhật CSDL
try {
    // === BẢO MẬT QUAN TRỌNG ===
    // Câu lệnh UPDATE có 2 điều kiện WHERE:
    // 1. id = ?           -> Cập nhật đúng công việc
    // 2. AND user_id = ? -> Chỉ cập nhật KHI nó thuộc về người dùng này
    
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_status, $task_id, $current_user_id]);

    // 6. Kiểm tra xem có thực sự cập nhật được không
    if ($stmt->rowCount() > 0) {
        // Cập nhật thành công
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Đã cập nhật trạng thái công việc!'
        ];
    } else {
        // Không có hàng nào bị ảnh hưởng
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'Không tìm thấy công việc hoặc bạn không có quyền.'
        ];
    }

} catch (PDOException $e) {
    // Lỗi CSDL
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Lỗi CSDL: ' . $e->getMessage()
    ];
}

// 7. Luôn chuyển hướng về trang chủ
header("Location: index.php");
exit;
?>