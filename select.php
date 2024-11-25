<?php
require('connect.php');
session_start();

// Set the default image dimensions
$height = "auto";
$width = 500;

// Get the program ID from the query string (e.g., select.php?id=1)
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// If an ID is provided, fetch the details for that program.
if ($id) {
    $query = "
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
        `program`.`categoryID` = `category`.`categoryID`
    WHERE 
        `program`.`programID` = :id
    LIMIT 1"; // To ensure we only get one program

    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);  // Bind the ID parameter
    $statement->execute();

    $row = $statement->fetch();
} else {
    // If no id is provided, redirect or show an error
    header('Location: index.php'); // Redirect back to index page if no ID
    exit;
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
    <?php include('header.php')?> 

    <?php if ($row): ?>
        <div class="program">
            <h2><?php echo htmlspecialchars($row['Name']); ?></h2>
            <p><strong>Description: </strong><?php echo nl2br(wordwrap(html_entity_decode($row['Description']), 60,  "\n" . str_repeat("&nbsp;", 22))); ?></p>
            <p><strong>Age Rating:</strong> <?php echo htmlspecialchars($row['Age Rating']); ?></p>
            <p><strong>Duration:</strong> <?php echo htmlspecialchars($row['Duration']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($row['Category']); ?></p>
            <p class="img">
                <img src="<?php echo htmlspecialchars($row['Image']); ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>" width = "<?php echo $width ?>" height = "<?php echo $height ?>">
            </p>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="edit.php?id=<?php echo $row['ProgramID']; ?>">Edit</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Program not found.</p>
    <?php endif; ?>

    <form method="post" action="index.php">
        <br>
        <button type="submit">Back</button>
    </form>
</body>
</html>