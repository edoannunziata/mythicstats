<?php
  session_start();
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
           
     $mypassword = hash('sha256', $_POST['password']); 
     $correct = "40e5d9f141d1e87da2c7f34737d630fddad10507f6df91ac8e6e0f71b35a9d0e";

    if($mypassword == $correct) {
         $_SESSION['log'] = "2796443842";
         
         header("location: insert.php");
      }else {
         $error = "Password non valida";
      }
   }
?>

<html>
   
   <head>
      <title>Login</title>
      
      
      
   </head>
   
   <body> 
               <form action = "" method = "post">
                  <label>Password: </label><input type = "password" name = "password" class = "box" />
                  <input type = "submit">
               </form>   
   </body>
</html>