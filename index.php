<?php
require('connect.php');
session_start();

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

if (isset($_GET['category']) && $_GET['category'] !== 'all') {
    $category = htmlspecialchars($_GET['category']);
    $query .= " WHERE `category`.`categoryName` = :category";
}

$statement = $db->prepare($query);


if (isset($category)) {
    $statement->bindParam(':category', $category);
}

$statement->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="_styles.css">
    <title>Try A Holosuite at Quark's!</title>
</head>
<body>
<?php include('header.php')?>
<br>
<div class="category">
    <form method="get" action="index.php">
        <input type="hidden" name="category" value="all">
        <input type="submit" value="All">
    </form>
    <form method="get" action="index.php">
        <input type="hidden" name="category" value="historical">
        <input type="submit" value="Historical">
    </form>
    <form method="get" action="index.php">
        <input type="hidden" name="category" value="training">
        <input type="submit" value="Training">
    </form>
    <form method="get" action="index.php">
        <input type="hidden" name="category" value="combat training">
        <input type="submit" value="Combat">
    </form>
    <form method="get" action="index.php">
        <input type="hidden" name="category" value="spirituality">
        <input type="submit" value="Spirituality">
    </form>
    <form method="get" action="index.php">
        <input type="hidden" name="category" value="entertainment">
        <input type="submit" value="Entertainment">
    </form>
    <form method="get" action="index.php">
        <input type="hidden" name="category" value="adult encounters">
        <input type="submit" value="Adult">
    </form>
    <form method="get" action="index.php">
        <input type="hidden" name="category" value="group activities">
        <input type="submit" value="Group">
    </form>
    </div>
        <?php while ($row = $statement->fetch()): ?>
    <div>
        <div class="program">
        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'user')): ?>
            <h2><a href="select.php?id=<?php echo urlencode($row['ProgramID']); ?>"><?php echo htmlspecialchars($row['Name']); ?></a></h2>
        <?php else: ?>
            <h2><?php echo htmlspecialchars($row['Name']); ?></h2>
        <?php endif; ?>
        <p><strong>Description: </strong><?php echo nl2br(wordwrap(html_entity_decode($row['Description']), 60,  "\n" . str_repeat("&nbsp;", 22))); ?></p>
        <p><strong>Age Rating:</strong> <?php echo htmlspecialchars($row['Age Rating']); ?></p>
        <p><strong>Duration:</strong> <?php echo htmlspecialchars($row['Duration']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['Category']); ?></p>
        <?php if ($row['Image']): ?>
            <p class="img"><img src="<?php echo htmlspecialchars($row['Image']); ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>"></p>
        <?php endif; ?>
        </div>
</div>
<br>
<?php endwhile ?>
</body>
</html>