<?php
require('connect.php');
session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM program WHERE programID = :id LIMIT 1";
$statement = $db->prepare($query);
$statement->bindValue('id', $id, PDO::PARAM_INT);
$statement->execute();
$row = $statement->fetch();

if ($_POST) {
    $name = filter_input(INPUT_POST, 'nameEdit', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $ageRatingID = filter_input(INPUT_POST, 'ageRatingID', FILTER_SANITIZE_NUMBER_INT);
    $categoryID = filter_input(INPUT_POST, 'categoryID', FILTER_SANITIZE_NUMBER_INT);
    $imageID = filter_input(INPUT_POST, 'imageID', FILTER_SANITIZE_NUMBER_INT);
    $expectedDuration = filter_input(INPUT_POST, 'expectedDuration', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if($name == "" || $description == "" || $ageRatingID == "" || $categoryID == "" || $imageID == "" || $expectedDuration == "") {
        exit("All fields must be filled in with at least one character! Please try again.");
    }

    $query = "UPDATE program SET 
                name = :name, 
                description = :description, 
                ageRatingID = :ageRatingID, 
                categoryID = :categoryID, 
                imageID = :imageID, 
                expectedDuration = :expectedDuration 
              WHERE programID = :id";
    $stmt = $db->prepare($query);

    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':ageRatingID', $ageRatingID, PDO::PARAM_INT);
    $stmt->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
    $stmt->bindValue(':imageID', $imageID, PDO::PARAM_INT);
    $stmt->bindValue(':expectedDuration', $expectedDuration);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {   
        header("Location: index.php?id=$id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program</title>
</head>
<body>

    <h2>Edit Program</h2>

    <form method="post">
        <label for="nameEdit">Edit Program Name</label><br>
        <input type="text" name="nameEdit" value="<?php echo htmlspecialchars(html_entity_decode($row['name'])) ?>" required><br><br>

        <label for="description">Edit Description</label><br>
        <textarea name="description" style="width: 300px; height: 150px;" required><?php echo htmlspecialchars($row['description']) ?></textarea><br><br>

        <label for="ageRatingID">Edit Age Rating</label><br>
        <select name="ageRatingID" required>
            <?php

            $query = "SELECT * FROM age_rating";
            $ageRatings = $db->query($query);
            while ($ageRow = $ageRatings->fetch()) {
                $selected = ($ageRow['ageRatingID'] == $row['ageRatingID']) ? 'selected' : '';
                echo "<option value='" . $ageRow['ageRatingID'] . "' $selected>" . htmlspecialchars($ageRow['description']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="categoryID">Edit Category</label><br>
        <select name="categoryID" required>
            <?php

            $query = "SELECT * FROM category";
            $categories = $db->query($query);
            while ($categoryRow = $categories->fetch()) {
                $selected = ($categoryRow['categoryID'] == $row['categoryID']) ? 'selected' : '';
                echo "<option value='" . $categoryRow['categoryID'] . "' $selected>" . htmlspecialchars($categoryRow['categoryName']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="imageID">Edit Image</label><br>
        <select name="imageID" required>
            <?php

            $query = "SELECT * FROM image";
            $images = $db->query($query);
            while ($imageRow = $images->fetch()) {
                $selected = ($imageRow['imageID'] == $row['imageID']) ? 'selected' : '';
                echo "<option value='" . $imageRow['imageID'] . "' $selected>" . htmlspecialchars($imageRow['image_source']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="expectedDuration">Edit Expected Duration</label><br>
        <input type="text" name="expectedDuration" value="<?php echo htmlspecialchars($row['expectedDuration']) ?>" required><br><br>

        <input type="submit" value="Update Program">
    </form>

    <form method="post" action="delete.php?id=<?php echo $id ?>">
        <br>    
        <button type="submit" onclick="return confirm('Are you sure you want to delete this program?');">Delete Program</button>
    </form>

    <form method="post" action="index.php">
        <br>
        <button type="submit">Back</button>
    </form>
</body>
</html>