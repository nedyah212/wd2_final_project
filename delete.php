<?php
    require('connect.php');
    require('authenticate.php');

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($id) {
        $query = "DELETE FROM program WHERE programID = :id LIMIT 1";

        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        header("Location: index.php");
        exit();
    } else {
        exit("Invalid program ID. Please try again.");
    }
?>