<?php
/* =========================================
 * FILE XỬ LÝ XÓA CÔNG VIỆC (delete_task.php)
 * ========================================= */

// 1. GỌI "BẢO VỆ"
// Bắt buộc phải đăng nhập mới được xóa
// Cung cấp $pdo và $current_user_id
require_once 'auth_check.php';

// 2. Kiểm tra xem ID có được gửi lên không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Yêu cầu không hợp lệ. Không có ID công việc.'
    ];
    header("Location: index.php");
    exit;
}

// 3. Lấy ID công việc và ép kiểu về số nguyên
$task_id = (int)$_GET['id'];

// 4. Xóa công việc
try {
    // === BẢO MẬT QUAN TRỌNG ===
    // Câu lệnh DELETE này có 2 điều kiện WHERE:
    // 1. id = ?           -> Xóa đúng công việc
    // 2. AND user_id = ? -> Xóa công việc CHỈ KHI nó thuộc về người dùng đang đăng nhập
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $current_user_id]);

    // 5. Kiểm tra xem có thực sự xóa được không
    // $stmt->rowCount() trả về số hàng bị ảnh hưởng (bị xóa)
    if ($stmt->rowCount() > 0) {
        // Xóa thành công (có 1 hàng bị xóa)
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Đã xóa công việc thành công!'
        ];
    } else {
        // Không có hàng nào bị xóa (Lý do: id không tồn tại HOẶC id này không phải của user)
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'Không tìm thấy công việc hoặc bạn không có quyền xóa.'
        ];
    }

} catch (PDOException $e) {
    // Lỗi CSDL
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Lỗi CSDL khi xóa công việc: ' . $e->getMessage()
    ];
}

// 6. Luôn chuyển hướng về trang chủ
header("Location: index.php");
exit;
?>