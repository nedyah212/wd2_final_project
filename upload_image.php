<?php
include('nav.php');
require('connect.php');  // Assuming this is where your database connection is

$target_dir = "images/";
$output_string = "";

if (!is_dir($target_dir)) {
    mkdir($target_dir);
}

if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0) {
    $file = $_FILES['uploaded_file'];
    $fileName = basename($file["name"]);
    $target_file = $target_dir . $fileName;

    $fileType = mime_content_type($file["tmp_name"]);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (in_array($fileType, $allowedTypes)) {
        // Move the file to the target directory
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $output_string = "File uploaded successfully!";
            
            // Insert into the `image` table
            try {
                $query = "INSERT INTO image (image_source) VALUES (:image_source)";
                $statement = $db->prepare($query);
                $statement->bindValue(':image_source', $target_file);
                $statement->execute();

                // Optionally, you can get the last inserted imageID
                $imageID = $db->lastInsertId();
                $output_string .= " Image record added to the database with ID: $imageID";
            } catch (PDOException $e) {
                $output_string = "Error inserting image into the database: " . $e->getMessage();
            }
        } else {
            $output_string = "Error moving file to upload directory.";
        }
    } else {
        $output_string = "Invalid file type. Only JPEG, PNG, and GIF files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>
    <h1>Image Upload</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="uploaded_file">Choose Image File:</label>
        <input type="file" name="uploaded_file" id="uploaded_file" />
        <br />
        <input type="submit" name="submit" value="Upload Image" />
        <p><?= $output_string ?></p>
    </form>
</body>
</html>