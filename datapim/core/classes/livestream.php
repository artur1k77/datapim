<?
	class livestream{
		public $twitchid;
		public $previewmedium;
		public $status;
		public $name;
		public $logo;
		public $viewers;
		
		
		function getHtmlRenderString($extended){
			$ls .= '<a href="/livestream/'.$this->twitchid.'/" style="display:block;">';
			$ls .= '<div class="livestreamwrapper" id="'.$this->twitchid.'">';
			
			$ls .= '<div class="livestreamhover '.$this->twitchid.'" style="display:none;"><img src="'.$this->previewmedium.'" width="150px">'.$this->status.'</div>';
				
			$ls .= '<div class="livestreamchannelname">'.$this->name.'</div>';
			$ls .= '<div class="livestreamlogo"><img  src="'.$this->logo.'" width="150px"></div>';
			$ls .= '<div class="livestreamchannelname">Viewers: '.$this->viewers.'</div>';
			
			$ls .= '</div></a>';
			
			return $ls;	
		}	
	}
?>