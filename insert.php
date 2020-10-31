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
      $oppo = mysql_real_escape_string($_POST['opponent']);

      if( $deck_id == 0 ) {
      		mysqli_query($db, "INSERT INTO mazzi_best3 (mazzo) VALUES ('$other');");
            $get_deck_id = mysqli_query($db, "SELECT id FROM mazzi_best3 WHERE mazzo = '$other' ");
            $deck_id = mysqli_fetch_array($get_deck_id,MYSQLI_ASSOC)['id'];
      }

		
       $get_oppo_id = mysqli_query($db, "SELECT id FROM giocatori_best3 WHERE name = '$oppo' ");
       $count = mysqli_num_rows($get_oppo_id);
       
       if ($count == 0) {
       		mysqli_query($db, "INSERT INTO giocatori_best3 (name) VALUES ('$oppo');");
            $get_oppo_id = mysqli_query($db, "SELECT id FROM giocatori_best3 WHERE name = '$oppo' ");
       }
       ;
        $oppo_id = mysqli_fetch_array($get_oppo_id,MYSQLI_ASSOC)['id'];
        mysqli_query($db, "INSERT INTO deck_played_bo3 (deck_id, player_id) VALUES ($deck_id, $oppo_id)");
        
        header("Location : ok.php");
       		
}     	
?>
<html>
   
   <head>
      <title>Inserimento Dati </title>
   </head>
   
   <body>
   		<h2> Inserimento Dati </h2>
		Mazzo Giocato:
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
 				 Mazzo Giocato (se ALTRO):  <input type="text" name="other_deck"><br>
                 Nome Opponent:  <input type="text" name="opponent"><br>
                 <input type="submit">
		</form>

      <?php include('footer.php') ?>
   </body>
   
</html>