<?php 

//Converts png to jpeg for use in gd library
function convertToJpg($file_name, $target_dir, $target_file)
{   
    $jpeg_file = $target_dir . pathinfo($file_name, PATHINFO_FILENAME) . '.jpg';
    $image = imagecreatefrompng($target_file);
    
    if (!$image) {
        throw new Exception('Failed to create image from PNG.');
    }

    imagejpeg($image, $jpeg_file, 90);
    imagedestroy($image);

    if (file_exists($target_file)) {
        unlink($target_file);
    } else {
        throw new Exception('Original PNG file does not exist.');
    }

    $target_file = $jpeg_file;
    return $target_file;
}

//Resizes image to 500 wide while maintaining aspect ratio
//note: gd needs to be enabled in xampp/php/php.ini 
function jpegResize($jpeg_file)
{
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
}
?>