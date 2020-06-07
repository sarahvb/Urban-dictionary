
<?php 
    require 'header.php';
    include 'includes/dbHandler.php'; 
        
    //Extra seurity making sure that only admins can enter this page.
    if(isset($_SESSION['usertype'])){
        if($_SESSION["usertype"] == 'Admin'){

        } else {
            header("Location: index.php");
        }
    }
?>

    
<div class="mainbox">
    <h2>List of all users in the system</h2>
    <p>Keep in mind that if you delete a user that has created topics or entries, these
    will be deleted as well.</p><br><br>

    <table>
        <tr>
            <th>User id</th>
            <th>Username</th>
            <th>Type of user</th>
            </tr>

        <?php 
            $sql = "SELECT userId, username, type FROM users;";
            $result = mysqli_query($connection, $sql); //Connection from connect.php
            $checkResult = mysqli_num_rows($result); //Check if we have any result at all

            if($checkResult > 0) {      //If there are more than 0 results
                while($row = mysqli_fetch_assoc($result)) { //Put them into an assosiative array
                    echo "<tr>
                    <td>" . $row['userId'] . "</td>
                    <td>" . $row["username"] . "</td>
                    <td>" . $row["type"] . "</td>
                    <td> <form method='post'><input type='submit' name='deleteUser' value='Delete user'><input type='hidden' name='userId' value='" . $row['userId'] . "'></form></td></tr>";
                }
            }
            if(isset($_GET["deluser"]) == "success") {
                echo '<p class="successMess">Successfully deleted user!</p>';
                }
        ?>

    </table>


</div>

</body>
</html> 