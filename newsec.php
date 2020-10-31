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
      <title>Genera Segreto Crittografico </title>
   </head>
   
   <body>
   
   <h2>Genera Segreto Crittografico </h2>
   
<?php
    
	 if($_SERVER["REQUEST_METHOD"] == "POST") {
      
      $name = mysql_real_escape_string($_POST['name']); 
      $exp = mysql_real_escape_string($_POST['expire']);
	  $random_str = base64_encode(openssl_random_pseudo_bytes(30));
      
	  switch ($exp) {
      	case 0:
      		mysqli_query($db, "INSERT INTO crypto_secrets
            		(secret, name, date_revoked) VALUES 
                    (\"$random_str\", \"$name\", NOW() + INTERVAL 2 HOUR)");
            break;
        case 1:
        	mysqli_query($db, "INSERT INTO crypto_secrets
            		(secret, name, date_revoked) VALUES 
                    (\"$random_str\", \"$name\", NOW() + INTERVAL 7 DAY)");
        	break;
        case 2:
        	mysqli_query($db, "INSERT INTO crypto_secrets
            		(secret, name, date_revoked) VALUES 
                    (\"$random_str\", \"$name\", NOW() + INTERVAL 30 DAY)");
            break;
      }
        header("Location : ok.php");
       		
}     	
?>

	Scadenza segreto:
		<select name="expire" form="gen_secret">
            <option value="0">2 ore</option>
            <option value="1">7 giorni</option>
            <option value="2">30 giorni</option>
  		</select> <br>
        
        Algoritmo:
        <select name="algo" form="gen_secret">
            <option value="0">HMAC SHA-256 bit</option>
  		</select>
        
        <form action="" id="gen_secret" method="post">
                 Nome:  <input type="text" name="name"><br>
                 <input type="submit">
		</form>

      <?php include('footer.php') ?>
   </body>
   
</html>