<?php
   session_start(); 
   if(!($_SESSION['log']=="2796443842")){
      header("location:index.php");
      die();
   }
?>