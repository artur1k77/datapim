<?

if(isset($this->request['filter'])){
	$queryLoc = $this->request['queryLoc'];
	$pageSize = $this->request['pageSize'];
	
	$pointedQuery = new pointedquery();
	$pointedQuery->setQueryLocation($queryLoc);
	$pointedQuery->setPageSize($pageSize);
	
	$filter = $this->request['filter'];
	if($filter && !empty($filter)){
		$where = " WHERE `name` LIKE '%".database::getInstance()->real_escape_string($filter)."%' ";
	}
	
	$livestreams  = new livestreams();
	$pointedResults=$livestreams->getLivestreams($where, $pointedQuery);
	
	$pointedResults->renderAsJson(false);
	//echo $livestreams->renderLivestreams();
}




?>