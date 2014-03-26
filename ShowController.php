<?php
/********************************
 * @AUTHOR: Christian Wallervand*
 ********************************/


require_once "DBC/BroadcastDBC.php";
require_once "DBC/ShowDBC.php";




$showDBC = new ShowDBC();
$broadcastDBC = new BroadcastDBC();


$showID = $_POST["showID"];

/***********************************************************
 * Function for securrity assurance						   *
 * If ShowID is tampered with before getting to the server,* 
 * the server will return an error message for the use	   *
 * Returns true if the show excists in the database	   	   *
 * *********************************************************/
function showExcists($sID) {
	$result = false;
	global $showDBC;
	$shows = $showDBC->selectAllShows();
	//Alternatvie way of traversing arrays
	foreach ($shows as $show) {
		if ($sID == $show->getID()) {
			$result = true;
			break;
		}
	}
	return $result;
}

/******************************************************
 *StreamOnDemand.php is entered without any parameters*
 ******************************************************/
if (is_null($showID)) {
	echo "<table id=\"showsTable\" class=\"content\">";
	$shows = $showDBC->selectAllShows();
	for ($i = 0; $i < count($shows); $i++) {
		//$lastBroadcastsForShow = $broadcastDBC->selectLastBroadcastForShow($shows[$i]->getID());
		echo "<tr onclick=\"getShowInfo(".$shows[$i]->getID().")\">
				<td class=\"showsTableTd1\"><img class=\"showImgSmall\" src=\"".$shows[$i]->getSmallImgPath()."\" /></td>
				<td class=\"showsTableTd2\">
					<p class=\"showNameP\">".$shows[$i]->getName()."</p>
					<p class=\"showInfoShort\">".$shows[$i]->getInfoShort()."</p>
			  	</td>
			  </tr>";
	}
	echo "</table>";
}

/********************************************************************
 * Used when a show is selected (parameter showID is set in the url)*
 ********************************************************************/
if (is_numeric($showID) && showExcists($showID)) {
	try {
	$show = $showDBC->selectShow($showID);
	$broadcastsForShow = $broadcastDBC->selectBroadcastsForShow($showID);
	echo "<div id=\"grayBoxContainer\"class=\"grayBox\">
			<div id=\"showInfoDiv\" class=\"grayBox\">
		  		<img id=\"showImgLarge\" src=\"".$show->getLargeImgPath()."\" />
		  		<div id=\"showInfoContentDiv\">
		  			<p class=\"showHeadline\">".$show->getName()."</p>
		  			<p class=\"showInfoLong\">".$show->getInfoLong()."</p>
		  		</div>
		  	</div>
		  </div>";
	
	/*echo "<div id=\"podcastDiv\" class=\"grayBox\">
			<p class=\"headline\">Også som podkast:</p>
	      </div>";*/
	
	
	echo "<table id=\"broadcastsTable\" class=\"content\">";
	for ($i = 0; $i < count($broadcastsForShow); $i++) {
		//class in just used for the javascript in StreamOnDemand.php to get filename
		echo "<tr id=\"".$broadcastsForShow[$i]->getID()."\" class=\"".$broadcastsForShow[$i]->getFilename()."\"  onclick=\"playSound(".$broadcastsForShow[$i]->getID(). ",'" .$broadcastsForShow[$i]->getFilename()."', '".$broadcastsForShow[$i]->getName()."')\">
					<td class=\"broadcastsTableTd1\"><img id=\"img".$broadcastsForShow[$i]->getID()."\" class=\"playImgTd\" src=\"img/play.png\"/></td>
					<td class=\"broadcastsTableTd2\">
						<p id=\"broadcastTitle".$broadcastsForShow[$i]->getID()."\" class=\"broadcastNameP\">".$broadcastsForShow[$i]->getName()."</p>
						<p class=\"broadcastInfoP\">".$broadcastsForShow[$i]->getRemark()."</p>
					</td>
				</tr>";
	}
	echo "</table>";
	}
	catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		echo "<p>Exception</p>";
		
	}
}
?>

