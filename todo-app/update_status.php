<?php

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


$task_id = (int)$_GET['id'];
$new_status = trim($_GET['status']);


$allowed_statuses = ['pending', 'in_progress', 'completed'];

if (!in_array($new_status, $allowed_statuses)) {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Trạng thái mới không hợp lệ.'
    ];
    header("Location: index.php");
    exit;
}


try {

    
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_status, $task_id, $current_user_id]);

 
    if ($stmt->rowCount() > 0) {
      
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Đã cập nhật trạng thái công việc!'
        ];
    } else {
     
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'Không tìm thấy công việc hoặc bạn không có quyền.'
        ];
    }

} catch (PDOException $e) {

    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Lỗi CSDL: ' . $e->getMessage()
    ];
}


header("Location: index.php");
exit;
?>