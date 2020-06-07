<?php
$server = 'localhost';
$user = 'root';
$password = '';
$database = 'imt3851_assign2';

// Create connection to localhost
$connect = new mysqli($server, $user, $password);

//Check the connection
if($connect->connect_error) {
    die("Failed to connect: " . $connect->connect_error);
}

//Create the database if it does not exist already
if($connect->select_db($database) === false) {
    $createDB = 'CREATE DATABASE ' . $database;
    $connect->query($createDB); //Create the database
    $connect->select_db($database); //Select it
    makeTables($connect); 
    //Because makeTables is a function, the tables and admin will only be made once
}

//Function for creating the starting tables: 
function makeTables($connect) {
    $usertable = "CREATE TABLE IF NOT EXISTS users (
        userId INT NOT NULL AUTO_INCREMENT,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        type VARCHAR(255) NOT NULL DEFAULT 'Author',
        CONSTRAINT primaryKey_user PRIMARY KEY (userId)
        )";
    $connect->query($usertable);

    //Create the one and only admin with hashed password, new users will by default be authors
    $adminPwd = password_hash('Admin123', PASSWORD_DEFAULT);
    $newAdmin = "INSERT INTO users(username, password, type)
                VALUES ('Admin','$adminPwd','Admin')";
    $connect->query($newAdmin);

    

    //Create topics table
    $topicTable = "CREATE TABLE IF NOT EXISTS topics (
        topicId INT NOT NULL AUTO_INCREMENT,
        topicTitle VARCHAR (255) NOT NULL,
        createdBy INT NOT NULL,
        CONSTRAINT primaryKey_topics PRIMARY KEY (topicId),
        CONSTRAINT foreignKey_user FOREIGN KEY (createdBy) REFERENCES users(userId) ON UPDATE CASCADE ON DELETE CASCADE
        );";
    $connect->query($topicTable);

    //Insert dummy topic data only for viewing purposes, created by "admin"
    $topicEntry = "INSERT INTO topics(topicTitle, createdBy) VALUES ('Horses', 1)";
    $connect->query($topicEntry);

    //Add index to the topic table, as this will be frequently searched
    $indexTopic = "ALTER TABLE topics ADD FULLTEXT (topicTitle);";
    $connect->query($indexTopic);



    //Create entry table
    $entryTable = "CREATE TABLE IF NOT EXISTS entries (
        entryId INT NOT NULL AUTO_INCREMENT,
        entryTitle VARCHAR(255) NOT NULL,
        description VARCHAR(1000) NOT NULL,
        dateCreated DATETIME DEFAULT NOW() NOT NULL,
        createdBy INT NOT NULL,
        topicId INT NOT NULL,
        CONSTRAINT primaryKey_entries PRIMARY KEY (entryId),
        CONSTRAINT foreignKey_users FOREIGN KEY (createdBy) REFERENCES users(userId) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT foreignKey_topics FOREIGN KEY (topicId) REFERENCES topics(topicId) ON UPDATE CASCADE ON DELETE CASCADE
        );";
    $connect->query($entryTable);

    //Insert dummy entry data only for viewing purposes, created by "admin"
    $date = date('Y-m-d H:i:s');
    $entryEntry = "INSERT INTO entries(entryTitle, description, dateCreated, createdBy, topicId) 
                    VALUES ('Brown horses','Brown horses with white dots are the best', '$date', 1, 1)";
    $connect->query($entryEntry);

    //Add index to the entry table, as this will be frequently searched
    $indexEntry = "ALTER TABLE entries ADD FULLTEXT (entryTitle, description);";
    $connect->query($indexEntry);
}

$connect->close();

?>