<?php
require('connect.php');
require('authenticate.php');

$imagesQuery = "SELECT `imageID`, `image_source` FROM `image`";
$imagesStatement = $db->prepare($imagesQuery);
$imagesStatement->execute();
$images = $imagesStatement->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    

    $selectedValue = filter_input(INPUT_POST, 'imageID', FILTER_SANITIZE_STRING);

    list($imageID, $imageSource) = explode('|', $selectedValue);

    $deleteQuery = "DELETE FROM `image` WHERE `imageID` = :imageID";
    $deleteStatement = $db->prepare($deleteQuery);  
    $deleteStatement->bindValue(':imageID', $imageID, PDO::PARAM_INT);
    
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
</head>
<body>
    <?php include('nav.php'); ?>

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

    <form method="get" action="index.php">
        <button type="submit">Cancel</button>
    </form>
</body>
</html>