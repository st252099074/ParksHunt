<?php
/* Registration process, inserts user info into the database 
   and sends account confirmation email message
 */session_start();

// Set session variables to be used on profile.php page




// Escape all $_POST variables to protect against SQL injections
$first_name = $mysqli->escape_string($_POST['firstname']);
$last_name = $mysqli->escape_string($_POST['lastname']);
$email = $mysqli->escape_string($_POST['email']);
$password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
$hash = $mysqli->escape_string( md5( rand(0,1000) ) );
$user_name = $mysqli->escape_string($_POST['username']);
$phone = $mysqli->escape_string($_POST['phone']);
$confirmpassword = $mysqli->escape_string(password_hash($_POST['confirmpassword'], PASSWORD_BCRYPT));

$captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';
$img_session = isset($_SESSION['img_session']) ? $_SESSION['img_session'] : '';

// Check if user with that email already exists
$result = $mysqli->query("SELECT * FROM users WHERE email='$email'") or die($mysqli->error());


// We know user email exists if the rows returned are more than 0
if ( $result->num_rows > 0 ) {
    
    $_SESSION['message'] = 'User with this email already exists!';
    header("location: error.php");
    
}

elseif (empty(trim($_POST['username'])))
{
      
    $_SESSION['message'] = 'Username can Not be empty!';
    header("location: error.php");
}


elseif ($_POST['password'] !== $_POST['confirmpassword'])
    
    
{ $_SESSION['message'] = 'Two passwords do Not match, please try again!';
    header("location: error.php");
}


elseif (md5($captcha) !== $img_session)
    { $_SESSION['message'] = 'Wrong Captcha!';
    header("location: error.php");
}

else { // Email doesn't already exist in a database, proceed...

    // active is 0 by DEFAULT (no need to include it here)
    $sql = "INSERT INTO users (user_name, first_name, last_name, email, password, hash, create_date, phone) " 
            . "VALUES ('$user_name','$first_name','$last_name','$email','$password', '$hash', CURRENT_TIMESTAMP(),'$phone')";
  
 
    
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['first_name'] = $_POST['firstname'];
    $_SESSION['last_name'] = $_POST['lastname'];
    $_SESSION['user_name'] = $_POST['username'];
    $_SESSION['phone'] = $_POST['phone'];
  
    
    // Add user to the database
    if ( mysqli_query($mysqli, $sql) ){
        
        $user = mysqli_insert_id($mysqli);
        
        $_SESSION['user_id'] = $user;
        
        $_SESSION['active'] = 0; //0 until user activates their account with verify.php
        $_SESSION['logged_in'] = true; // So we know the user has logged in
        $_SESSION['message'] =
                
                 "Confirmation link has been sent to $email, please verify
                 your account by clicking on the link in the email!";

        // Send registration confirmation link (verify.php)
        $to      = $email;
        $subject = 'Account Verification ()';
        $message_body = '
        Hello '.$first_name.',

        Thank you for signing up!

        Please click this link to activate your account:

        http://localhost/sh_project/verify.php?email='.$email.'&hash='.$hash;   //change, it is on local now

        mail( $to, $subject, $message_body );

        header("location: profile.php"); 

    }

    else {
        $_SESSION['message'] = 'Registration failed!';
        header("location: error.php");
    }

}