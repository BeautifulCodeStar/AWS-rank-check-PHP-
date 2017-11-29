<?
define("_WOJO", true); 
require_once("../init.php");
include 'RankChecker.php'; 

?>
<?php if(Membership::is_valid([1])): ?>
<?php $user_id = Auth::$userdata->id;?>

<h1>!!!!User has valid login ID:<?=$user_id;?></h1>

<?php
	
	
	$RankChecker = new RankChecker();

	//first check if deleting an asin
	if (isset(($_GET['asin']))) {
		$asin = $_GET['asin'];
		$RankChecker->removeAsin($asin,$user_id);
		//deleted now continue
	}


	//get users saved list of keywords and asins
	$kwlist = $RankChecker->getSavedKeywordList($user_id);

	$RankChecker->pretty($kwlist);




	foreach ($kwlist as $asinObj) {
		echo 'selected: ' . $asinObj['asin'] . ' - <a href="index.php?asin='.$asinObj['asin'].'">delete</a><br/><br/>';

		$keywords = explode(',', $asinObj['keywords']);

		if (count($keywords) > 20) {
			$keywords = array_slice($keywords, 0, 20);
		}
		foreach ($keywords as $keyword) {
			//now we have the asin and the keywords to run api on
			$searchResults = $RankChecker->searchAsinKeyword( $asinObj['asin'], $keyword );
		}

		echo '<br/><br/>--------------<br/>';
	}



	
?>





<? if (!isset($_POST['submit_asin_keyword'])) {

	echo '<h2>create new keywords set for asin</h2>';
	echo '<form action="keyword_submit.php" method="post">';
	echo '<input type="text" style="width:250px" name="asin" placeholder="asin" /><br/>';
	echo '<input type="text" style="width:250px" name="keyword" placeholder="keywords seperated by comma" /><br/>';
	echo '<input type="hidden" name="userid" value='.$user_id.' />';
	echo '<input type="submit" name="submit_asin_keyword"/>';

} ?>

<?php else: ?>

<h1>User membership is't not valid. Show your custom error message here</h1>

<?php endif; ?>
