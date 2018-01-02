<?php
	header("Content-Type:text/html;charset=utf-8");
	header('Access-Control-Allow-Origin: http://localhost:8983');
	require_once('SpellCorrector.php');
	ini_set('memory_limit', '1000M');

	ini_set('display_errors', 1);
	$limit = 50;
	$query = isset($_GET['q'])? $_GET['q']:false;
	// $core = isset($_GET['pagerank'])? "solr/SCrawler/":"solr/RCrawler/";
	//$core = $_GET['pagerank'];
	$currentDoc = null;
	$previousDoc = null;
	$results = false;
	if($query){
		try{

			require_once('Apache/Solr/Service.php');
			$solr = new Apache_Solr_Service('localhost',8983, "solr/SCrawler/");
			if(get_magic_quotes_gpc() == 1){
				$query = stripcslashes($query);
			}
			$addParam = array('sort' => 'pageRankFile desc');
			if($_GET['pagerank'] == "defaultPR")
			 	$results = $solr->search($query, 0, $limit);
			else{
				$results = $solr->search($query, 0, $limit, $addParam);
			}
		}catch(Exception $e){
			die("<html><head><title>SEARCH EXCEPTION</title></head><body><pre>{$e}</pre></body></html>");
		}
	}
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Marshall School Search</title>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script type="text/javascript" src="PorterStemmer.js"></script>
</head>
<body>
<!-- Form that will accept the search query term and return the results. -->
<!-- Also contains the radio button to select which page rank algorithm to run -->
<form accept-charset="utf-8" method="get" action="index.php">
	<div class="ui-widget">
		<label for="q">Search:</label>
		<input id="q" name="q" type="text" value="<?php if(isset($_GET['q'])) echo $_GET['q'];?>" onkeyup="getAutoSuggestions()" />
	</div>
	<br/><br/>
	<label for="pagerank"></label>
	<input type="radio" name="pagerank" value="defaultPR" <?php if(empty($_GET['pagerank'])) echo "checked"; elseif ($_GET['pagerank'] == 'defaultPR') {
		echo "checked";
	}?> >default Solr </input><input type="radio" name="pagerank" value="modifiedPR" <?php 
		if(isset($_GET['pagerank']) && $_GET['pagerank']=="modifiedPR")
			echo "checked";
	?> >external pagerank </input><br/><br/>
	<input type="submit"></input>
	<br/><br/>
</form>
<?php
// If any results are found
if($results){
	$total = (int)$results->response->numFound;
	$start = min(1, $total);
	$end = min($limit, $total);
	$count = 0;
	$terms = explode(' ', trim($_GET['q'],' '));
	$checkTerm = "";
	$insteadFor = "";
	$errorFound = 0;
	foreach ($terms as $term) {
		try{
			$insteadFor = $term . " ";
			$checkTerm = $checkTerm . SpellCorrector::correct($term) . " ";
			$currTerm = SpellCorrector::correct($term);
			if($currTerm == $term)
				echo "";
			else{
				$errorFound = 1;
			}
		}catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();
		}
	}
	if($errorFound == 1){
		$didYouMean = "http://localhost:8888/572/index.php?q=".$checkTerm."&pagerank=".$_GET['pagerank'];
		$searchInsteadFor = "http://localhost:8888/572/index.php?q=".$insteadFor."&pagerank=".$_GET['pagerank'];
		//echo $didYouMean;
		echo "Showing results for: "; ?><a href="<?php echo $searchInsteadFor;?>"><?php echo $insteadFor;?></a><br/><?php
		echo "Did you mean: "; ?><a href="<?php echo $didYouMean;?>"><?php echo $checkTerm;?></a><br/><?php
		
		$errorFound = 0;
		
	}
?>
<div>
	Results <?php echo $start;?> - <?php if($total > 10 ) echo "10"; else echo $total;?> of <?php echo $total?>:
</div>
<ul>
	<?php
		// $count = 0;
		$checkContent = false;
		$urlMapper = array();
		if (($handle = fopen("urlTracker.csv", "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			    $urlMapper[$data[1]] = $data[0];
			}
			fclose($handle);
		}
		//var_dump($urlMapper);
		$dupUrl = array();
		$hash = "";
		foreach($results->response->docs as $doc) {
			
			$previousDoc = $currentDoc;
			$filePath = "file:///".$doc->id;
			$currentDoc = file_get_contents($filePath);
			$files = explode("/", $doc->id);
			$fileSize = sizeof($files);
			try{
				if(empty($urlMapper[$files[$fileSize-1]]))
					continue;
			}catch(Exception $e){
				continue;
			}
			$fileName = $urlMapper[$files[$fileSize-1]];
			$hash = md5(strip_tags($currentDoc));
			if(array_key_exists($hash, $dupUrl))
				$checkContent = true;
			else{
				$count++;
				$checkContent = false;
				$dupUrl[$hash] = 0;
				// echo $dupUrl[$hash];
				
			}
			// if(md5(strip_tags($currentDoc)) == md5(strip_tags($previousDoc))){
			// 	$checkContent = true;
			// }else{
			// 	$count++;
			// 	$checkContent = false;
			// }
			if($count==11)
				break;
			if($checkContent == false){
			?>
				<li>
					<p><a href="<?php echo $fileName;?>">Document</a></p>
					<p>Title: <?php echo " ".$doc->title?> </p>
					<p>
					Author : <?php
								if(isset($doc->author))
									echo $doc->author;
								else
									echo "NA";
					         ?>
					</p>
					<p>
					File size : <?php
									if(isset($doc->stream_size))
										echo ($doc->stream_size/100)."KB";
									else
										echo "NA";
					            ?>	
					</p>
					<p>
					Date created : <?php
					                  if(isset($doc->date_created))
					                  	echo $doc->date_created;
					                  else
					                  	echo "NA";
					                  ?>
					</p>

				</li>
			<?php 
	    	}
		}
	?>
</ul>
<?php
}?>
 <script type="text/javascript" src="app.js"></script>
</body>
</html>