<?php
/********************************
 * @AUTHOR: Christian Wallervand*
 ********************************/
require_once "Util/DButil.php";	
require_once "Util/GeneralUtil.php";
require_once "Model/Broadcast.php";
class BroadcastDBC {
	
	//Select podcasts that belongs to a given program
	protected $QUERY_SELECT_BROADCASTS_FOR_SHOW 		= "SELECT o.title, o.refnr, o.filename, o.remark
									   	   		   		   FROM ondemand o
									   	   		  	 	   WHERE o.program = ? AND o.softdel = 0
									   	   		   		   ORDER BY o.createdate DESC";
	
	
	public function selectBroadcastsForShow($sID) {
		//Security check
		if (is_numeric($sID)) {
			//$con = new mysqli(DButil::MYSQL_SERVER, DButil::MYSQL_USER_NAME, DButil::MYSQL_PASSWORD, DButil::DB_NAME);
			$con = DButil::connectToFileserver();
			if (DButil::DBconnection($con, DButil::DB_NAME_FILESERVER)) {		
				if ($stmt = $con->prepare($this->QUERY_SELECT_BROADCASTS_FOR_SHOW)) {
					//The first param indicates that $program is expected to be a integer
					$stmt->bind_param("i", $show);
					$show = $sID;
					$stmt->execute();
					$stmt->bind_result($title, $refnr, $filepath, $remark);
				}
				$list = array();
				while($stmt->fetch()) {
				
					//Remove the time from the broadcast title
					$title = GeneralUtil::encode(GeneralUtil::removeTime($title));
					$filename = GeneralUtil::pathTail($filepath);
					$remark = GeneralUtil::encode($remark);
					$broadcast = new Broadcast($refnr, $title, $remark, $filename);
					array_push($list, $broadcast);
				}
				$con->close();
			}
			return $list;
		}
	}
}
?>
