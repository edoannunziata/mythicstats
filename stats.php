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
      <title>Statistiche </title>
   </head>
   
   <body>
   
   <h2> Statistiche </h2>
   
<?php

                
   $sql = mysqli_query($db, "
       		SELECT mazzi_best3.mazzo, count(mazzi_best3.mazzo) AS cnt
            FROM mazzi_best3 
            JOIN deck_played_bo3 ON mazzi_best3.id = deck_played_bo3.deck_id
            JOIN giocatori_best3 ON giocatori_best3.id = deck_played_bo3.player_id
            GROUP BY mazzi_best3.mazzo
            ORDER BY cnt ASC");

			echo "<table>";
            $sum = 0; $stack = array();
            while ($row = $sql->fetch_array() ){
				$sum = $sum + $row[1];
                array_push($stack, $row);
			}
			while ($row = array_pop($stack) ){
            	$perc = sprintf("%.1f%%", $row[1]/$sum * 100);
                	echo "<tr> <td><b>$row[0]</b></td>";
                echo "<td>&nbsp</td> <td>$row[1]</td>  
                <td>&nbsp</td> <td><i>($perc)</i></td> </tr>";
			}
            echo "</table><br>";         

?>

     <?php include('footer.php') ?>
   </body>
   
</html>