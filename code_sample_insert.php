<?php
    include ('mysqli_connect.php');

   #sanitizing input from $_POST variable to prevent SQL injection
   $userId = $dbc->escape_string($_POST['userId']); // current_user_id
   $placeId = $dbc->escape_string($_POST['placeId']); // the_place_id

   #check if the record exists (the user has been to this park site)
   $sql = "SELECT user_id, site_id FROM visit_table where (user_id = '".$userId."'  and site_id = '". $placeId."')";
   $result1 = @mysqli_query ($dbc,  $sql); 
   if ( $result1->num_rows == 0 ) {

   $query = "INSERT INTO visit_table (user_id, site_id, date) VALUES ('".$userId."' , '".$placeId."', CURRENT_TIMESTAMP())";     
   $result = @mysqli_query ($dbc, $query); // Run the query.

   if ($result) { // If it ran OK.
        // Print a message.
        echo '<h1 id="mainhead">Success!</h1>';
    }
    else { // If it did not run OK.
        // Print error.
        echo '<h1 id="mainhead">Error</h1>';
    }};


    #check if the record exists (the user has answered questions to this park site)
    $query2 = "SELECT form FROM site_table as  tp JOIN  visit_table as up ON tp.site_id = up.site_id where (up.user_id = '".$userId."' and tp.site_id = '".$placeId."')" ;
    $sql2 = mysqli_query($dbc, $query2);
    if (!$sql2) {
    printf("Error: %s\n", mysqli_error($dbc));
    exit();
    }
    while($row = mysqli_fetch_array($sql2, MYSQLI_ASSOC)) {
    if ($row['form'] == '0'){   
       $check = '0';
    break;
                            }
    else { 
       $check = '1';
    break;
    }}
    echo $check;



              



?>    
            

