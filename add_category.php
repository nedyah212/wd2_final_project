<?php
session_start();
require('connect.php');
include('header.php');

$output_string = "";

$query = "SELECT * FROM category"; 
$statement = $db->prepare($query);
$statement->execute();
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['create_category'])) {
    $new_name = $_POST['new_name'];
    $new_description = $_POST['new_description'];

    if (!empty($new_name) && !empty($new_description)) {
        try {
            $query = "INSERT INTO category (categoryName, description) VALUES (:category_name, :description)";
            $statement = $db->prepare($query);
            $statement->bindValue(':category_name', $new_name);
            $statement->bindValue(':description', $new_description);
            $statement->execute();

            $output_string = "New category created successfully.";
            
            header("Location: add_category.php");
            exit;

        } catch (Exception $e) {
            $output_string = "Error creating category: " . $e->getMessage();
        }
    } else {
        $output_string = "Please provide both a category name and description.";
    }
}

if (isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $new_name = $_POST['new_name'];
    $new_description = $_POST['new_description'];

    if (!empty($category_id) && !empty($new_name) && !empty($new_description)) {
        try {
            $query = "UPDATE category SET categoryName = :category_name, description = :description WHERE categoryID = :category_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':category_name', $new_name);
            $statement->bindValue(':description', $new_description);
            $statement->bindValue(':category_id', $category_id);
            $statement->execute();

            $output_string = "Category updated successfully.";

            header("Location: add_category.php");
            exit;

        } catch (Exception $e) {
            $output_string = "Error updating category: " . $e->getMessage();
        }
    } else {
        $output_string = "Please select a category, and provide both a new name and description.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Category Management</title>
</head>
<body>
    <p><?= $output_string ?></p>

    <h2>Create New Category</h2>
    <form method="post" action="add_category.php">
        <label for="new_name">Category Name:</label>
        <input type="text" name="new_name" id="new_name" required />
        <br><br>

        <label for="new_description">Description:</label>
        <textarea name="new_description" id="new_description" required></textarea>
        <br><br>

        <input type="submit" name="create_category" value="Create Category" />
    </form>

    <hr>

    <h2>Modify Categories</h2>
    <form method="post" action="add_category.php">
        <label for="category_id">Select Category to Modify:</label>
        <select name="category_id" id="category_id" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['categoryID'] ?>"><?= htmlspecialchars($category['categoryName']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="new_name">New Category Name:</label>
        <input type="text" name="new_name" id="new_name" required />
        <br><br>

        <label for="new_description">New Description:</label>
        <textarea name="new_description" id="new_description" required></textarea>
        <br><br>

        <input type="submit" name="update_category" value="Update Category" />
    </form>

</body>
</html>