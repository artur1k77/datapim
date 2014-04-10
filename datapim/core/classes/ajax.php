<?

class ajax{
	
	
	function __construct($request){
		if(is_array($request) && count($request)>0){
			$this->request = $request;
			$this->load_request_params();
		}else{
			$this->response('request failed.');	
		}
	}
	
	function load_request_params(){
		if($this->request['ajax'] == 1 && isset($this->request['request'])){
			if(file_exists(AJAX_PATH.$this->request['request'].'.php')){
				// maybe sanitizen geen id of dat gaat boeien .. vast wels
				$this->ready = true;	
			}else{
				$this->ready = false;
			}
		}else{
			$this->response('request failed no params specified.');	
		}
	}
	
	function speciale_ajax_request_afvangen(){
		// ja dat moeten we vast nog doen
		// zoals dingen ...	
	}
	
	function call_ajax(){
		global $mysqli;
		if($this->ready){
			ob_start();
			include(AJAX_PATH.$this->request['request'].'.php');
			$this->ajaxresponse = ob_get_clean();
		}
	}
	
	function response($overwritemsg=false){
		if($overwritemsg){
			echo $overwritemsg;
		}else{
			echo $this->ajaxresponse;
		}
	}
}

?>