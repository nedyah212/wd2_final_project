<?php
session_start();
include('connect.php');
include('header.php');

//Gets image_sources for drop down box from db
$imagesQuery = "SELECT `imageID`, `image_source` FROM `image` WHERE(imageID > 1)";
$imagesStatement = $db->prepare($imagesQuery);
$imagesStatement->execute();
$images = $imagesStatement->fetchAll(PDO::FETCH_ASSOC);

//When delete image is clicked
if ($_POST) 
{   
    //Separates image_ID and image_source and aplies them to variables
    $selectedValue = filter_input(INPUT_POST, 'imageID', FILTER_SANITIZE_STRING);
    list($imageID, $imageSource) = explode('|', $selectedValue);

    //Sets the value of any image_ID in program matching the above image_ID to 1 (null), to prepare photo for deletion
    $updateQuery = "UPDATE `program` SET `imageID` = 1 WHERE `imageID` = :imageID";
    $updateStatement = $db->prepare($updateQuery);
    $updateStatement->bindValue(':imageID', $imageID, PDO::PARAM_STR);
    $updateStatement->execute();

    //Deletes image row from image table
    $deleteQuery = "DELETE FROM `image` WHERE `imageID` = :imageID";
    $deleteStatement = $db->prepare($deleteQuery);  
    $deleteStatement->bindValue(':imageID', $imageID, PDO::PARAM_INT);
    
    //Deletes matching image from images directory
    if ($deleteStatement->execute()) {
        if (file_exists($imageSource)) {
            unlink($imageSource);
        }

        header("Location: index.php");
        exit();
    } else {
        exit("Error deleting the image. Please try again.");
    }
}
?>

<!DOCTYPE html>
<html>
<html lang="en">
<head>
    <title>Delete Image</title>
    <link rel="stylesheet" href="_styles.css">
</head>
<body>
    <h2>Delete Image</h2>
    <form method="post" action="">
        <label for="imageID">Image</label>
        <select id="imageID" name="imageID" required>
            <option value="">Select Image</option>
            <?php foreach ($images as $image): ?>
                <option value="<?= htmlspecialchars($image['imageID'] . '|' . $image['image_source']) ?>">
                    <?= htmlspecialchars($image['image_source']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="submit" value="Delete Image">
    </form>
</body>
</html>