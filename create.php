
<?php 
    require 'header.php';
    include 'includes/connect.php'; 
    require 'includes/dbHandler.php'; 
?>

<div class="mainbox">
    <div class="displaycontent">

        <!-------------Left column for creating a new topic------------>
        <div>
            <h2>Create a new topic</h2>

            <!--Form for creating a new topic-->
            <form action="create.php" method="post">
            <label for="nametopic">Name of topic:</label><br>
            <input type="text" name="nametopic" id="nametopic" placeholder="Horses, Cars..."> <br><br>
            <input type="submit" name="createTopic" id="createTopic" value="Create topic">
            </form>

            <!--Feedback to the user-->
            <?php 
                if(isset($_GET["createtopic"]) == "success") {
                echo '<p class="successMess">Successfully added new topic!</p>'; } 

                if(isset($_GET["error"]) == "titlealreadyexist") {
                    echo '<p class="errorMess">This topic already exist!</p>'; } 
            ?>
        </div>

        <!------------Right column for creating a new entry-------->
        <div>
            <h2>Make a new entry</h2>
            <?php 
                if(isset($_GET["createentry"]) == "success") {
                    echo '<p class="successMess">Successfully added new entry!</p>'; } 
            ?>
            <!--Form for creating new entry-->
            <form action="create.php" method="post">
            <label for="entrytitle">Title:</label> <br>
            <input type="text" name="entrytitle" id="entrytitle" placeholder="Horses, Cars..."> <br><br>
            <label for="content">Content:</label><br>
            <textarea name="content" id="content" cols="30" rows="10"></textarea> <br><br>
            <label for="topic">Please choose a topic for this entry</label> <br>
            <select name="topic" id="topic">
            <?php include 'includes/topicsMenu.php'; //The dropdown menu for topics?> 
            </select> <br> <br>
            <input type="submit" name="createEntry" id="createEntry" value="Create entry">
            </form>
        </div> 

    </div>
</div>



</body>
</html>