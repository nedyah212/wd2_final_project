<?php
include('nav.php');
require('connect.php');

$target_dir = "images/";
$output_string = "";

if (!is_dir($target_dir)) 
{
    mkdir($target_dir);
}

if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0) 
{
    $file = $_FILES['uploaded_file'];
    $fileName = basename($file["name"]);
    $target_file = $target_dir . $fileName;

    $fileType = mime_content_type($file["tmp_name"]);
    $allowedTypes = ['image/jpeg', 'image/png'];

    if (in_array($fileType, $allowedTypes)) 
    {
        if (move_uploaded_file($file["tmp_name"], $target_file)) 
        {               
            try {
                $file_info = pathinfo($target_file);
                if ($file_info['extension'] === 'png') {
                    
                    $image = imagecreatefrompng($target_file);
                    if (!$image) {
                        throw new Exception('Failed to create image from PNG.');
                    }
            
                    $jpeg_file = $file_info['dirname'] . '/' . $file_info['filename'] . '.jpg';
            
                    imagejpeg($image, $jpeg_file, 90);
                    imagedestroy($image);
            
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    } else {
                        throw new Exception('Original PNG file does not exist.');
                    }
        
                    $image = imagecreatefromjpeg($jpeg_file);
                    $width = imagesx($image);
                    $height = imagesy($image);
            
                    $new_width = 500;
                    $new_height = ($height / ($width / 500));
            
                    $new_image = imagecreatetruecolor($new_width, $new_height);
            
                    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            
                    imagejpeg($new_image, $jpeg_file);
            
                    imagedestroy($image);
                    imagedestroy($new_image);

                    $query = "INSERT INTO image (image_source) VALUES (:image_source)";
                    $statement = $db->prepare($query);

                    $dot_position = strpos($target_file, '.');
                    if($dot_position != false){
                        $target_file = substr($target_file, 0, $dot_position);
                        $target_file .= ".jpg";
                    }

                    $statement->bindValue(':image_source', $target_file);
                    $statement->execute();
            
                    $output_string = "File uploaded, and resized successfully!";
                }

                if ($file_info['extension'] === 'jpeg' || $file_info['extension'] === 'jpg') {
                    $image = imagecreatefromjpeg($jpeg_file);
                    $width = imagesx($image);
                    $height = imagesy($image);
            
                    $new_width = 500;
                    $new_height = ($height / ($width / 500));
            
                    $new_image = imagecreatetruecolor($new_width, $new_height);
            
                    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            
                    imagejpeg($new_image, $jpeg_file);
            
                    imagedestroy($image);
                    imagedestroy($new_image);

                    $query = "INSERT INTO image (image_source) VALUES (:image_source)";
                    $statement = $db->prepare($query);
                    $statement->bindValue(':image_source', $target_file);
                    $statement->execute();
            
                    $output_string = "File uploaded, and resized successfully!";
                }
            } catch (Exception $e) {
                $output_string = "Error: " . $e->getMessage();}}
        else{$output_string = "Error moving file to upload directory.";}} 
    else {$output_string = "Invalid file type.";}}
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