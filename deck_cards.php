<?php
   include('session.php');
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'beststatsintheworld');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'my_beststatsintheworld');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>

<?php
  
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      
      $deck_id = mysql_real_escape_string($_POST['deck_id']); 
      $other = mysql_real_escape_string($_POST['other_deck']);
      $class_id = mysql_real_escape_string($_POST['classification_id']);

      if( $deck_id == 0 ) {
      		mysqli_query($db, "INSERT INTO mazzi_best3 (mazzo) VALUES ('$other');");
            $get_deck_id = mysqli_query($db, "SELECT id FROM mazzi_best3 WHERE mazzo = '$other' ");
            $deck_id = mysqli_fetch_array($get_deck_id,MYSQLI_ASSOC)['id'];
      }

		$sql = mysqli_query ($db, "SELECT classification_id FROM classification_id_assoc 
        							WHERE classification_id=$class_id");
        if (mysqli_num_rows($sql) > 0) {
        	mysqli_query($db, "UPDATE classification_id_assoc
        				   SET deck_id = $deck_id
                           WHERE classification_id = $class_id");
        }
        else {
        	mysqli_query($db, "INSERT INTO classification_id_assoc (classification_id, deck_id)
        				   VALUES ($class_id, $deck_id)");
        }
        
        header("Location : ok.php");
       		
}     	
?>

<?php
	if (!(isset($_GET['deck']))) {
    	http_response_code(404);
		header('notfound.php');
    } 
    
    else {
    	$deck = $_GET['deck'];
    	echo("<html><head><title> Esplora Archetipo </title><body><h2> Esplora Archetipo </h2>");
    }
    
			$sql = mysqli_query($db, "SELECT classification_id_assoc.classification_id, 
            								 classification_id_assoc.deck_id, 
                                             mazzi_best3.mazzo 
            						  FROM classification_id_assoc
                                      JOIN mazzi_best3 ON mazzi_best3.id = classification_id_assoc.deck_id
                                      WHERE classification_id_assoc.classification_id = $deck");
                  
                $known = 0;
				 if (mysqli_num_rows($sql) > 0) {
                		$row = $sql->fetch_array();
                    	$known = 1;
                        $name = $row[2];
				}	
    
  			$json_str = file_get_contents('./digest.json');
   			$decoded = json_decode(stripslashes($json_str), TRUE);

			$i = 0; $found = 0;
            while (!$found && $decoded[$i]) {
            	if ($decoded[$i]['id'] == $deck) $found = 1;
                else $i = $i + 1;
            }
			
            if (!$found) {
            	http_response_code(404);
				header('notfound.php');
            }
            else {
                $classification_id = $decoded[$i]['id'];
                $cards = $decoded[$i]['cards'];
            }
            
			if ($known) {
            	echo ("<b>Nome Archetipo:</b> $name ");
            }
            else {
            	echo ("<b>Archetipo Sconosciuto</b>");
            }
            echo ("<br><b>Classification ID:</b> $classification_id ");
            echo "<br><br>";
            echo "<i>Carte giocate</i>:";
			$i = 0;
            echo "<ul>";
            while ($cards[$i]) {
            	echo "<li> $cards[$i] </li>";
                $i = $i + 1;
            }
            echo "</ul>"

?>
		Aggiorna nome mazzo:
		<select name="deck_id" form="deckform">
        	<option value="0">ALTRO</option>
			<?php 
				$sql = mysqli_query($db, "SELECT id, mazzo FROM mazzi_best3 ORDER BY mazzo ASC");
				while ($row = $sql->fetch_assoc()){
				echo '<option value="'.$row['id'].'">'.$row['mazzo'].'</option>';
				}
			?>
  		</select>
        
        <form action="" id="deckform" method="post">
 				 Nome mazzo (se ALTRO):  <input type="text" name="other_deck"><br>
                 <input type="hidden" value="<?php echo("$deck");?>" name="classification_id">
                 <input type="submit">
		</form>
     <?php include('footer.php') ?>
   </body>
   
</html>