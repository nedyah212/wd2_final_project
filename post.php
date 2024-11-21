<?php
    require('connect.php');
    require('authenticate.php');
    
    $display = "none";

    $ageRatingsQuery = "SELECT `ageRatingID`, `description` FROM `age_rating`";
    $ageRatingsStatement = $db->prepare($ageRatingsQuery);
    $ageRatingsStatement->execute();
    $ageRatings = $ageRatingsStatement->fetchAll(PDO::FETCH_ASSOC);

    $categoriesQuery = "SELECT `categoryID`, `categoryName` FROM `category`";
    $categoriesStatement = $db->prepare($categoriesQuery);
    $categoriesStatement->execute();
    $categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);

    $imagesQuery = "SELECT `imageID`, `image_source` FROM `image`";
    $imagesStatement = $db->prepare($imagesQuery);
    $imagesStatement->execute();
    $images = $imagesStatement->fetchAll(PDO::FETCH_ASSOC);

    if ($_POST) {    
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $ageRatingID = filter_input(INPUT_POST, 'ageRatingID', FILTER_SANITIZE_NUMBER_INT);
        $categoryID = filter_input(INPUT_POST, 'categoryID', FILTER_SANITIZE_NUMBER_INT);
        $imageID = filter_input(INPUT_POST, 'imageID', FILTER_SANITIZE_NUMBER_INT);
        $expectedDuration = filter_input(INPUT_POST, 'expectedDuration', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if($name == "" || $description == "" || $ageRatingID == "" || $categoryID == "" || $imageID == "" || $expectedDuration == "") {
            exit("All fields must be filled in with at least one character! Please try again.");
        }

        $query = "INSERT INTO program (name, description, ageRatingID, categoryID, imageID, expectedDuration) 
                  VALUES (:name, :description, :ageRatingID, :categoryID, :imageID, :expectedDuration)";
        $statement = $db->prepare($query);

        $statement->bindValue(':name', $name);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':ageRatingID', $ageRatingID, PDO::PARAM_INT);
        $statement->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
        $statement->bindValue(':imageID', $imageID, PDO::PARAM_INT);
        $statement->bindValue(':expectedDuration', $expectedDuration);

        if ($statement->execute()) {
            header("Location: index.php");
            exit();
        } else {
            exit("Error inserting program data. Please try again.");
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert Program</title>
</head>
<body>
    <?php include('nav.php'); ?>

    <h2>Add New Program</h2>
    <form method="post" action="post.php">
        <label for="name">Program Name</label>
        <input type="text" id="name" name="name" required><br>

        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="ageRatingID">Age Rating</label>
        <select id="ageRatingID" name="ageRatingID" required>
            <option value="">Select Age Rating</option>
            <?php foreach ($ageRatings as $rating): ?>
                <option value="<?= htmlspecialchars($rating['ageRatingID']) ?>">
                    <?= htmlspecialchars($rating['description']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="categoryID">Category</label>
        <select id="categoryID" name="categoryID" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['categoryID']) ?>">
                    <?= htmlspecialchars($category['categoryName']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="imageID">Image</label>
        <select id="imageID" name="imageID" required>
            <option value="">Select Image</option>
            <?php foreach ($images as $image): ?>
                <option value="<?= htmlspecialchars($image['imageID']) ?>">
                    <?= htmlspecialchars($image['image_source']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="expectedDuration">Expected Duration</label>
        <input type="text" id="expectedDuration" name="expectedDuration" required><br>

        <br>
        <input type="submit" value="Add Program">
    </form>

    <form method="post" action="index.php">
        <br>    
        <button type="submit">Cancel</button>
    </form>
</body>
</html>