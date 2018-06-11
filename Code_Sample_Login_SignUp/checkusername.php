<?php

 include ('mysqli_connect.php');

 if(isset($_POST["user_name"])){
     
     
     
     $result = $dbc ->query("SELECT * FROM users WHERE user_name='".$_POST["user_name"]."'");
     
 
     if(mysqli_num_rows($result) > 0)
     
    {
         echo '<span class="text-danger">Username already exists!</span>';
 
    }
     
     
     else { echo '<span class="text-success">Username Available</span>';  }
 }



  ?>