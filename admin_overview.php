<?php
include('connect.php');

$query = 
"SELECT 
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
    `age_rating` 
ON 
    `program`.`ageRatingID` = `age_rating`.`ageRatingID`
JOIN
    `image`
ON
    `program`.`imageID` = `image`.`imageID`
JOIN 
    `category`
ON 
    `program`.`categoryID` = `category`.`categoryID`";
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
        <a href="edit.php?id=<?php echo htmlspecialchars($row['ProgramID']); ?>">Edit</a>
        <hr>
    <?php endwhile; ?>    
    <a href="index.php">Back to Index</a>
</body>
</html>