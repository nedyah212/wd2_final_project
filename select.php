<?php
require('connect.php');
session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    $programQuery = "
    SELECT 
        `program`.`programID` AS `ProgramID`, 
        `program`.`name` AS `Name`,
        `program`.`description` AS `Description`,  
        `age_rating`.`description` AS `Age Rating`,
        `program`.`expectedDuration` AS `Duration`, 
        `category`.`categoryName` AS `Category`, 
        `image`.`image_source` AS `Image`
    FROM 
        `program` 
    JOIN 
        `age_rating` ON `program`.`ageRatingID` = `age_rating`.`ageRatingID`
    JOIN 
        `image` ON `program`.`imageID` = `image`.`imageID`
    JOIN 
        `category` ON `program`.`categoryID` = `category`.`categoryID`
    WHERE 
        `program`.`programID` = :id
    LIMIT 1";

    $programStatement = $db->prepare($programQuery);
    $programStatement->bindValue(':id', $id, PDO::PARAM_INT);
    $programStatement->execute();
    $row = $programStatement->fetch();

    $reviewQuery = "SELECT `reviewerName`, `rating`, `reviewText` FROM `review` WHERE `programID` = :id AND `hidden`= 0";
    $reviewStatement = $db->prepare($reviewQuery);
    $reviewStatement->bindValue(':id', $id, PDO::PARAM_INT);
    $reviewStatement->execute();
    $review = $reviewStatement->fetchAll() ?: [];
} else {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewerName = filter_input(INPUT_POST, 'reviewerName', FILTER_SANITIZE_STRING);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $reviewText = filter_input(INPUT_POST, 'reviewText', FILTER_SANITIZE_STRING);

    if ($reviewerName && $rating && $reviewText) {
        $insertQuery = "INSERT INTO `review` (`programID`, `reviewerName`, `rating`, `reviewText`) VALUES (:programID, :reviewerName, :rating, :reviewText)";
        $insertStatement = $db->prepare($insertQuery);
        $insertStatement->bindValue(':programID', $id, PDO::PARAM_INT);
        $insertStatement->bindValue(':reviewerName', $reviewerName, PDO::PARAM_STR);
        $insertStatement->bindValue(':rating', $rating, PDO::PARAM_INT);
        $insertStatement->bindValue(':reviewText', $reviewText, PDO::PARAM_STR);
        $insertStatement->execute();

        header("Location: select.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="_styles.css">
    <title>Program Details</title>
</head>
<body>
    <?php include('header.php') ?>

    <?php if ($row): ?>
        <div class="program">
            <h2><?php echo htmlspecialchars($row['Name']); ?></h2>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($row['Description'])); ?></p>
            <p><strong>Age Rating:</strong> <?php echo htmlspecialchars($row['Age Rating']); ?></p>
            <p><strong>Duration:</strong> <?php echo htmlspecialchars($row['Duration']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($row['Category']); ?></p>
            <?php if ($row['Image']): ?>
                <p class="img"><img src="<?php echo htmlspecialchars($row['Image']); ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>" width="500" height="auto"></p>
            <?php endif; ?>
        </div>

        <h3>Reviews</h3>
        <?php if ($review): ?>
        <ul>
        <?php foreach ($review as $singleReview): ?>
            <li>
                <p><strong><?php echo htmlspecialchars($singleReview['reviewerName']); ?></strong> rated it <?php echo htmlspecialchars($singleReview['rating']); ?>/5</p>
                <p><?php echo htmlspecialchars($singleReview['reviewText']); ?></p>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <p>No reviews yet. Log In and Leave a Review.</p>
        <?php endif; ?>       
        <div class='review'>
            <h3>Leave a Review</h3>
            <form method="post">
                <label for="reviewerName">Your Name:</label>
                <input type="text" name="reviewerName" id="reviewerName" required>

                <label for="rating">Rating (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" required>

                <label for="reviewText">Your Review:</label>
                <textarea name="reviewText" id="reviewText" required></textarea>

                <button type="submit">Submit Review</button>
            </form>
        </div>
    <?php else: ?>
        <p>Program not found.</p>
    <?php endif; ?>

    <button class='review' onclick="history.back()">Back</button>
</body>
</html>