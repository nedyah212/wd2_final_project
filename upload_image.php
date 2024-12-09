<?php
session_start();
require('connect.php');
include('header.php');
include('functions.php');


$target_dir = "images/";
$output_string = "";

if (!is_dir($target_dir)) {
    mkdir($target_dir);
}

if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0) {
    $file = $_FILES['uploaded_file'];
    $file_name = basename($file["name"]);
    $target_file = $target_dir . $file_name;

    $fileType = mime_content_type($file["tmp_name"]);
    $allowedTypes = ['image/jpeg', 'image/png'];

    if (in_array($fileType, $allowedTypes)) {
        
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            try {
                
                if ($fileType == 'image/png') {
                    $jpeg_file = convertToJpg($file_name, $target_dir, $target_file);
                }
                
                if ($fileType == 'image/jpeg') {
                    $jpeg_file = $target_file;
                }

                jpegResize($jpeg_file);

                $query = "INSERT INTO image (image_source) VALUES (:image_source)";
                $statement = $db->prepare($query);
                $statement->bindValue(':image_source', $jpeg_file);
                $statement->execute();

                $output_string = "File uploaded, and resized successfully!";
            } catch (Exception) {
                $output_string = "Error";
            }
        } else {
            $output_string = "Error moving file to upload directory.";
        }
    } else {
        $output_string = "Invalid file type.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin File Upload</title>
</head>
<body>
    <h1>Admin File Upload: </h1>
    <link rel="stylesheet" href="_styles.css">
    <form method="post" enctype="multipart/form-data">
        <label for="uploaded_file"><strong>Choose Image File (Blank Row For No Image):</strong></label>
        <br><br>
        <input type="file" name="uploaded_file" id="uploaded_file" />
        <br><br>
        <input type="submit" name="submit" value="Upload Image" />
        <p><?= $output_string ?></p>
    </form>
</body>
</html>