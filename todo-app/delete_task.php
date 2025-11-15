<?php


require_once 'auth_check.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Yêu cầu không hợp lệ. Không có ID công việc.'
    ];
    header("Location: index.php");
    exit;
}

$task_id = (int)$_GET['id'];


try {

    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $current_user_id]);


    if ($stmt->rowCount() > 0) {
  
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Đã xóa công việc thành công!'
        ];
    } else {
   
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'Không tìm thấy công việc hoặc bạn không có quyền xóa.'
        ];
    }

} catch (PDOException $e) {

    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Lỗi CSDL khi xóa công việc: ' . $e->getMessage()
    ];
}


header("Location: index.php");
exit;
?>