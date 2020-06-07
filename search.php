<?php 
    require 'header.php';
    include 'includes/connect.php'; 
?>

    
    <div class="mainbox">

        <h2>Search results</h2>
        <?php 
            if(isset($_POST['searchbutton'])) {
                //Check that the user did not write code etc in the search field, and get the input
                $searchInput = mysqli_real_escape_string($connection, $_POST['search']); 

                $sql = "SELECT t.*, e.* FROM topics t LEFT OUTER JOIN entries e ON t.topicId = e.topicId 
                WHERE MATCH(t.topicTitle) AGAINST('*$searchInput*' IN BOOLEAN MODE) OR
                MATCH(e.entryTitle, e.description) AGAINST('*$searchInput*' IN BOOLEAN MODE);"; 
                $searchResult = mysqli_query($connection, $sql); 
                $finalResult = mysqli_num_rows($searchResult); //Checking if we have any result from $searchResult


                if($finalResult > 0) { //If there are more than 0 results
                    echo '<p>There are ' . $finalResult . ' results from your search on "' . $searchInput . '":</p>';
                    while($list = mysqli_fetch_assoc($searchResult)) { //Display the results
                        echo '<h3> Topic: ' . $list["topicTitle"] . '</h3>' . 
                        '<h4>' . $list["entryTitle"] . '</h4>' . 
                        '<p>' . $list["description"] . '</p><hr>';
                    }
                } else { //If there are 0 results
                    echo '<p>There are ' . $finalResult . ' results your search on: "' . $searchInput . '"!</p>';
                }

            }
        ?>
    </div>
    
</body>
</html>