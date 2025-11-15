<?php
require_once 'auth_check.php';


if (isset($_POST['id'])) { $task_id = (int)$_POST['id']; } 
elseif (isset($_GET['id'])) { $task_id = (int)$_GET['id']; } 
else {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Yêu cầu không hợp lệ.'];
    header("Location: index.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = trim($_POST['due_date']);
    $status = trim($_POST['status']);

    if (empty($title)) {
        $error = "Tiêu đề là bắt buộc.";
    } else {
        $description_to_db = !empty($description) ? $description : NULL;
        $due_date_to_db = !empty($due_date) ? $due_date : NULL;
        
        try {
           
            $stmt = $pdo->prepare(
                "UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ? 
                 WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([
                $title, $description_to_db, $due_date_to_db, $status, 
                $task_id, $current_user_id
            ]);

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Đã cập nhật công việc!'];
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = "Lỗi CSDL: " . $e->getMessage();
        }
    }
}


try {
    
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $current_user_id]);
    $task = $stmt->fetch();

    if (!$task) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Không tìm thấy công việc.'];
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    die("Lỗi CSDL: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Công việc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary shadow-sm"><div class="container"><a class="navbar-brand" href="index.php"><i class="fa-solid fa-list-check"></i> ToDo App</a></div></nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white"><h2 class="h4 mb-0">Chỉnh sửa Công việc</h2></div>
                    <div class="card-body p-4">
                        <?php if (isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                        <form action="edit_task.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề (Bắt buộc)</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($task['description']); ?></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="form-label">Ngày hết hạn</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Chưa xong</option>
                                        <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>Đang làm</option>
                                        <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="text-end">
                                <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-xmark"></i> Hủy bỏ</a>
                                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>