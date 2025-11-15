<?php


require_once 'auth_check.php'; 


$filter_status = $_GET['filter_status'] ?? 'all'; 
$sort_by = $_GET['sort_by'] ?? 'created_desc'; 


$sql = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$current_user_id]; 


if ($filter_status !== 'all') {
   
    $sql .= " AND status = ?";
  
    $params[] = $filter_status;
}


switch ($sort_by) {
    case 'due_asc':
      
        $sql .= " ORDER BY due_date ASC";
        break;
    case 'created_desc':
    default:
       
        $sql .= " ORDER BY created_at DESC";
}


try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params); 
    $tasks = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi khi truy vấn CSDL: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ToDo App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .task-completed strong { text-decoration: line-through; color: #6c757d; }
        .text-nowrap { white-space: nowrap; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-list-check"></i> ToDo App</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            Chào, <strong><?php echo htmlspecialchars($current_username); ?></strong>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white pb-0 border-0">
                        <h2 class="h4 text-center">Quản lý Công việc</h2>
                    </div>
                    <div class="card-body">
                        
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['message']['text']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['message']); ?>
                        <?php endif; ?>

                        <h3 class="h5">Thêm công việc mới</h3>
                        <form action="add_task.php" method="POST" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6"><label for="title" class="form-label">Tiêu đề (Bắt buộc)</label><input type="text" class="form-control" id="title" name="title" required></div>
                                <div class="col-md-6"><label for="due_date" class="form-label">Ngày hết hạn (Tùy chọn)</label><input type="date" class="form-control" id="due_date" name="due_date" min="<?php echo date('Y-m-d'); ?>"></div>
                                <div class="col-12"><label for="description" class="form-label">Mô tả (Tùtùy chọn)</label><textarea class="form-control" id="description" name="description" rows="2"></textarea></div>
                                <div class="col-12 text-end"><button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm công việc</button></div>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <h3 class="h5 mb-3">Danh sách công việc của bạn</h3>
                        <form action="index.php" method="GET" class="row g-3 align-items-end mb-3 bg-light p-3 rounded">
                            <div class="col-md-5">
                                <label for="filter_status" class="form-label">Lọc theo trạng thái:</label>
                                <select name="filter_status" id="filter_status" class="form-select">
                                    <option value="all" <?php echo ($filter_status == 'all') ? 'selected' : ''; ?>>Tất cả</option>
                                    <option value="pending" <?php echo ($filter_status == 'pending') ? 'selected' : ''; ?>>Chưa xong</option>
                                    <option value="in_progress" <?php echo ($filter_status == 'in_progress') ? 'selected' : ''; ?>>Đang làm</option>
                                    <option value="completed" <?php echo ($filter_status == 'completed') ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="sort_by" class="form-label">Sắp xếp theo:</label>
                                <select name="sort_by" id="sort_by" class="form-select">
                                    <option value="created_desc" <?php echo ($sort_by == 'created_desc') ? 'selected' : ''; ?>>Ngày tạo (Mới nhất trước)</option>
                                    <option value="due_asc" <?php echo ($sort_by == 'due_asc') ? 'selected' : ''; ?>>Ngày hết hạn (Gần nhất trước)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fa-solid fa-filter"></i> Lọc
                                </button>
                            </div>
                        </form>

                        <?php if (empty($tasks)): ?>
                            <div class="alert alert-info text-center">
                                <i class="fa-solid fa-clipboard-check"></i>
                                <p class="mb-0">Không tìm thấy công việc nào!</p>
                                </div>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($tasks as $task): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center 
                                           <?php echo ($task['status'] == 'completed') ? 'list-group-item-light' : ''; ?>">
                                        
                                        <div class="<?php echo ($task['status'] == 'completed') ? 'task-completed' : ''; ?>">
                                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                                            <?php if (!empty($task['description'])): ?><small class="d-block text-muted"><?php echo htmlspecialchars($task['description']); ?></small><?php endif; ?>
                                            <?php if (!empty($task['due_date'])): ?>
                                                <small class="d-block <?php echo ($task['due_date'] < date('Y-m-d') && $task['status'] != 'completed') ? 'text-danger fw-bold' : 'text-secondary'; ?>">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                    Hết hạn: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="text-nowrap">
                                            <?php if ($task['status'] == 'completed'): ?>
                                                <a href="update_status.php?id=<?php echo $task['id']; ?>&status=pending" class="btn btn-warning btn-sm" title="Đánh dấu Chưa hoàn thành"><i class="fa-solid fa-arrow-rotate-left"></i></a>
                                            <?php else: ?>
                                                <a href="update_status.php?id=<?php echo $task['id']; ?>&status=completed" class="btn btn-success btn-sm" title="Đánh dấu Hoàn thành"><i class="fa-solid fa-check"></i></a>
                                            <?php endif; ?>
                                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-info btn-sm text-white" title="Chỉnh sửa công việc"><i class="fa-solid fa-pencil"></i></a>
                                            <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm" title="Xóa công việc" onclick="return confirm('Bạn có chắc chắn muốn xóa công việc này?');"><i class="fa-solid fa-trash-can"></i></a>
                                        </div>
                                        
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>