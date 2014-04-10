<?php

// class die de html pagina bouwt

class pageloader{


function __construct($page=false){
	if(isset($page) && ctype_alpha(str_replace('-','',$page))){ // aplhabet en - laten we toe als pagenames ..
		$this->page = $page;
		$this->load_page($page);
	}else{
		utils::throwExcption('No page defined !!! or page is not aplhabetic');	
	}
}

function load_page($page){
		$this->pagefile = PAGE_PATH.$page.".php";
		if(file_exists(PAGE_PATH.$page.".php")){
			
			require_once($this->pagefile);
			$this->pageconfig = $pageconfig;
			
			if($this->pageconfig['private']==true){
				if(!user::getInstance()->getValidated()){
					unset($pageconfig);
					$this->pagefile = PAGE_PATH."notloggedin.php";
					require_once($this->pagefile);
					$this->pageconfig = $pageconfig;
				}
			}
		}else{
			$this->pagefile = PAGE_PATH."404.php";
			require_once($this->pagefile);
			$this->pageconfig = $pageconfig;
			$this->setHeaders(404);
		}
		
		if(is_array($this->pageconfig) && count($this->pageconfig)>0){
			$this->create_page();
		}else{
			utils::throwExcption('Pageconfig array not found !!!');
		}
}

	function setHeaders($code)
	{
		$code .= ' ' . $this->headercodes[strval($code)];
		header("{$_SERVER['SERVER_PROTOCOL']} $code");
	}
	
	function setCustomHeaders($text){
		header("$text");	
	}
	
	private $headercodes = array(
		'100' => 'Continue',
		'200' => 'OK',
		'201' => 'Created',
		'202' => 'Accepted',
		'203' => 'Non-Authoritative Information',
		'204' => 'No Content',
		'205' => 'Reset Content',
		'206' => 'Partial Content',
		'300' => 'Multiple Choices',
		'301' => 'Moved Permanently',
		'302' => 'Found',
		'303' => 'See Other',
		'304' => 'Not Modified',
		'305' => 'Use Proxy',
		'307' => 'Temporary Redirect',
		'400' => 'Bad Request',
		'401' => 'Unauthorized',
		'402' => 'Payment Required',
		'403' => 'Forbidden',
		'404' => 'Not Found',
		'405' => 'Method Not Allowed',
		'406' => 'Not Acceptable',
		'409' => 'Conflict',
		'410' => 'Gone',
		'411' => 'Length Required',
		'412' => 'Precondition Failed',
		'413' => 'Request Entity Too Large',
		'414' => 'Request-URI Too Long',
		'415' => 'Unsupported Media Type',
		'416' => 'Requested Range Not Satisfiable',
		'417' => 'Expectation Failed',
		'500' => 'Internal Server Error',
		'501' => 'Not Implemented',
		'503' => 'Service Unavailable'
	);


function generate_metas(){
	if(isset($this->pageconfig['title'])){
		$this->html_meta .= "<title>".$this->pageconfig['title']." - Dota2essentials.com</title>\n";	
	}
	if(isset($this->pageconfig['keywords'])){
		$this->html_meta .= "<meta name='keywords' content='".$this->pageconfig['keywords']."'>\n";	
	}
	if(isset($this->pageconfig['description'])){
		$this->html_meta .= "<meta name='description' content='".$this->pageconfig['description']."'>\n";	
	}
}

function outputMetas(){
	return $this->html_meta;	
}


function create_page(){
	$this->html_page = '<div id="message_wrap">';
	$this->html_page .= '<div class="message_header">Server Message</div>';
	$this->html_page .= '<div id="message_contents">Server Message</div>';
	$this->html_page .= '<div class="message_footer">';
	$this->html_page .= '<div id="confirm_btn" class="message_button">OK</div>';
	$this->html_page .= '</div></div>';
	
	if($this->pageconfig['columns'] == 2){
		$this->html_page .= '<div id="contentleft">';
		//$this->html_page .= '<div id="responseoverlay"></div>';
		$this->html_page .= $this->load_html_modules($this->pageconfig['maincontent']);
		$this->html_page .= '</div>';
		$this->html_page .= '<div id="contentright">';
		$this->html_page .= $this->load_html_modules($this->pageconfig['sidebar']);
		$this->html_page .= '</div>';
	}elseif($this->pageconfig['columns'] == 1){
		$this->html_page .= '<div id="contentwide">';
		$this->html_page .= $this->load_html_modules($this->pageconfig['maincontent']);
		$this->html_page .= '</div>';		
	}else{
		utils::throwExcption('wrong column amount specified !!!');
	}
}

function load_html_modules($modules){
	if(is_array($modules) && count($modules)>0){
		foreach($modules as $module){
			if(is_array($module)){
				$this->arguments = $module;
				$module = $module['module'];	
			}
			if($this->arguments['private']==true){
				if(!user::getInstance()->getValidated()){
					return true;	
				}
			}
			$file = MODULE_PATH.$module.'.php';
			if(file_exists($file)){
				ob_start();
				include($file);
				$html .= ob_get_clean();
				$html .= '<div class="clear"></div>';
			}else{
				$html .= 'Failed to load HTML block';
			}
			unset($this->arguments);
		}
		return $html;
	}else{
		utils::throwExcption('no modules specified !!!');	
	}	
}

function output_page(){
	return $this->html_page;
}
	
}


?>