<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'beststatsintheworld');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'my_beststatsintheworld');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
?>

<?php
function json_response($code = 200, $msg)
{
	header_remove();
	http_response_code($code);
	header('Content-Type: application/json');
   header('Status: '.$code);
   if ($code == 200) {
   		return json_encode(array(
        'status' => $code,
        ));
   }
   else {
    return json_encode(array(
        'status' => $code,
        'details' => $msg
        ));
   }
}
   
   		$json_str = file_get_contents('php://input');
   		$decoded = json_decode(stripslashes($json_str), TRUE);
        $api_id = $decoded['api_id'];
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
        $type = $decoded['type'];
        $deck_id = mysql_real_escape_string($decoded['deck_id']); 
      	$other = mysql_real_escape_string($decoded['other_deck']);
      	$oppo = mysql_real_escape_string($decoded['opponent']);
        $cards = $decoded['cards'];
        
        if (!$decoded) {
        	exit(json_response(400, "Invalid JSON."));
        } 
        
        $qry = mysqli_query($db, "SELECT * FROM crypto_secrets WHERE id = '$api_id' ");  
        if ((mysqli_num_rows($qry) != 1)) {
        	exit(json_response(401, "Authentication Failure."));
        }
        
        $row = $qry->fetch_array();
        $secret = $row[1];
        $hash = hash_hmac('sha256', $json_str, $secret);
        
        if ($hash != $auth_header) {
        	exit(json_response(401, "Authentication Failure."));
        }
        
