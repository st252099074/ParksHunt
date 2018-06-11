<?php 
/* Main page with two forms: sign up and log in */
require 'db.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Sign-Up/Login Form</title>
  <?php include 'css/css.html'; ?>
		<script type='text/javascript' src='js/jquery.min.js'></script>
		

    
</head>
    
 
<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    if (isset($_POST['login'])) { //user logging in

        require 'login.php';
        
    }
    
    elseif (isset($_POST['register'])) { //user registering
        
        require 'register.php';
        
    }
}
?>
<body>
    

    
  <div class="form">
      
      <ul class="tab-group">
        <li class="tab"><a href="#signup">Sign Up</a></li>
        <li class="tab active"><a href="#login">Log In</a></li>
      </ul>
      
      <div class="tab-content">

         <div id="login">   
          <h1>Welcome Back to Scavenger Hunt!</h1>
          
          <form action="index.php" method="post" autocomplete="off">
          
            <div class="field-wrap">
            <label>
              Email Address<span class="req">*</span>
            </label>
            <input type="email" required autocomplete="off" name="email"/>
          </div>
          
          <div class="field-wrap">
            <label>
              Password<span class="req">*</span>
            </label>
            <input type="password" required autocomplete="off" name="password"/>
          </div>
          
          <p class="forgot"><a href="forgot.php">Forgot Password?</a></p>
          
          <button class="button button-block" name="login" />Log In</button>
          
          </form>

        </div>
          
        <div id="signup">   
          <h1>Sign Up for Scavenger Hunt</h1>
          <div class="response"></div>
          <form action="index.php" method="post" autocomplete="off">
          
          <div class="top-row">
            <div class="field-wrap">
              <label>
                First Name<span class="req">*</span>
              </label>
              <input type="text" required autocomplete="off" name='firstname' />
            </div>
        
            <div class="field-wrap">
              <label>
                Last Name<span class="req">*</span>
              </label>
              <input type="text"required autocomplete="off" name='lastname' />
            </div>
          </div>

              
             <div class="field-wrap">
            <label>
              Username<span class="req">*</span>
            </label>
            <input type="text"required autocomplete="off" name='username' id = "username"/>
               <span id = "availability"></span> 
          </div>
              
              
          <div class="field-wrap">
            <label>
              Email Address<span class="req">*</span>
            </label>
            <input type="email"required autocomplete="off" name='email' />
          </div>
              
              
              
              
          <div class="field-wrap">
            <label>
              Phone (Format: 999-999-9999)
            </label>
            <input type="text"  name='phone'  pattern='^[2-9]\d{2}-\d{3}-\d{4}$' title='Phone Number Format: 999-999-9999'/>
          </div>
              
              
          
          <div class="field-wrap">
            <label  for="psw">
              Set A Password<span class="req">*</span>
            </label>
            <input type="password" id= "psw"  autocomplete="off" name='password'  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required/>
          </div>
              
              <!-- password verifcaiton content -->
<div id="message">
  
  <div id="letter" class="invalid">A <b>lowercase</b> letter</div>
  <div id="capital" class="invalid">A <b>capital (uppercase)</b> letter</div>
  <div id="number" class="invalid">A <b>number</b></div>
  <div id="length" class="invalid">Minimum <b>8 characters</b></div>
</div>
              
              
           <div class="field-wrap">
            <label>
              Confirm Password<span class="req">*</span>
            </label>
            <input type="password"  id = "psw2" autocomplete="off" name='confirmpassword'  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="two passwords must match" required/> <span id="validate-status"></span>
          </div>    
         
              
              
              
            <div class="field-wrap">
            <label>
              Enter Code<span class="req">*</span>
            </label>
            	
				<input type="text" name="captcha"  required=""/>
			<img src="captcha.php" id="captcha"/>
          </div>  
              
              
              
              
              
              
          <button type="submit" class="button button-block" name="register" />Register</button>
          
          </form>


</div>  
</div><!-- tab-content -->
   
</div> <!-- /form -->

<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

<script src="js/index.js"></script>




<script>


$(document).ready(function(){
    
    
   $('#username').blur(function(){
       
       var username = $(this).val();
       
       $.ajax({
           
           url:"checkusername.php",
           method:"POST",
           data:{user_name:username},
           dataType:"text",
           success:function(html){
               
               $('#availability').html(html);
               
           }
           
       });
       
       
   });
    
    
    
    
    
});
    
    


</script>

    





<script>
    
var myInput = document.getElementById("psw");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var length = document.getElementById("length");

// When the user clicks on the password field, show the message box
myInput.onfocus = function() {
    document.getElementById("message").style.display = "block";
}

// When the user clicks outside of the password field, hide the message box
myInput.onblur = function() {
    document.getElementById("message").style.display = "none";
}

// When the user starts to type something inside the password field
myInput.onkeyup = function() {
  // Validate lowercase letters
  var lowerCaseLetters = /[a-z]/g;
  if(myInput.value.match(lowerCaseLetters)) {  
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
  }
  
  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if(myInput.value.match(upperCaseLetters)) {  
    capital.classList.remove("invalid");
    capital.classList.add("valid");
      
      
      
  } else { 
   capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if(myInput.value.match(numbers)) {  
    number.classList.remove("invalid");
    number.classList.add("valid");
  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");
  }
  
  // Validate length
  if(myInput.value.length >= 8) {
    length.classList.remove("invalid");
    length.classList.add("valid");
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
  }
}



    

    
    


</script>


</body>
</html>
