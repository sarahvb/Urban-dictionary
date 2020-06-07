<?php 
session_start();
//Header.php is a page that is included throughout all the pages
//This means that i can keep the session going on all pages, and it also
//help with the design. The navigation is carried in all pages.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <header>
        <nav>
            <ul>
                <!-- Guest will see home and the only ones to see signup -->
                <li><a href="index.php">Home</a></li>
                <?php if(!isset($_SESSION['username'])) : ?>
                    <li><a href="newAccount.php">Sign up</a></li>
                <?php endif ?>

                <!-- Logged in users will see home, create and profile -->
                <?php if(isset($_SESSION['username'])) : ?>
                    <li><a href="userProfile.php">Profile</a></li>
                    <li><a href="create.php">Create topic or entry</a></li>
                <?php endif ?>

                <!-- Admin will se everything including link to list of users -->
                <?php if(isset($_SESSION['usertype'])) : ?>
                    <?php if($_SESSION['usertype'] == 'Admin') : ?>
                    <li><a href="userList.php">Users</a></li>
                <?php endif ?>
                <?php endif ?>
            </ul>
        <!--------------------------------------------------------------------->
        
            <!--Search -->
            <form action="search.php" method="POST">
                <input type="search" name="search" id="search" placeholder="Search">
                <input type="submit" name="searchbutton" value="Search">
            </form>

            <!--Log in or out forms depending on the state -->
            <div class="signInOut">
                <?php 
                if(isset($_SESSION['username'])) { //If a user is logged in, only display logout
                    $username = $_SESSION['username'];
                    echo "<p>Logged in as $username</p>
                    <form action='includes/dbHandler.php' method='post'>
                    <input type='submit' name='logout' id='logout' value='Log out'>
                    </form>";
                } else { //If there is no user logged in, display the login form
                    echo '<form action="includes/dbHandler.php" method="post">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password">
                    <input type="submit" name="login" id="submit" value="Log in">
                    </form>';
                }
                ?>
            </div>
        </nav>
    </header>

