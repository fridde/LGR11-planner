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

	list($html, $head, $body, $form1, $form2, $tab1, $tab2, $col_1) = array_fill(0,20,"");
	$head .= qtag("meta");
	$head .= inc("jquery,jqueryUIjs, jqueryUIcss,css,bootcss,bootjs,boottheme,fAwe,init,css" , FALSE, TRUE);
	$head .= tag("title", "LGR11 - Login");
	
	$html .= tag("head", $head);
	
	if($messageCode){
		$messageText = "OBS: " . $ini_array["messages"][$messageCode];
		$body .= tag("p", $messageText, "bg-danger");
	}
	
	$mailInput = qtag("textinput", "user", "Mejladress");
	$keyInput = qtag("textinput", "key", "Lösenord");
	$form1 .= tag("p", $mailInput) . tag("p", $keyInput);
	$form1 .= tag("button", "Logga in", array("type" => "submit", "formaction" => "update.php?type=login"));
	
	$tab1 .= tag("h1", "Logga in") . tag("form", $form1, array("method" => "post"));
	
	$form2 .= tag("p", $mailInput);
	$form2 .= tag("button", "Skapa ny användare", array("type" => "submit", "formaction" => "update.php?type=newUser"));
	$form2 .= tag("button", "Jag har glömt mitt lösenord", array("type" => "submit", "formaction" => "update.php?type=forgottenEmail"));
	
	$tab2 .= tag("h1", "Ny användare") . tag("form", $form2, array("method" => "post"));
	
	$tabs = qtag("tabs", "", array("Logga in" => $tab1, "Ny användare" => $tab2));
	// "", tabcontent
	$back_to_start = qtag("a", "Tillbaka till startsidan", "index.php");
	$col_1 .= tag("div", $tabs . $back_to_start , "col-md-10 col-md-offset-2");
	$container = tag("div", $col_1, "row");
	$body .= tag("div", $container, "container");
	
	$html .= tag("body", $body);
	echo tag("html", $html); 
