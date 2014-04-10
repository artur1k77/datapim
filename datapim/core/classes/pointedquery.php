<?
class pointedquery{
	private $queryLocation = 0;
	private $pageSize =75;
	private $ignorePointers = false;
	private $objects = array();
	public $messages = array();
	
	function __construct() {
	}
	
	public function setQueryLocation($loc) {
		if(!empty($loc) && is_numeric($loc)) {
			$this->queryLocation = $loc;	
		}
	}
	
	public function getQueryLocation() {
		return $this->queryLocation;	
	}
	
	public function incrementQueryLocation() {
		$this->queryLocation += $this->pageSize;	
	}
	
	public function resetQueryLocation() {
		$this->queryLocation = 0;	
	}
	
	public function setPageSize($pSize) {
		if(!empty($pSize) && is_numeric($pSize) && $pSize<=250) {
			$this->pageSize = $pSize;	
		}
	}
	
	public function getPageSize() {
		return $this->pageSize;	
	}
	
	public function setIgnorePointers() {
		$this->ignorePointers = true;	
	}
	
	public function setObjects($objects) {
		if(!empty($objects) && is_array($objects)){
			$this->objects = $objects;
		}
	}
	
	public function getObjects() {
		return $this->objects;	
	}
	
	function createLimitString() {
		if($this->ignorePointers) {
			return '';
		} else {
			return ' LIMIT '.$this->getQueryLocation().','.$this->getPageSize();
		}
	}
	
	function renderAsJson($extended=false) {
		$returnHtml='';
		if(!empty($this->objects)) {
			foreach($this->objects as $object) {
				$returnHtml.=$object->getHtmlRenderString($extended);
			}
		}
		$return['messages'] = $this->messages;
		$return['queryLoc'] = $this->queryLocation;
		$return['pageSize'] = $this->pageSize;
		$return['htmlBody'] = $returnHtml;
		echo json_encode($return);
	}
}
?>
