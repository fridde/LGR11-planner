<?php
	/* PREAMBLE */
    $url = "https://raw.githubusercontent.com/fridde/friddes_php_functions/master/include.php";
    $content = file_get_contents($url); 
    if($content != FALSE){
        file_put_contents("include.php", $content); 
	}
    include "include.php";
	/* END OF PREAMBLE */
	
	inc("fnc,sql,pdown");
	
	list($html, $head, $body, $nav, $form, $h1, $ul) = array_fill(0,20,""); 
	
	$text = file_get_contents("README.md");
	$Parsedown = new Parsedown();
	
	$head .= qtag("meta");
	$head .= tag("title", $col_name . " - LGR11-planerare");
	$html .= tag("head", $head);
	
	$container = $Parsedown->text($text);
	$body .= tag("div", $container, "container");
	$html .= tag("body", $body);
	echo tag("html", $html);
	
	
	