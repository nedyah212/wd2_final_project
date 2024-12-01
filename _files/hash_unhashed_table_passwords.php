        <?php
            $sql = "SELECT user_name, password FROM `user`";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $user = $getUserStatement->fetch(PDO::FETCH_ASSOC); 

            foreach ($users as $user) {
                $user_name = $user['user_name'];
                $plainPassword = $user['password'];
            
                // Hash the password using bcrypt
                $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
            
                // Update the password in the database
                $updateSql = "UPDATE `user` SET password = :password WHERE user_name = :user_name";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->bindParam(':password', $hashedPassword);
                $updateStmt->bindParam(':user_name', $user_name);
                $updateStmt->execute();
            }
        ?>