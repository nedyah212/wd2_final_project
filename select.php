<?php
require('connect.php');

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM blog WHERE id = :id LIMIT 1";
$statement = $db->prepare($query);
$statement->bindValue('id', $id, PDO::PARAM_INT);
$statement->execute();
$row = $statement->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include('nav.php'); ?>
    <?php if($row): ?>
        <?php echo $row['title']?>
        <br>
        <?php echo $row['date_posted']?>
        <br>
        <br>
        <?php echo $row['content'] ?>
        <br>
        <br>
    <?php endif ?>
    <form method="post" action="index.php">
        <br>
        <button type="submit">Back</button>
    </form>
</body>
</html>