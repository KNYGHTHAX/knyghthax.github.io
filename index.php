<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Leads</title>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .upload-form {
        text-align: center;
    }

    .upload-form input[type="file"] {
        display: none;
    }

    .upload-btn {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .upload-btn:hover {
        background-color: #0056b3;
    }

    .file-label {
        font-size: 16px;
        margin-top: 10px;
        display: block;
    }

    .file-name {
        font-size: 14px;
        color: #555;
    }

    .success-message {
        color: green;
        text-align: center;
        margin-top: 20px;
    }
</style>
</head>
<body>
    <div class="container">
        <h2>Upload Leads List</h2>
        
        <form action="process_leads.php" method="post" enctype="multipart/form-data" class="upload-form">
            <label for="fileToUpload" class="upload-btn">Select TXT file</label><br>
            <input required  type="file" name="fileToUpload" id="fileToUpload" onchange="updateFileName(this)"><br>
            <span class="file-label" id="fileNameLabel">No file selected</span><br>
            <input type="checkbox" id="validateLeads" name="validateLeads">
            <label for="validateLeads">Validate Leads</label><br><br>
            <label for="botToken">Telegram Bot Token:</label><br><br>
            <input type="text" id="botToken" name="botToken" required><br><br>
            <label for="chatId">Telegram Chat ID:</label><br><br>
            <input type="text" id="chatId" name="chatId" required><br><br>
            <input type="submit" value="Filter" name="submit" class="upload-btn"><br>
        </form>
    </div>
<?php
session_start(); // Start the session
if(isset($_SESSION['success_message'])) {
    echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Remove the message from session
}
?>
<script>
    function updateFileName(input) {
        var fileName = input.files[0].name;
        document.getElementById("fileNameLabel").innerHTML = fileName;
    }
</script>
</body>
</html>