switch ($type) {
        
   case "insert":
      
      	if( !$oppo ) {
        	exit(json_response(400, "Missing necessary parameter: 'opponent'."));
        }

		$qry = mysqli_query($db, "SELECT * FROM mazzi_best3 WHERE id = '$deck_id' ");  
        if ((mysqli_num_rows($qry) != 1) && ($deck_id != 0)) {
        	exit(json_response(400, "Invalid deck ID."));
        }

      	if( $deck_id == 0 ) {
      		if( !$other ) {
        		exit(json_response(400, "Missing necessary parameter: 'otherdeck'."));
        	}
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
       
        $oppo_id = mysqli_fetch_array($get_oppo_id,MYSQLI_ASSOC)['id'];
        mysqli_query($db, "INSERT INTO deck_played_bo3 (deck_id, player_id, use_mode) VALUES ($deck_id, $oppo_id, $api_id)");
        
        exit(json_response(200, "Insertion Successful."));
        break;
        
	case "stats":
    	exit(json_response(200, "OK"));
        
    case "autoclassify":
    	if( !$cards ) {
        	exit(json_response(400, "Missing necessary parameter: 'cards'."));
        }
        
         $sql = mysqli_query($db, "SELECT classification_id_assoc.classification_id, 
            								 classification_id_assoc.deck_id, 
                                             mazzi_best3.mazzo 
            						  FROM classification_id_assoc
                                      JOIN mazzi_best3 ON mazzi_best3.id = classification_id_assoc.deck_id");
                  
				 if (mysqli_num_rows($sql) > 0) {
            			$match_clid_id = [];
						while ($row = $sql->fetch_array()) {
							$match_clid_id[$row[0]] = $row[1];
                        }
				}
                
        $json_digest = file_get_contents('./digest.json');
   		$deckdb = json_decode(stripslashes($json_digest), TRUE);
        $deck_id_maximum_score = null;
        $maximum_score = -1;
        for($i = 0; $i < count($cards); ++$i) {
   			 for($j = 0; $j < count($deckdb); ++$j) {
             	if ($match_clid_id[$deckdb[$j]['id']]) {
                	$current_score = 0;
             		for($k = 0; $k < count($deckdb[$j]['cards']); ++$k) {
                		if ($cards[$i] == $deckdb[$j]['cards'][$k]) {                   	
                    		$current_score++;
                    	}
                	}
                	if ($current_score > $maximum_score) { 
                    	$maximum_score = $current_score;
                        $deck_id_maximum_score = $match_clid_id[$deckdb[$j]['id']];
                    }
                }
             }
		}
        
       if( !$oppo ) {
        	exit(json_response(400, "Missing necessary parameter: 'opponent'."));
        }

       $get_oppo_id = mysqli_query($db, "SELECT id FROM giocatori_best3 WHERE name = '$oppo' ");
       $count = mysqli_num_rows($get_oppo_id);
       
       if ($count == 0) {
       		mysqli_query($db, "INSERT INTO giocatori_best3 (name) VALUES ('$oppo');");
            $get_oppo_id = mysqli_query($db, "SELECT id FROM giocatori_best3 WHERE name = '$oppo' ");
       }
       
        $oppo_id = mysqli_fetch_array($get_oppo_id,MYSQLI_ASSOC)['id'];
        mysqli_query($db, "INSERT INTO deck_played_bo3 (deck_id, player_id, use_mode) VALUES ($deck_id_maximum_score, $oppo_id, $api_id)");
        
        exit(json_response(200, "Insertion Successful."));
        
    case "classify":
    	if( !$cards ) {
        	exit(json_response(400, "Missing necessary parameter: 'cards'."));
        }
        
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
                
        $json_digest = file_get_contents('./digest.json');
   		$deckdb = json_decode(stripslashes($json_digest), TRUE);
        $scores = [];
        for($i = 0; $i < count($cards); ++$i) {
   			 for($j = 0; $j < count($deckdb); ++$j) {
             	if ($match_clid_name[$deckdb[$j]['id']]) {
                	$current_score = 0;
             		for($k = 0; $k < count($deckdb[$j]['cards']); ++$k) {
                		if ($cards[$i] == $deckdb[$j]['cards'][$k]) {                   	
                    		$current_score++;
                    	}
                	}
                    if ($current_score>0) {
                		if ($scores[$match_clid_name[$deckdb[$j]['id']]]) { 
                			$scores[$match_clid_name[$deckdb[$j]['id']]] = max($scores[$match_clid_name[$deckdb[$j]['id']]], $current_score);
                		} else {
                			$scores[$match_clid_name[$deckdb[$j]['id']]] = $current_score;
                		}
                    }
                }
             }
		}
       
        $response = array('status' => 200, 'scores' => $scores);
         exit(json_encode($response));
        
    case "query":
    	if( !$oppo ) {
        	exit(json_response(400, "Missing necessary parameter: 'opponent'."));
        }
    	$sql = mysqli_query($db, "
       		SELECT mazzi_best3.mazzo, deck_played_bo3.time_id, mazzi_best3.id
            FROM mazzi_best3 
            JOIN deck_played_bo3 ON mazzi_best3.id = deck_played_bo3.deck_id
            JOIN giocatori_best3 ON giocatori_best3.id = deck_played_bo3.player_id
            WHERE giocatori_best3.name = '$oppo'
            ORDER BY deck_played_bo3.time_id DESC");
            header_remove();
			http_response_code(200);
			header('Content-Type: application/json');
            $enc = [];
            while ($row = $sql->fetch_array()) {
         		array_push($enc, array('deck_id' => $row[2], 'deck_name' => $row[0], 'time' => $row[1]));
         	}
            $response = array('status' => 200, 'opponent' => $oppo, 'decks' => $enc);
         exit(json_encode($response));
        
    case "decks":
    	$sql = mysqli_query($db, "
       		SELECT mazzi_best3.mazzo, mazzi_best3.id
            FROM mazzi_best3 
            ORDER BY mazzi_best3.id ASC");
            header_remove();
			http_response_code(200);
			header('Content-Type: application/json');
            $enc = [];
            while ($row = $sql->fetch_array()) {
         		array_push($enc, array('deck_name' => $row[0], 'deck_id' => $row[1]));
         	}
            $response = array('status' => 200, 'decks' => $enc);
         exit(json_encode($response));
        
    default:
    	exit(json_response(400, "Unknown type of request."));
        
 }
?>