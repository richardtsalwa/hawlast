<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/upload_php_error.log');
error_reporting(E_ALL);

if (!isset($_GET['lish']) || $_GET['lish'] !== '2025') {
    http_response_code(403);
    exit('<h3 style="color:red;text-align:center;">Access denied.</h3>');
}

require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Upload </title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body { font-family: Arial; margin:20px; background:#f9f9f9; }
.container { max-width:500px; margin:auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
input, select, button { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:5px; }
button { background:#2c7; color:white; cursor:pointer; border:none; }
button:hover { background:#28a745; }
#msg { margin-top:10px; font-weight:bold; }

/* New CSS for File List */
.file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #eee;
}
.file-item:hover {
    background: #f0f0f0;
}
.file-info {
    flex-grow: 1;
}
.delete-btn {
    background: none;
    border: none;
    color: red;
    font-weight: bold;
    cursor: pointer;
    font-size: 16px;
    padding: 5px 10px;
    margin-left: 10px;
}
.delete-btn:hover {
    color: darkred;
}

</style>
</head>
<body>
<div class="container">
  <h3>Upload PDF or Visit <a href="https://www.hawlast.com/lish/">Home Page</a></a></h3>

  <label>Select Category</label>
  <select id="category">
    <option value="">-- Select Existing --</option>
  </select>

  <label>Or Add New Category</label>
  <input type="text" id="new-category" placeholder="e.g. PDFs">

  <label>Choose PDF</label>
  <input type="file" id="pdf-file" accept="application/pdf">

  <button id="upload-btn">Upload</button>

  <div id="msg"></div>
</div>

<div class="container" style="margin-top: 20px;">
    <h3>Existing Files</h3>
    <ul id="file-list" style="list-style: none; padding: 0;">
        </ul>
</div>

<script>
$(function(){
    // Load categories
    // The 'cats' argument is ALREADY the parsed JSON object (array)
    $.get('upload_ajax.php?action=list_categories', function(cats){ 
        // No JSON.parse needed here
        // We use 'cats' directly since jQuery parsed it.
        $('#category').append(cats.map(c => `<option value="${c.id}">${c.name}</option>`));
    }).fail(function(xhr, status, error) { 
        // Add a failure handler to catch server issues
        console.error('Failed to load categories:', status, error, xhr.responseText);
    });

    $('#upload-btn').click(function(){
        const file = $('#pdf-file')[0].files[0];
        const catId = $('#category').val();
        const newCat = $('#new-category').val().trim();
        if(!file) return $('#msg').text('Please choose a PDF.');
        if(!file.name.endsWith('.pdf')) return $('#msg').text('Only PDF files are allowed.');

        const formData = new FormData();
        formData.append('file', file);
        formData.append('category_id', catId);
        formData.append('new_category', newCat);

        $('#msg').text('Uploading...');
        $.ajax({
            url: 'upload_ajax.php?action=upload',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', 
            timeout: 120000,
            success: function(res){ 
                // NO JSON.parse or try/catch needed here either.
                $('#msg').css('color', res.status === 'success' ? 'green' : 'red').text(res.msg);
                if(res.status === 'success') $('#pdf-file').val('');
            },
  
  error: function(xhr, status, error) {
           let errorMsg = 'Upload failed: ';

                if (xhr.status === 0) {
                    errorMsg += 'Network or Connection Blocked (Status 0).';
                } else if (xhr.status === 413) {
                    errorMsg += 'File Too Large (Error 413). Check server upload limits.';
                } else if (xhr.status === 500) {
                    errorMsg += 'Internal Server Error (500). Check your PHP code and server logs.';
                } else if (xhr.responseText && xhr.responseText.length > 0) {
                    // Capture non-JSON output (like HTML error page from the server)
                    errorMsg += 'Server returned non-JSON data. First 50 chars: "' + xhr.responseText.substring(0, 50) + '..."';
                } else {
                    errorMsg += status + ' (' + xhr.status + ')';
                }

             $('#msg').text(errorMsg).css('color', 'red');
             console.error('AJAX Upload Error Details:', xhr, status, error);
           },
complete: function() {
                uploadButton.prop('disabled', false).text('Upload');
            }


        });
    });
});
</script>
<script>
$(function(){
    console.log('Document Ready: jQuery is initialized.');

    // --- NEW FUNCTION: Loads the file list ---
    function loadFiles() {
        $.get('upload_ajax.php?action=list_files', function(files){
            const listHtml = files.map(file => `
                <li class="file-item" data-id="${file.id}">
                    <div class="file-info">${file.title} (${file.category_name})</div>
                    <button class="delete-btn" data-file-id="${file.id}">X</button>
                </li>
            `).join('');
            $('#file-list').html(listHtml || '<li>No files uploaded yet.</li>');
        }, 'json').fail(function(xhr, status, error) {
            console.error('Failed to load files:', status, error, xhr.responseText);
        });
    }

    // --- NEW FUNCTION: Handles file deletion ---
    function deleteFile(fileId) {
        if (!confirm('Are you sure you want to delete this file? This action is permanent.')) {
            return;
        }

        $.ajax({
            url: 'upload_ajax.php?action=delete_file',
            method: 'POST',
            dataType: 'json',
            data: { file_id: fileId },
            success: function(res) {
                if (res.status === 'success') {
                    alert(res.msg);
                    loadFiles(); // Reload the list on success
                } else {
                    alert('Deletion failed: ' + res.msg);
                }
            },
            error: function() {
                alert('Server error during deletion.');
            }
        });
    }

    // --- EXISTING CODE MODIFIED: Load categories and initial files ---
    $.get('upload_ajax.php?action=list_categories', function(cats){ 
        $('#category').append(cats.map(c => `<option value="${c.id}">${c.name}</option>`));
        loadFiles(); // Load files immediately after categories are handled
    }).fail(function(xhr, status, error) { 
        console.error('Failed to load categories:', status, error, xhr.responseText);
    });
    
    // --- EXISTING CODE MODIFIED: Upload logic to refresh list ---
    $('#upload-btn').click(function(){
        const file = $('#pdf-file')[0].files[0];
        const catId = $('#category').val();
        const newCat = $('#new-category').val().trim();
        if(!file) return $('#msg').text('Please choose a PDF.');
        if(!file.name.endsWith('.pdf')) return $('#msg').text('Only PDF files are allowed.');

        const formData = new FormData();
        formData.append('file', file);
        formData.append('category_id', catId);
        formData.append('new_category', newCat);

        $('#msg').text('Uploading...');
        $.ajax({
            url: 'upload_ajax.php?action=upload',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res){
                $('#msg').css('color', res.status === 'success' ? 'green' : 'red').text(res.msg);
                if(res.status === 'success') {
                    $('#pdf-file').val('');
                    loadFiles(); // <<< REFRESH LIST ON SUCCESSFUL UPLOAD
                }
            },
            error: function(xhr, status, error) {
                $('#msg').text('Upload failed: ' + status);
                console.error('Upload error:', xhr, status, error);
            }
        });
    });

    // --- NEW EVENT DELEGATION: Handle delete button clicks ---
    $('#file-list').on('click', '.delete-btn', function() {
        const fileId = $(this).data('file-id');
        deleteFile(fileId);
    });
});
</script>

</body>
</html>
