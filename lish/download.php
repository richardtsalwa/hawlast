<?php
// download.php
require_once 'config.php';

$token = $_GET['token'] ?? null;
if (!$token) { http_response_code(400); echo "Missing token."; exit; }

$stmt = $pdo->prepare("SELECT dt.*, p.email, p.phone FROM download_tokens dt JOIN purchases p ON dt.purchase_id = p.id WHERE dt.token = ? LIMIT 1");
$stmt->execute([$token]);
$rec = $stmt->fetch();

if (!$rec) { http_response_code(404); echo "Invalid token."; exit; }

if ($rec['expires_at'] && (new DateTime($rec['expires_at']) < new DateTime())) {
    http_response_code(403);
    echo "This download link has expired.";
    exit;
}

if ($rec['downloads'] >= $rec['download_limit']) {
    http_response_code(403);
    echo "Download limit reached for this link.";
    exit;
}

// data is JSON list of filenames
$files = json_decode($rec['data'], true);
if (!is_array($files) || count($files) == 0) {
    echo "No files available.";
    exit;
}

// If multiple files: create zip stream on-the-fly or prompt to download a zip.
// We'll create a temporary zip file and send it (safer/compatible).
$zipname = tempnam(sys_get_temp_dir(), 'dlzip_') . '.zip';
$zip = new ZipArchive();
if ($zip->open($zipname, ZipArchive::CREATE) !== TRUE) {
    http_response_code(500);
    echo "Failed to create zip.";
    exit;
}

foreach ($files as $file) {
    // Both protections:
    $path = realpath(__DIR__ . '/' . $file);
    $basedir = realpath(__DIR__ . '/pdfs');
    if (!$path || strpos($path, $basedir) !== 0) {
        continue; // skip invalid or outside files
    }
    $zip->addFile($path, basename($path));
}
$zip->close();

// Update downloads counter
$stmt = $pdo->prepare("UPDATE download_tokens SET downloads = downloads + 1 WHERE id = ?");
$stmt->execute([$rec['id']]);

// deliver the zip
if (!file_exists($zipname)) { http_response_code(500); echo "Zip missing."; exit; }
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="notes_download.zip"');
header('Content-Length: ' . filesize($zipname));
readfile($zipname);
unlink($zipname);
exit;
