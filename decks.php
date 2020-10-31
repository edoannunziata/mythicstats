<?php
   include('session.php');
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'beststatsintheworld');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'my_beststatsintheworld');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>

<html><head><title> Archetipi </title><body><h2> Archetipi </h2><html><body>

<?php   
    
  			$json_str = file_get_contents('./digest.json');
   			$decoded = json_decode(stripslashes($json_str), TRUE);
			$sql = mysqli_query($db, "SELECT classification_id_assoc.classification_id, 
            								 classification_id_assoc.deck_id, 
                                             mazzi_best3.mazzo 
            						  FROM classification_id_assoc
                                      JOIN mazzi_best3 ON mazzi_best3.id = classification_id_assoc.deck_id");
                  
				 if (mysqli_num_rows($sql) > 0) {
            			$match_clid_name = [];
						while ($row = $sql->fetch_array()) {
							$match_clid_name[$row[0]] = $row[2];
                        }
				}
                
            echo "<ul>";
            $i = 0;
            while ($decoded[$i]) {
            	$class_id = $decoded[$i]['id'];
            	if ($match_clid_name[$class_id]) $name = $match_clid_name[$class_id]; else $name = "Sconosciuto";	
            	echo "<li> <a href=\"deck_cards.php?deck=$class_id\">$name</a></li>";
                $i = $i + 1;
            }
            echo "</ul>";

?>

     <?php include('footer.php') ?>
   </body>
   
</html>