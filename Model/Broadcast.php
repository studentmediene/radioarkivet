<?php
/********************************
 * @AUTHOR: Christian Wallervand*
 ********************************/
class Broadcast { 
	private $id;
	private $name;
	private $remark;
	private $filename;
	
	public function Broadcast($id, $name, $remark, $filename) {
		$this->id = $id;
		$this->name = $name;
		$this->remark = $remark;
		$this->filename = $filename;
	}
	
	public function setID($id) {$this->id = $id;}
	public function getID() {return $this->id;}
	public function setName($name) {$this->name = $name;}
	public function getName() {return $this->name;}
	public function setRemark($remark) {$this->remark = $remark;}
	public function getRemark() {return $this->remark;}
	public function setFilename($filename) {$this->filename = $filename;}
	public function getFilename() {return $this->filename;}
}