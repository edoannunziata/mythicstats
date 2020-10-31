<?php
   include('session.php');
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'beststatsintheworld');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'my_beststatsintheworld');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>

<html>
   
   <head>
      <title>App </title>
   </head>
   
   <body>
   		<h2> App </h2>
		<ul><li><a href="gui.exe">Scarica binario per <i>Microsoft Windows</i></a><br>
        <code>md5 = <b><?php echo(md5_file('gui.exe')) ?></b></code>
        </li></ul>
        
        
      <?php include('footer.php') ?>
   </body>
   
</html>