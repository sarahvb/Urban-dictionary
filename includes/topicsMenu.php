<?php
include 'includes/connect.php';

/* This is very little code that only displays the titles from topics in
    a select dropdown for the users, when they make a new entry in create.php.*/

$result = $connection->query("SELECT topicId, topicTitle FROM topics");
$checkResult = mysqli_num_rows($result);

if($checkResult < 0) { 
    echo '<p>There are no available topics. Please create one first.</p>';
} else {
    while ($list = $result->fetch_assoc()) {
        $topicId = $list['topicId'];
        $title = $list['topicTitle'];
        echo '<option value="'.$topicId.'">' .$title.'</option>';
    }
}
