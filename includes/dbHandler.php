<?php

//This file serves as a library for functions used all around the application
//such as registering, log in and out, creating topics and entries etc
require_once "connect.php"; //We need this to connect to the database


//___________SANITIZE USER INPUT____________________________________________________________
function inputSanitize($var, $connection) {
    $var = stripslashes($_POST[$var]);
    $var = htmlentities($var);
    $var = strip_tags($var);
    $var = $connection->real_escape_string($var);

    return $var;
}
//___________________________________________________________________________________________



//___________REGISTER NEW USER____________________________________________from newAccount.php
if (isset($_POST['submit'])) {

    $username = inputSanitize("username", $connection);
    $password = inputSanitize("password", $connection);
    $confPassword = inputSanitize("confPassword", $connection);

    //Check if some of the fields are empty when user try to register
    if(empty($username) || empty($password) || empty($confPassword)) {
        header("Location: ../newAccount.php?error=emptyfields");
        exit();
    } 
    //Validate the username to make sure there are no special characters
    elseif (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../newAccount.php?error=invalidusername");
        exit();
    } 
    //Password cannot be less than 8 or more than 16 characters
    elseif (strlen($password) < 8 || strlen($password) > 16) {
        header("Location: ../newAccount.php?error=invalidpassword");
        exit();
    } 
    //Check if password and confirm password match 
    elseif ($password !== $confPassword) { 
        header("Location: ../newAccount.php?error=passwordnotmatch");
        exit();
    } 
    //Check if the username already exist
    else { 
        $sql = "SELECT username FROM users WHERE username=?";
        $statement = mysqli_stmt_init($connection);
        if(!mysqli_stmt_prepare($statement, $sql)) {
            header("Location: ../newAccount.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($statement, "s", $username); //Take the input username
            mysqli_stmt_execute($statement);
            mysqli_stmt_store_result($statement); //And store the input
            $checkResult = mysqli_stmt_num_rows($statement); //Get the rows from database
    
            if($checkResult > 0) { //If the username already exists 
                header("Location: ../newAccount.php?error=useralreadytaken");
                exit();
            } else { //If the username does not exist
                $sql = "INSERT INTO users (username, password) VALUES (?, ?)"; //Passing placeholders ?
                $statement = mysqli_stmt_init($connection);
                if (!mysqli_stmt_prepare($statement, $sql)) { //If it does not work
                    header("Location: ../newAccount.php?error=sqlerror");
                    exit();
                } else { //If it works, pass the information with hashed password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); //Hashing the password
                    mysqli_stmt_bind_param($statement, "ss", $username, $hashedPassword);
                    mysqli_stmt_execute($statement);
                    header("Location: ../index.php?signup=success");
                    exit();
                }
            }
        }  
    }
    //Close off the connection 
    mysqli_stmt_close($statement);
    mysqli_close($connection);
}
//___________________________________________________________________________________________



//__________LOG IN____________________________________________________________from header.php
if (isset($_POST['login'])) {

    $username = inputSanitize("username", $connection);
    $password = inputSanitize("password", $connection);

    if(empty($username) || empty($password)) {
        header("Location: ../index.php?error=emptyfields");
        exit();
    } else { //Does this user exists
        $sql = "SELECT * FROM users WHERE BINARY username=?"; //BINARY is for case sensitive username
        $statement = mysqli_stmt_init($connection); //The connection from connect.php
        if(!mysqli_stmt_prepare($statement, $sql)) {
            header("Location: ../index.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($statement, "s", $username); //Pass in the params that the user typed in
            mysqli_stmt_execute($statement); //Now we can grab the result
            $result = mysqli_stmt_get_result($statement);

            if($row = mysqli_fetch_assoc($result)) { //Fetch data from result into assoc array
                $checkPassword = password_verify($password, $row['password']); //Check input password up against database for match
                if($checkPassword == false) {
                    header("Location: ../index.php?error=wrongpassword");
                    exit();
                } elseif($checkPassword == true) { //If the right password is typed in, log in
                    session_start(); //Start a session
                    $_SESSION['userId'] = $row['userId']; //Grab information from the database about the current user
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['usertype'] = $row['type'];

                    header("Location: ../index.php?success=loggedin");
                    exit();
                }
            } else {
                header("Location: ../index.php?error=nouser");
                exit();
            }
        }
    }
}
//___________________________________________________________________________________________



//_______LOG OUT USER_________________________________________________________from header.php
if (isset($_POST['logout'])) { 
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../index.php?success=loggetout");
    exit();
}
//___________________________________________________________________________________________



//__________CREATE TOPIC______________________________________________________from create.php
if (isset($_POST['createTopic'])) { 

    $title = inputSanitize("nametopic", $connection);
    $userId = $_SESSION['userId'];
    $sql = "SELECT topicTitle FROM topics WHERE topicTitle=?";

    $statement = mysqli_stmt_init($connection);
    if (!mysqli_stmt_prepare($statement, $sql)) { //If it does not work
        header("Location: create.php?error=sqlerror");
        exit();
    } else { //If it works
        mysqli_stmt_bind_param($statement, "s", $title); //Take the input title
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement); //And store the input
        $checkResult = mysqli_stmt_num_rows($statement); //Get the rows from database

        if($checkResult > 0) { //If the title already exists in the database
            header("Location: create.php?error=titlealreadyexist"); //Display error in the url
            exit();
        } else { //If the title does not exist in the db
            $sql = "INSERT INTO topics (topicTitle, createdBy) VALUES (?,?)"; //Passing placeholders
            mysqli_stmt_prepare($statement, $sql);
            mysqli_stmt_bind_param($statement, "si", $title, $userId);
            mysqli_stmt_execute($statement);
            header("Location: create.php?createtopic=success");
            exit();   
        }
    }
    mysqli_stmt_close($statement);
}
//___________________________________________________________________________________________



//_________CREATE ENTRY_______________________________________________________from create.php
if(isset($_SESSION['username'])) {

    if (isset($_POST['createEntry'])) { 
        $entrytitle = inputSanitize('entrytitle', $connection);
        $content = inputSanitize('content', $connection);
        $userId = $_SESSION['userId'];
        $topicId = inputSanitize('topic', $connection);

        $statement = mysqli_stmt_init($connection);
        $sql = "INSERT INTO entries (entryTitle, description, createdBy, topicId) VALUES (?,?,?,?)"; //Passing placeholders
        
        if (!mysqli_stmt_prepare($statement, $sql)) { //If it does not work
            header("Location: create.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_prepare($statement, $sql); 
            mysqli_stmt_bind_param($statement, "ssii", $entrytitle, $content, $userId, $topicId);
            mysqli_stmt_execute($statement);
            header("Location: create.php?createentry=success");
            exit();  
        } 
        mysqli_stmt_close($statement);
    }
}
//___________________________________________________________________________________________



//_______________DELETE TOPIC___________________________________________________from index.php
if(isset($_POST['deleteTopic'])) {
    $sql = "DELETE FROM topics WHERE topicId=" . $_REQUEST['topicId'];
    $connection->query($sql);
    header("Location: index.php?deltopic=success");
    exit();  
}
//___________________________________________________________________________________________



//_______________DELETE ENTRY__________________________________________________from index.php
if(isset($_POST['deleteEntry'])) {
    $sql = "DELETE FROM entries WHERE entryId=" . $_REQUEST['entryId'];
    $connection->query($sql);
    header("Location: index.php?delentry=success");
    exit();  
}
//___________________________________________________________________________________________



//________________DELETE USER (ADMIN PRIVILEGE)_____________________________from userList.php
if(isset($_POST['deleteUser'])) {
    $sql = "DELETE FROM users WHERE userId=" . $_REQUEST['userId'];
    $connection->query($sql);
    header("Location: userList.php?deluser=success");
    exit();  
}
//___________________________________________________________________________________________



//___________UPDATE USERNAME_____________________________________________from userProfile.php
if(isset($_POST['changeUsername'])) {
    $userId = $_SESSION['userId'];
    $newUsername = $_POST['newUsername'];
    $sql = "SELECT username FROM users WHERE username=?;";

    $statement = mysqli_stmt_init($connection);

    if (!mysqli_stmt_prepare($statement, $sql)) { //If it does not work
        header("Location: userProfile.php?error=sqlerror");
        exit();
    } else {
        mysqli_stmt_bind_param($statement, "s", $newUsername); //Take the input username
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement); //And store the input
        $checkResult = mysqli_stmt_num_rows($statement); //Get the rows from database
    
        if($checkResult > 0) { //If the username already exists 
            header("Location: userProfile.php?error=useralreadytaken");
            exit();
        } else {
            $sql = "UPDATE users SET username = ? WHERE userId = ?"; //The query to be sent
            mysqli_stmt_prepare($statement, $sql); 
            mysqli_stmt_bind_param($statement, "si", $newUsername, $userId);
            mysqli_stmt_execute($statement);
            header("Location: userProfile.php?changeusername=success");
            exit();  
        }
    } 
    mysqli_stmt_close($statement);
}
//___________________________________________________________________________________________



//__________UPDATE PASSWORD______________________________________________from userProfile.php
if(isset($_POST['changePass'])) { 
    $currentPass = $_POST['currentPass']; //What user says is current password
    $newPassword = $_POST['newPass'];   //The new password the user wants
    $confNewPassword = $_POST['confNewPass']; //Confirm the new password
    $userId = $_SESSION['userId'];     //The current user making the request
    
    $sql = "SELECT * FROM users WHERE userId=?"; //Placeholder ?
    $statement = mysqli_stmt_init($connection); //The connection from connect.php

    if(!mysqli_stmt_prepare($statement, $sql)) {
        header("Location: userProfile.php?error=sqlerror");
        exit();
    } else {
        mysqli_stmt_bind_param($statement, "i", $userId); 
        mysqli_stmt_execute($statement); 
        $result = mysqli_stmt_get_result($statement); //Get the results from db

        if($row = mysqli_fetch_assoc($result)) { //Fetch data from result into assoc array
            //Check if the input password from user is the same as the one saved in db on this user:
            $checkPassword = password_verify($currentPass, $row['password']); 

            if($checkPassword == false) { //If it is not the same
                header("Location: userProfile.php?error=wrongpassword");
                exit();
            } elseif ($newPassword !== $confNewPassword) { //If confirmed password is not equal
                header("Location: userProfile.php?error=passwordnotmatch");
                exit();
            } elseif (strlen($newPassword) < 8 || strlen($newPassword) > 16) { //Password must be <8 and >16
                header("Location: userProfile.php?error=invalidpassword");
                exit();
            } elseif ($checkPassword == true) { //If the right password is typed in
                $sql = "UPDATE users SET password = ? WHERE userId = ?";
                $newHashedPass = password_hash($newPassword, PASSWORD_DEFAULT); //Re-hash the new password
                mysqli_stmt_prepare($statement, $sql); 
                mysqli_stmt_bind_param($statement, "si", $newHashedPass, $userId); //Connect the new pass to user
                mysqli_stmt_execute($statement); //Pass it to the database
                header("Location: userProfile.php?updatepassword=success");
                exit();
            } else {
                header("Location: userProfile.php?error=notupdated");
                exit();
            }
        }
    }
    mysqli_stmt_close($statement);
}
//___________________________________________________________________________________________



//__________________DISPLAY TOPICS_______________________________________________on index.php
function displayTopics($query) { 
    $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $result = mysqli_query($connection, $query); 
    $checkResult = mysqli_num_rows($result); //Check if we have any result at all
    if($checkResult > 0) {      //If there are more than 0 results
        while($row = mysqli_fetch_assoc($result)) { //Put them into an assosiative array
            $article = '<article><h3><a href="index.php?topicId=' . $row["topicId"] . 
                        '">' . $row["topicTitle"] . '<a/></h3>' . //Make the topic title clickable 
                        '<p> Made by: ' . $row['username'] . '</p>';
            
            //Admin can delete any topic no matter who made it, and see the total entries in each topic
            if(isset($_SESSION['usertype'])) {
                if($_SESSION['usertype'] == 'Admin') {
                    $sql = "SELECT COUNT(*) FROM entries WHERE topicId =" . $row['topicId'];
                    $db = $connection->query($sql);
                    $count = $db->fetch_row();
                    $article .= $count[0] . " entries in this topic<br><br>";
                    $article .= "<form method='post'><input type='submit' name='deleteTopic' value='Delete topic'><input type='hidden' name='topicId' value='" . $row['topicId'] . "'></form>";
                    
                //Users can delete their own topics
                } elseif($_SESSION['usertype'] == 'Author') { 
                if($_SESSION['userId'] == $row['userId']) { 
                    $article .= "<form method='post'><input type='submit' name='deleteTopic' value='Delete topic'><input type='hidden' name='topicId' value='" . $row['topicId'] . "'></form>";
                    }
                }
            }
            //Adding the closing tag to get the delete button inside the styled article only for design purpose
            $article .= "</article>"; 
            echo $article;
        }
    } else {
        echo "<br><br>There are no topics right now. Log in or register to create one!";
    }
}
//___________________________________________________________________________________________



//___________________DISPLAY ENTRIES_____________________________________________on index.php
function displayEntries($sql){

    $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $result = mysqli_query($connection, $sql); //Connection from connect.php
    $checkResult = mysqli_num_rows($result); //Check if we have any result at all

    if($checkResult > 0) {   //If there are more than 0 results
        while($row = mysqli_fetch_assoc($result)) { //Put them into an assosiative array
            $entries = '<article><h3>' . $row["entryTitle"] . '</h3>' . 
                        '<p>Date: ' . $row['dateCreated'] . '</p>' .
                        '<p>' . $row['description'] . '</p>' .
                        '<p> Written by: ' . $row['username'] . '</p>' . 
                        '<p> Topic: ' . $row['topicTitle'] . '</p>';

            if(isset($_SESSION['usertype'])) {
                if($_SESSION['usertype'] == 'Admin') {
                    $entries .= "<form method='post'><input type='submit' name='deleteEntry' value='Delete entry'><input type='hidden' name='entryId' value='" . $row['entryId'] . "'></form>";
                    
                } elseif($_SESSION['usertype'] == 'Author') { 
                    if($_SESSION['userId'] == $row['userId']) { 
                        $entries .= "<form method='post'><input type='submit' name='deleteEntry' value='Delete entry'><input type='hidden' name='entryId' value='" . $row['entryId'] . "'></form>";
                    }
                }
            }
            
            $entries .= "</article>"; 
            echo $entries;
        }
    } else {
        echo "There are no entries in this topic (yet).";
    }
}
//___________________________________________________________________________________________




?>