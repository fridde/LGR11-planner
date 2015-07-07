<?php
	
	/* PREAMBLE */
    $url = "https://raw.githubusercontent.com/fridde/friddes_php_functions/master/include.php";
    $content = file_get_contents($url); 
    if($content != FALSE){
        //file_put_contents("include.php", $content); 
	}
    include "include.php";
	/* END OF PREAMBLE */
	
	inc("fnc,sql");
	$ini_array = parse_ini_file("config.ini", TRUE);
	activate_all_errors();
	extract(extract_request($ini_array["variables"]["v"]));
	
	list($html, $head, $body, $nav, $form, $h1, $ul) = array_fill(0,20,""); 
	
	$col_name = "Namnlös planering";
	if ($col_code) {
		$collection = sql_select("lgr_collections", array("col_code" => $col_code));
		$collection = (count($collection) > 0 ? reset($collection) : FALSE );
		if($collection){
			$rowsToView = $collection["content_array"];
			$col_name = $collection["col_name"];
		}
	}
	$head .= qtag("meta");
	$head .= tag("title", $col_name . " - LGR11-planerare");
		
	$incString = "jquery,jqueryUIjs, jqueryUIcss,css";
	if($view == "table"){
		$incString .= ",DTjQ,DTTT,DTfH,DTin,DTcss,DTfHcss,DTfHcss,DTTTcss";
	}
	else{
		$incString .= ",bootcss,bootjs,boottheme,fAwe,init,star";
	}
	$head .= inc($incString, FALSE, TRUE);
	
	$html .= tag("head", $head);
	
	$links = array("Startsida" => "index.php");
	if($user && $key){
		$links["Min sida"] = "view_user.php?user=" . $user . "&key=" . $key;  
	}
	else {
		$links["Logga in"] = "login.php";
	}
	
	$body .= qtag("nav", "fixed", $links);
	
	$h1 .= qtag("textinput", "col_name", "", "", $col_name, "colName");
	$form .=  tag("h1", $h1);
	if($col_code){
		$form .=  tag("p", 'Kod för planeringen: <code>' . $col_code . '</code>', array("id" => "codeGiven"));
		$oldColCode = TRUE;
	}
	else {
		$oldColCode = FALSE;
		/* loop to ensure that this string is unique */
		$codeExists = TRUE;
		while($codeExists){
			$col_code = generateRandomString(5);
			$checkCollections = sql_select("lgr_collections", array("col_code" => $col_code));
			if(count($checkCollections) == 0){
				$codeExists = FALSE;
			}
		}
	}
	$form .=  add_hidden_fields(array("user" => $user, "key" => $key));
	$form .= qtag("hidden", $col_code, "c", "colCode");
	//$form .=  qtag("submit", "Spara");
	/* $translationArray contains the column-headers from the sql-Table as the keys and the desired column names of the html-Table as the values. */ 
	$translationArray = array("index" => "Index", "subject_id" => "Ämne", "year" => "Åk", "topic" => "Tema", "content" => "Innehåll");
	$wantedColumns = array_keys($translationArray);
	
	/* rowsToView will contain all the indices that either belong to a certain collection (given by $_REQUEST["c"]) 
		or are given directly via $_REQUEST["t"], seperated by comma 
	*/
	
	
	$rowsToView = explode(",", $rowsToView);
	$criteriaArray = array("OR", array("index" => array()));
	foreach ($rowsToView as $index) {
		$criteriaArray[1]["index"][] = $index;
	}
	
	$chosenContentTable = sql_select("lgr_centralcontent", $criteriaArray);
	
	$criteriaArray[0] = "AND";
	$nonChosenContentTable = sql_select("lgr_centralcontent", $criteriaArray, "all", TRUE);
	
	$tableTable = array("chosen" => $chosenContentTable, "notChosen" => $nonChosenContentTable);
	
	foreach($tableTable as $tableKey => $table){
		$table = array_choose_columns($table, $wantedColumns);
		$table = array_change_col_names($table, $translationArray);
		
		foreach($table as $rowNumber => $row){
			$checkbox = qtag("checkbox", "indexArray", $row["Index"], $tableKey == "chosen");
			$table[$rowNumber] = array("Spara" => $checkbox) + $row;
		}
		$tableTable[$tableKey] = $table;
	}
	
	if($view != "table"){
		if($oldColCode){
			$theList = $tableTable["chosen"];
		}
		else {
			$theList = $tableTable["notChosen"];
		}
		$theList = array_orderby($theList, "Index");
		
		foreach($theList as $row => $rowContent){
			$li = qtag("fa", "arrows-v", "2x");
			$li .= $rowContent["Spara"] . $rowContent["Index"] . ": " .  $rowContent["Innehåll"];
			$current_id = "indexArray_" . $rowContent["Index"];
			$li_atts = array("id" => $current_id);
			
			if(isset($ini_array["topic_colors"][$rowContent["Ämne"]])){
				$color = $ini_array["topic_colors"][$rowContent["Ämne"]];
			}
			$color = ($color == "" ? FALSE : $color);
			if($color){$li_atts["style"] = 'background-color:#' . $color;}
			$ul .= tag("li", $li, $li_atts);
		}
		$leftdiv = tag("ul", $ul, array("id" => "contentlist"));
		$rightdiv = "";
		$row = qtag("div", $leftdiv, "col-md-8") . qtag("div", $rightdiv, "col-md-4");
		$container = qtag("div", $row, "row");
		$form .= qtag("div", $container, "container");
	}
	else{
		$form .=  create_htmltable_from_array($tableTable["chosen"], "chosen");
		
		$form .=  create_htmltable_from_array($tableTable["notChosen"], "notChosen");
		// "notChosen" => $nonChosenContentTable
	}
	$container = tag("form", $form, array("action" => "update.php?type=collection", "method" => "post"));
	$body .= tag("div", $container, "container");
	$html .= tag("body", $body);
	echo tag("html", $html);
?>

