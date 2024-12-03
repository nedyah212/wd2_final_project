<?php
include('connect.php');
session_start();
include('header.php');


$sortColumn = isset($_POST['sortColumn']) ? $_POST['sortColumn'] : 'program.name';
$sortOrder = isset($_POST['sortOrder']) ? $_POST['sortOrder'] : 'ASC';

$query = "
SELECT 
    `program`.`programID` AS `ProgramID`, 
    `program`.`name` AS `Name`,
    `program`.`description` AS `Description`,  
    `age_rating`.`description` AS `Age Rating`,
    `program`.`expectedDuration` AS `Duration`, 
    `category`.`categoryName` AS `Category`, 
    `image`.`image_source` AS `Image`,
    `review`.`reviewerName` AS `Reviewer`, 
    `review`.`reviewText` AS `Review` ,
    `review`.`hidden` AS `Hidden`,
    `review`.`reviewID` AS `ReviewID`
FROM 
    `program`
JOIN 
    `age_rating` ON `program`.`ageRatingID` = `age_rating`.`ageRatingID`
JOIN
    `image` ON `program`.`imageID` = `image`.`imageID`
JOIN 
    `category` ON `program`.`categoryID` = `category`.`categoryID`
LEFT JOIN 
    `review` ON `program`.`programID` = `review`.`programID`
ORDER BY $sortColumn $sortOrder"; 

$statement = $db->prepare($query);
$statement->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Overview</title>
</head>
<body>
    <h3>Admin Overview</h3>

    <form action="admin_overview.php" method="post">
        <label for="sortColumn">Sort by:</label>
        <select name="sortColumn" id="sortColumn">
            <option value="program.name" <?php echo ($sortColumn == 'program.name') ? 'selected' : ''; ?>>Title</option>
            <option value="age_rating.description" <?php echo ($sortColumn == 'age_rating.description') ? 'selected' : ''; ?>>Age Rating</option>
            <option value="category.categoryName" <?php echo ($sortColumn == 'category.categoryName') ? 'selected' : ''; ?>>Category</option>
        </select>

        <label for="sortOrder">Order:</label>
        <select name="sortOrder" id="sortOrder">
            <option value="ASC" <?php echo ($sortOrder == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
            <option value="DESC" <?php echo ($sortOrder == 'DESC') ? 'selected' : ''; ?>>Descending</option>
        </select>

        <button type="submit">Sort</button>
    </form>

    <hr>

    <?php 
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)): ?>
        <h2>
            <a href="select.php?id=<?php echo htmlspecialchars($row['ProgramID']); ?>">
                <?php echo htmlspecialchars($row['Name'] ?? 'No Name'); ?>
            </a>
        </h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($row['Description']); ?></p>
        <p><strong>Age Rating:</strong> <?php echo htmlspecialchars($row['Age Rating']); ?></p>
        <p><strong>Duration:</strong> <?php echo htmlspecialchars($row['Duration']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['Category']); ?></p>
        
        <?php if ($row['Reviewer'] && $row['Review']): ?>
            <h4>User Reviews:</h4>
            <p><strong><?php echo htmlspecialchars($row['Reviewer']); ?> says:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($row['Review'])); ?></p>

            <form action="admin_overview.php" method="post">
                <input type="hidden" name="reviewID" value="<?php echo $row['ReviewID']; ?>">
                <label for="hiddenStatus<?php echo $row['ReviewID']; ?>">Review Visibility:</label>
                <select name="hiddenStatus" id="hiddenStatus<?php echo $row['ReviewID']; ?>" onchange="this.form.submit()">
                    <option value="0" <?php echo ($row['Hidden'] == 0) ? 'selected' : ''; ?>>Visible</option>
                    <option value="1" <?php echo ($row['Hidden'] == 1) ? 'selected' : ''; ?>>Hidden</option>
                </select>
            </form>

            <form action="admin_overview.php" method="post" onsubmit="return confirm('Are you sure you want to delete this review?');">
                <input type="hidden" name="deleteReviewID" value="<?php echo $row['ReviewID']; ?>">
                <button type="submit">Delete Review</button>
            </form>

        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
        
        <a href="edit.php?id=<?php echo htmlspecialchars($row['ProgramID']); ?>">Edit</a>
        <hr>
    <?php endwhile; ?>    

    <a href="index.php">Back to Index</a>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reviewID']) && isset($_POST['hiddenStatus'])) {
        $reviewID = (int) $_POST['reviewID'];
        $hiddenStatus = (int) $_POST['hiddenStatus'];

        $updateQuery = "UPDATE review SET hidden = :hiddenStatus WHERE reviewID = :reviewID";
        $updateStatement = $db->prepare($updateQuery);
        $updateStatement->bindValue(':hiddenStatus', $hiddenStatus, PDO::PARAM_INT);
        $updateStatement->bindValue(':reviewID', $reviewID, PDO::PARAM_INT);
        $updateStatement->execute();

        header('Location: admin_overview.php');
        exit;
    }

    if (isset($_POST['deleteReviewID'])) {
        $deleteReviewID = (int) $_POST['deleteReviewID'];

        $deleteQuery = "DELETE FROM review WHERE reviewID = :reviewID";
        $deleteStatement = $db->prepare($deleteQuery);
        $deleteStatement->bindValue(':reviewID', $deleteReviewID, PDO::PARAM_INT);
        $deleteStatement->execute();

        header('Location: admin_overview.php');
        exit;
    }
}
?>