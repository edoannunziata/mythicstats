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
      <title>Query </title>
   </head>
   
   <body>
   
   		<h2> Query </h2>
		
        <form action="" id="deckform" method="get">
                 Nome Opponent: <input type="text" name="opponent">
                 <input type="submit">
		</form>
        
<?php
  
   if(isset($_GET['opponent'])) {
      
       $oppo = mysql_real_escape_string($_GET['opponent']);
       $sql = mysqli_query($db, "
       		SELECT mazzi_best3.mazzo, deck_played_bo3.time_id
            FROM mazzi_best3 
            JOIN deck_played_bo3 ON mazzi_best3.id = deck_played_bo3.deck_id
            JOIN giocatori_best3 ON giocatori_best3.id = deck_played_bo3.player_id
            WHERE giocatori_best3.name = '$oppo'
            ORDER BY deck_played_bo3.time_id DESC");

			echo "Mazzi giocati da";
            echo " $oppo:<br>";
            echo "<ul>";
            if ($row = $sql->fetch_array()) {
				while ($row){
					echo "<li> <b>$row[0]</b> (<i>$row[1]</i>) </li>";
                    $row = $sql->fetch_array();
				}
            }
            else {
            	echo "Nessun risultato.";
                $sql = mysqli_query($db, "
                		SELECT giocatori_best3.name FROM giocatori_best3
                	");
                $prio = new SplPriorityQueue();
                while ($row = $sql->fetch_array()) {
                	$curr_dist = 0-levenshtein($oppo, $row[0]);
					$prio->insert($row[0], $curr_dist);
				}
                $best = $prio->extract();
                echo "<br><br> Forse cercavi: <a href=\"?opponent=$best\">$best</a>, ";
                $best = $prio->extract();
                echo "<a href=\"?opponent=$best\">$best</a>, ";
                $best = $prio->extract();
                echo "<a href=\"?opponent=$best\">$best</a>";
            }
            echo "</ul>";
            
            $html = file_get_contents("https://www.mtggoldfish.com/player/$oppo");
            $result = preg_match('#\\b' . preg_quote("No tournament", '#') . '\\b#i', $html);
            if ($result) {
    			echo "Non ci sono risultati su MTGGoldfish";
			}
            else {
            	echo "<a href = \"https://www.mtggoldfish.com/player/$oppo\">Risultati MTGGoldfish</a>";
            }
			            
            echo "<br><br>";
    }            

?>

     <?php include('footer.php') ?>
   </body>
   
</html>