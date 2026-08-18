<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/upload_error.log');
error_reporting(E_ALL);

require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'list_categories') {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_id = $_POST['category_id'] ?? '';
    $new_cat = trim($_POST['new_category'] ?? '');
    $file = $_FILES['file'] ?? null;

   if (!$file || $file['error'] !== 0) {
    $errorCode = $file['error'] ?? 'N/A';
    $errorMsg = 'File upload error (Code: ' . $errorCode . '). This is often caused by a file being too large for the server to accept (php.ini limits).';

    if ($errorCode == 1) { $errorMsg = 'File size exceeds php.ini upload_max_filesize.'; }
    if ($errorCode == 3) { $errorMsg = 'File was only partially uploaded.'; }
    if ($errorCode == 4) { $errorMsg = 'No file was uploaded.'; }
    
    echo json_encode(['status'=>'error','msg'=> $errorMsg]);
    exit;
}

    if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'pdf') {
        echo json_encode(['status'=>'error','msg'=>'Only PDF files allowed.']);
        exit;
    }

    // --- START: Handle category ---
$category_message = ''; // Variable to store feedback for the user

if ($new_cat !== '') {
    // 1. CHECK FOR EXISTING CATEGORY (Securely)
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$new_cat]);
    $existing_id = $stmt->fetchColumn();

    if ($existing_id) {
        // Category exists: Use the existing ID and set a message.
        $cat_id = $existing_id;
        $category_message = " (Note: Category '$new_cat' already existed.)";
    } else {
        // Category does not exist: Insert it.
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        if ($stmt->execute([$new_cat])) {
            $cat_id = $pdo->lastInsertId();
        } else {
            // Fallback for an unlikely database failure
            echo json_encode(['status'=>'error','msg'=>'Failed to create new category due to DB error.']);
            exit;
        }
    }
}

if (!$cat_id) {
    echo json_encode(['status'=>'error','msg'=>'Please select or add a category.']);
    exit;
}

    $folder = __DIR__ . '/pdfs';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $safeName = uniqid('file_', true) . '.pdf';
    move_uploaded_file($file['tmp_name'], "$folder/$safeName");

    $title = pathinfo($file['name'], PATHINFO_FILENAME);
    $stmt = $pdo->prepare("INSERT INTO pdf_files (category_id, filename, title) VALUES (?, ?, ?)");
    $stmt->execute([$cat_id, $safeName, $title]);

    echo json_encode(['status'=>'success','msg'=>'File uploaded successfully.']);
    exit;
}

// --- NEW ACTION: list_files ---
if ($action === 'list_files') {
    // Select files and the associated category name
    $stmt = $pdo->query("
        SELECT 
            p.id, 
            p.title, 
            c.name AS category_name
        FROM pdf_files p
        JOIN categories c ON p.category_id = c.id
        ORDER BY p.id DESC
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// --- NEW ACTION: delete_file ---
if ($action === 'delete_file' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_id = $_POST['file_id'] ?? 0;

    // 1. Get filename to delete from disk
    $stmt = $pdo->prepare("SELECT filename FROM pdf_files WHERE id = ?");
    $stmt->execute([$file_id]);
    $filename = $stmt->fetchColumn();

    if ($filename) {
        // 2. Delete file from disk
        $file_path = __DIR__ . '/pdfs/' . $filename;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // 3. Delete record from database
        $stmt = $pdo->prepare("DELETE FROM pdf_files WHERE id = ?");
        $stmt->execute([$file_id]);

        echo json_encode(['status' => 'success', 'msg' => 'File deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'File not found in database.']);
    }
    exit;
}

echo json_encode(['status'=>'error','msg'=>'Invalid request.']);
