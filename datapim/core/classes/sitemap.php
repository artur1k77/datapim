<?


class sitemap{
	
	function __construct($domain,$directory){
		if(!empty($domain)  && !empty($directory) ){
			$this->domain = $domain;
			$this->directory = $directory;	
		}else{
			return false;	
		}
	}


	function generateSitemap($array,$type){
		$i=1;
		foreach($array as $sitemap){
			$temp = $this->sitemapStart();
			if($type==1){
				foreach($sitemap as $result){
					$temp .= '<url>'; 
					$temp .= "\n";
					$temp .= '<loc>http://'.$this->domain.'/films/'.$this->safeurl($result['Naam']).'/'.$result['ID'].'/</loc>';
					$temp .= "\n";
					$temp .= '</url>';
					$temp .= "\n";
				}
			}
			if($type==2){
				foreach($sitemap as $result){
					$temp .= '<url>'; 
					$temp .= "\n";
					$temp .= '<loc>http://'.$this->domain.'/films-nieuws/'.$result['id'].'/'.$this->safeurl($result['titel']).'/</loc>';
					$temp .= "\n";
					$temp .= '</url>';
					$temp .= "\n";
				}
			}
			$temp .= $this->sitemapEnd();
			if($type==1){
				$this->saveSitemap($temp, ''.$this->directory.'/sitemapmovies'.$i.'.xml');
			}
			if($type==2){
				$this->saveSitemap($temp, ''.$this->directory.'/sitemapnieuws'.$i.'.xml');
			}
			unset($temp,$sitemap);
			$i++;
		}
	}
	
	function sitemapStart(){
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= "\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';
		$xml .= "\n";
		return $xml;
	}
	
	function sitemapEnd(){
		$xml = '</urlset>';
		return $xml;
	}
	
	function saveSitemap($data,$filename){
			file_put_contents($filename, $data);
	}
	
	function generateSitemapIndex(){
		$dir = $this->directory;
		$files = scandir($dir);
		unset($files[0],$files[1],$files[2]); // .. . generateindex.php
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= "\n";
		$xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$xml .= "\n";
		foreach($files as $file){
			$xml .= '<sitemap>';
			$xml .= "\n";
			$xml .= '<loc>http://'.$this->domain.'/sitemap/'.$file.'</loc>';
			$xml .= "\n";
			//$xml .= '<lastmod>2004-10-01T18:23:17+00:00</lastmod>';
			$xml .= '</sitemap>';
			$xml .= "\n";	
		}
	   $xml .= '</sitemapindex>';
	   $this->saveSitemap($xml,''.$this->directory.'/sitemapindex.xml');
	}
	
	function safeurl( $v ){
        $v = strtolower( $v );
        $v = preg_replace( "/[^a-z0-9]+/", "-", $v );
        $v = trim( $v, "-" );
        return $v;
	}
} 

?>