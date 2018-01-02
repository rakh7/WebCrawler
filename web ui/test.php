<?php
	// require_once('SpellCorrector.php');
	// $term = "school";
	// $corrector = new SpellCorrector();
	//$checkTerm = SpellCorrector::correct($term);
	// $checkTerm = SpellCorrector::known_edits2($term);
	// echo $checkTerm."askjd";
	//if($checkTerm == $term)
	//	echo "same";
	//else
	// try{
			
	// 		$checkTerm = SpellCorrector::correct($term);
	// 		echo $checkTerm."askjd";
	// 		if($checkTerm == $term)
	// 			echo "same";
	// 		else
	// 			echo "incorrect";
	// 	}catch(Exception $e) {
	// 	  echo 'Message: ' .$e->getMessage();
	// 	}
	// echo "asd";
	//$content = file_get_contents("big.txt");
	// echo ".";
	//$temp = preg_replace("/[^a-zA-Z\t\n]+/", " ", $content);
	//var_dump($temp);
	//$temp2 = strtolower($content);
	// $matches = array();
	//echo $temp2;
	//var_dump(preg_match("/[a-z]+/", $temp2,$matches));
	//$matches = explode(" ", $content);
	//preg_match_all("/[a-z]+/", mb_strtolower($text,'UTF-8'), $matches);
    //var_dump($matches);
//     $file = fopen("big.txt","r");
// $counter = 0;
// while(! feof($file))
//   {

//   //echo strtolower(fgets($file)). "<br />";
//   	preg_match_all("/[a-z]+/", strtolower(fgets($file)), $match);
//   	var_dump($match[0]);
//   	foreach ($match[0] as $key => $value) {
//   		# code...
//   		array_push($matches, $value);
//   		$counter++;
//   	}
  	
  	
//   	// var_dump($match);
//   }

//   var_dump($matches[0]);

// fclose($file);

	// $dir = opendir("/Users/rakshithr/Documents/USC/CSCI572/Assignment3/solr-5.3.1/downloads");
ini_set('max_execution_time', 300);
$dir = new DirectoryIterator(dirname("/Users/rakshithr/Documents/USC/CSCI572/Assignment3/solr-5.3.1/downloads/."));
$file = fopen("big7.txt","w");
$data = "";
$i = 0;
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
    	$theContent = file_get_contents("/Users/rakshithr/Documents/USC/CSCI572/Assignment3/solr-5.3.1/downloads/".$fileinfo->getFilename());
        $data = strip_tags(str_replace('<', ' <', $theContent));
        preg_match_all("/[a-zA-Z]+/", $data, $data2);
        
        foreach ($data2 as $key) {
        	$j = count($key);
        	for ($i=0; $i < $j; $i++) { 
        		fwrite($file, $key[$i]);
        		fwrite($file, " ");
        	}
        	
        }
        fwrite($file, " ");
        //var_dump($data2);
		#fwrite($file, "\n");
    }
}
fclose($file);
	// $file = fopen("big.txt","r");
	// 	$counter = 0.0;
	// 	$temp = 0.0;
	// 	while(! feof($file))
	// 	{

	// 	  //echo strtolower(fgets($file)). "<br />";
	// 	  	preg_match_all("/[a-z]+/", strtolower(fgets($file)), $match);
	// 	  	foreach ($match[0] as $key => $value) {
	// 	  		# code...
	// 	  		$matches[$counter] = $value;

	// 	  		$counter = $counter + 1.0;
	// 	  		echo $counter."-".$value."<br/>";
	// 	  	}
		  	
	// 	  	$temp = $temp + 1.0;
		  	
	// 	  	// var_dump($match);
	// 	}
	// 	fclose($file);
 //    echo "aslkdj";
?>