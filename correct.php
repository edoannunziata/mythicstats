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
      <title>Ultimi Inserimenti </title>
   </head>
   
   <body>
   
   <h2> Ultimi Inserimenti </h2>
   
   
<?php

	if (isset($_GET['delete'])) {
   		 $id_to_delete=$_GET['delete'];
         $sql = mysqli_query($db, "
         	DELETE FROM deck_played_bo3
            WHERE deck_played_bo3.pair_id = $id_to_delete
         ");
         header("Location : ok.php");
    }
  
   $sql = mysqli_query($db, "
       		SELECT mazzi_best3.mazzo, giocatori_best3.name, deck_played_bo3.time_id AS time,
            	deck_played_bo3.pair_id, deck_played_bo3.use_mode AS umode 
            FROM mazzi_best3 
            JOIN deck_played_bo3 ON mazzi_best3.id = deck_played_bo3.deck_id
            JOIN giocatori_best3 ON giocatori_best3.id = deck_played_bo3.player_id
            ORDER BY time DESC");

			echo "<table>";
          	$cnt = 20;
			while (($row = $sql->fetch_array()) && $cnt){
            	echo "<tr> 
                		<td>
                        	<a href = \"query.php?opponent=$row[1]\">$row[1]</a>
                        </td> 
                        <td>&nbsp</td>"; 
                		echo " <td><b>$row[0]</b></td>";
                echo"
                		<td>&nbsp</td> 
                        <td><i>($row[2])</i></td> 
                        <td>&nbsp</td>";
                  if($row[4]) { echo"<td><i>API id: </i><b>$row[4]</b></td>"; } else {echo"<td><i>inserito manualmente<i></td>";}
                  echo "<td>&nbsp</td>
                        <td>
                			<a href = \"?delete=$row[3]\">rimuovi</a>
                        </td>
                      </tr>";
                $cnt = $cnt-1;
			}
            echo "</table><br>";         

?>

      <?php include('footer.php') ?>
   </body>
   
</html>