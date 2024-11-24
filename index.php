<?php
session_start();
$_SESSION['role'] = ''; // Login in logic needs to be added here

require('connect.php');

$height = "auto";
$width = 500;

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
    `program`.`categoryID` = `category`.`categoryID`
";

$statement = $db->prepare($query);
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
    <?php while($row = $statement->fetch()): ?>
        <div>
            <div class="program">
                <h2><a href="select.php?id=<?php echo $row['ProgramID']; ?>"><?php echo htmlspecialchars($row['Name']); ?></a></h2>
                <p><strong>Description: </strong><?php echo nl2br(wordwrap(html_entity_decode($row['Description']), 60,  "\n" . str_repeat("&nbsp;", 22))); ?></p>
                <p><strong>Age Rating:</strong> <?php echo htmlspecialchars($row['Age Rating']); ?></p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($row['Duration']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($row['Category']); ?></p>
                <p class="img"><img src="<?php echo htmlspecialchars($row['Image']); ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>" width = "<?php echo $width ?>" height = "<?php echo $height ?>"></p>
                <?php 
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): 
                ?>
                <a href="edit.php?id=<?php echo $row['ProgramID']; ?>">Edit</a>
                <?php 
                endif; 
                ?>
            </div>
        </div>
        <br>
    <?php endwhile ?>
</body>
</html>