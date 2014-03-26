<?php
/********************************
 * @AUTHOR: Christian Wallervand*
 ********************************/
 class Show {
 	private $id;
 	private $name;
 	private $imgPath;
 	
 	private $smallImgPath;
 	private $largeImgPath;
 	
 	private $broadcasts; //Array
 	private $infoShort;
 	private $infoLong;
 	
 	public function Show($id, $name) {
 		$this->id = $id;
 		$this->name = $name;
 	}
 	
 	public function setID($id) {$this->id = $id;}
 	public function getID() {return $this->id;}
 	public function setName($name) {$this->name = $name;}
 	public function getName() {return $this->name;}
 	public function setImgPath($imgPath) {$this->imgPath = $imgPath;}
 	public function getImgPath() {return $this->imgPath;}
 	
 	public function setSmallImgPath($smallImgPath) {$this->smallImgPath = $smallImgPath;}
 	public function getSmallImgPath() {return $this->smallImgPath;}
 	public function setLargeImgPath($largeImgPath) {$this->largeImgPath = $largeImgPath;}
 	public function getLargeImgPath() {return $this->largeImgPath;}
 	
 	public function setBroadcasts($broadcasts) {$this->broadcasts = $broadcasts;}
 	public function getBroadcasts() {return $this->broadcasts;}
 	public function setInfoShort($infoShort) {$this->infoShort = $infoShort;}
 	public function getInfoShort() {return $this->infoShort;}
 	public function setInfoLong($infoLong) {$this->infoLong = $infoLong;}
 	public function getInfoLong() {return $this->infoLong;}
}
?>
