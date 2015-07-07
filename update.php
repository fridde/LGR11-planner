<?php
	
	/* PREAMBLE */
    $url = "https://raw.githubusercontent.com/fridde/friddes_php_functions/master/include.php";
    $content = file_get_contents($url);
    if($content != FALSE){
        file_put_contents("include.php", $content); 
	}
	include "include.php";
	/* END OF PREAMBLE */
	inc("fnc,sql");
	$ini_array = parse_ini_file("config.ini", TRUE);
	
	extract(extract_request($ini_array["variables"]["v"]));
	
	if(!$user){$user = "";}
	
	if(!$indexArray){$indexArray = array();}
	$content_array = implode(",", $indexArray);
	
	switch($requestType){
		
		case "collection":
		$collectionRow = sql_select("lgr_collections", array("col_code" => $col_code));
		if(count($collectionRow) == 1){ // collection exists
			$collectionRow = reset($collectionRow);
			$collectionRow["content_array"] = $content_array;
			$collectionRow["col_name"] = $col_name;
			sql_update_row($collectionRow["id"], "lgr_collections", $collectionRow);
		} 
		else { // this is a new collection
			$collectionRow = array();
			$collectionRow["user"] = $user;
			/* the uniqueness of the $col_code was made sure of in index.php */
			$collectionRow["col_code"] = $col_code; 
			$collectionRow["col_name"] = $col_name;
			$collectionRow["content_array"] = $content_array;
			sql_insert_rows("lgr_collections", $collectionRow);
		}
		
		if($user != ""){
			redirect("view_user.php?user=" . $user . '&key=' . $key);
		}
		else{
			redirect("index.php?c=" . $col_code);
		}
		break;
		
		case "login":
		redirect("view_user.php?user=" . $user . '&key=' . $key);
		break;
		
		case "newUser":
		$userExists = 0 < count(sql_select("lgr_users", array("user" => $user)));
		if($userExists){
			redirect("login.php?m=ue");
		}
		else {
			$newPassword = generateRandomString(5);
			sql_insert_rows("lgr_users", array("user" => $user, "password" => $newPassword));
			redirect("view_user.php?user=" . $user . '&key=' . $newPassword);
		}
		
		break;
		
		case "changeName":
		$collectionRow = sql_select("lgr_collections", array("col_code" => $col_code));
		if(count($collectionRow) == 1){
			$collectionRow = reset($collectionRow);
			$collectionRow["col_name"] = $col_name;
			sql_update_row($collectionRow["id"], "lgr_collections", $collectionRow);
		} 
		break;
		
		case "changeOrder":
		$collectionRow = sql_select("lgr_collections", array("col_code" => $col_code));
		if(count($collectionRow) == 1){
			$collectionRow = reset($collectionRow);
			$collectionRow["content_array"] = $content_array;
			$collectionRow["col_name"] = $col_name;
			sql_update_row($collectionRow["id"], "lgr_collections", $collectionRow);
		} 
		break;
		
		case "forgottenEmail":
		
		break;
		
		case "add_other":
		
		$matchingCollections = sql_select("lgr_collections", array("col_code" => $col_code));
		$nrResults = count($matchingCollections);
		
		if($nrResults > 0){ 	// it should be exactly one, otherwise there's a bug in the system
			$resultRow = reset($matchingCollections);
			if(trim($resultRow["user"]) == ""){ 	// no user owns this collection yet
				sql_update_row($resultRow["id"], "lgr_collections", array("user" => $user));
			}
			elseif($resultRow["user"] === $user){	//the user wanted to assign his own collection to himself. Stupid!
				// we do nothing. everything's alright
			}
			else { 			// someone else already owns this collection. We create a copy!
				$newRow = $resultRow;
				
				$newRow["id"] = "";
				$newRow["user"] = $user;
				$newRow["col_code"] = generateRandomString(5);
				
				sql_insert_rows("lgr_collections", $newRow);
				
				redirect("view_user.php?user=" . $user . '&key=' . $key);
			}
			
		}
		else {	// if no collection was found
			
		}
		
		break;
	}
	
	
	
