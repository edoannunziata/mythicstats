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
      <title>Segreti Crittografici </title>
   </head>
   
   <body>
   
   <h2> Segreti Crittografici </h2>
   
<?php

  if (isset($_GET['delete'])) {
   		 $id_to_delete=$_GET['delete'];
         $sql = mysqli_query($db, "
         	UPDATE crypto_secrets
            SET date_revoked = NOW()
            WHERE id = $id_to_delete
         ");
         header("Location : ok.php");
    }
    
   if (isset($_GET['show'])) {
   $sql = mysqli_query($db, "
       		SELECT *
            FROM crypto_secrets
            WHERE date_revoked > NOW()
            ORDER BY date_granted ASC");

			echo "<table>";
            while ($row = $sql->fetch_array()) {
				echo "<tr> <td>id = $row[0]</td> <td>&nbsp</td> <td><b><code>$row[1]</code></b></td>  
                <td>&nbsp</td> <td>$row[2]</td> <td>&nbsp</td> <td><i>($row[3] -
                $row[4])</i></td> <td>&nbsp</td>
                <td> <a href = \"?delete=$row[0]\">revoca</a></td> </tr>";
			}
            echo "</table><br>";
            echo(" <a href = newsec.php>Genera nuovo segreto</a></td><br><br> ");
		}
        else {
        	echo(" <a href = \"?show=1\">Mostra lista dei segreti crittografici</a></td><br><br> ");
        }
?>

      <?php include('footer.php') ?>
   </body>
   
</html>