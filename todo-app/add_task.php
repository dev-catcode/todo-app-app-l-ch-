<?php

require_once 'auth_check.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

 
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = trim($_POST['due_date']);


    if (empty($title)) {
      
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Tiêu đề công việc là bắt buộc.'
        ];
        header("Location: index.php");
        exit;
    }

  
    $description_to_db = !empty($description) ? $description : NULL;
    
 
    $due_date_to_db = !empty($due_date) ? $due_date : NULL;
    
  
    $status = 'pending';

    
    try {
       
        $stmt = $pdo->prepare(
            "INSERT INTO tasks (user_id, title, description, due_date, status) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        $stmt->execute([
            $current_user_id,  
            $title,
            $description_to_db,
            $due_date_to_db,
            $status
        ]);

     
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Đã thêm công việc mới thành công!'
        ];

    } catch (PDOException $e) {
      
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Lỗi CSDL khi thêm công việc: ' . $e->getMessage()
        ];
    }

} else {
   
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Yêu cầu không hợp lệ.'
    ];
}


header("Location: index.php");
exit;
?>