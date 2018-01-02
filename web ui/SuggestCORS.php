<?php
	require_once('SpellCorrector.php');
	$query = $_GET['query'];
	//$checkTerm = SpellCorrector::correct($query);
	//echo $checkTerm."hi";
	$contURL = "http://localhost:8983/solr/SCrawler/suggest?q=" . rawurlencode($query) . "&wt=json&indent=true";
	$content = file_get_contents($contURL);
	echo $content;
?>
