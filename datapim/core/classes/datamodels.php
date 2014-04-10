<?

class datamodels{
	
	public $model = false;
	
	public function getModel($model){
		if(ctype_alpha($model)){
			$this->loadModel($model);
		}else{
			utils::throwExcption('No Model specified or model is not alphabetic.');
		}
	}
	
	public function loadModel($model){
			if(method_exists($this,$model)){
				$this->$model();
			}else{
				utils::throwExcption('Model not found ....');
			}
			if($this->model){
				return $this->model;	
			}
	}
	
	public function Cosmetics(){
		
		$model['config']['img_path'] = MEDIA_PATH_COSMETICS;
		
		// keys = api naam kolom naam , value = database naam of filename ingeval van images
		$model['databasemodel']['items']['tablename'] = 'cosmetics';
		$model['databasemodel']['items']['updateonduplicate'] = true;
		$model['databasemodel']['items']['savetype'] = 'multi';
		$model['databasemodel']['items']['images'] = array(
			'image_url',
			'image_url_large'
		);
		$model['databasemodel']['items']['dataset'] = array(
			'name'=>'name',
			'defindex'=>'defindex',
			'item_class'=>'item_class',
			'item_type_name'=>'item_type_name',
			'item_name'=>'item_name',
			'item_description'=>'item_description',
			'item_set'=>'item_set',
			'proper_name'=>'proper_name',
			'item_quality'=>'item_quality',
			'image_inventory'=>'image_inventory',
			'min_ilevel'=>'min_ilevel',
			'max_ilevel'=>'max_ilevel',
			'image_url'=>'image_url',
			'image_url_large'=>'image_url_large',
			'can_craft_mark'=>'can_craft_mark',
			'can_be_restored'=>'can_be_restored',
			'strange_parts'=>'strange_parts',
			'paintable_unusual'=>'paintable_unusual',
			'styles'=>'styles',
			'item_rarity'=>'item_rarity',
			'lastupdate'=>'lastupdate',
			'id'=>'id'	
		);
		
		
		$model['databasemodel']['qualities']['tablename'] = 'cosmetics_qualities';
		$model['databasemodel']['qualities']['updateonduplicate'] = true;
		$model['databasemodel']['qualities']['savetype'] = 'single';
		$model['databasemodel']['qualities']['dataset'] = array(
			'name',
			'id'
		);
		
		$this->model = $model;
		
	}
	
	public function CosmeticsKV(){
		
		$model['config']['img_path'] = MEDIA_PATH_COSMETICS;
		
		// keys = api naam kolom naam , value = database naam of filename ingeval van images
		$model['databasemodel']['items']['tablename'] = 'cosmetics';
		$model['databasemodel']['items']['updateonduplicate'] = true;
		$model['databasemodel']['items']['updateindexkey'] = 'defindex';
		$model['databasemodel']['items']['savetype'] = 'multi';
		
		$model['databasemodel']['items']['specialfields'] = array(
			'item_rarity'=>'item_rarity{getrarityindexkv}'
		);
		// json -> database : function :
		$model['databasemodel']['items']['specialfieldsarray'] = array( // pas op werkt alleen als die in eerste array nivo vd result staat.
			'used_by_heroes'=>'hero{getvalveid}'
		);
		
		$model['databasemodel']['items']['dataset'] = array(
			'item_rarity'=>'item_rarity_text',
			'item_slot'=>'item_slot',
			'prefab'=>'prefab',
			'is_weapon'=>'is_weapon',
			'used_by_heroes'=>'hero'
		);
		
		$model['databasemodel']['rarities']['tablename'] = 'cosmetic_rarities';
		$model['databasemodel']['rarities']['updateonduplicate'] = true;
		$model['databasemodel']['rarities']['savetype'] = 'multi';
		$model['databasemodel']['rarities']['dataset'] = array(
			'value'=>'valve_value',
			'color'=>'colorindex'
		);
		
		$model['databasemodel']['rarities']['specialfields'] = array( // pas op werkt alleen als die in eerste array nivo vd result staat.
			'value'=>'name{saveupdatekey}',
			'color'=>'color{getraritycolorhex}'
		);
		
		$this->model = $model;
		
	}
	
	public function Heroes(){
		
		$model['config']['img_path'] = MEDIA_PATH_HEROS;
		
		// keys = api naam kolom naam , value = database naam of filename ingeval van images
		$model['databasemodel']['heroes']['tablename'] = 'heros';
		$model['databasemodel']['heroes']['updateonduplicate'] = true;
		//$model['databasemodel']['heroes']['updateindexkey'] = 'defindex';
		$model['databasemodel']['heroes']['savetype'] = 'multi';
		
		$model['databasemodel']['heroes']['customimageurl'] = array(
			'imgverysmall'=>'http://cdn.dota2.com/apps/dota2/images/heroes/{usedname}_sb.png',
			'imgsmall'=>'http://cdn.dota2.com/apps/dota2/images/heroes/{usedname}_hphover.png',
			'imgmedium'=>'http://cdn.dota2.com/apps/dota2/images/heroes/{usedname}_full.png',
			'imgbig'=>'http://cdn.dota2.com/apps/dota2/images/heroes/{usedname}_vert.jpg',
		);
		
		// json -> database : function :
		$model['databasemodel']['heroes']['specialfields'] = array(
			'name'=>'usedname{extractheroname}',
		);
		
		$model['databasemodel']['heroes']['dataset'] = array(
			'name'=>'ingamename',
			'localized_name'=>'name',
			'id'=>'valveid'	
		);
		
		$this->model = $model;
		
	}
	
	public function Items(){
		
		$model['config']['img_path'] = MEDIA_PATH_ITEMS;
		
		// keys = api naam kolom naam , value = database naam of filename ingeval van images
		$model['databasemodel']['itemdata']['tablename'] = 'items';
		$model['databasemodel']['itemdata']['updateonduplicate'] = true;
		//$model['databasemodel']['heroes']['updateindexkey'] = 'defindex';
		$model['databasemodel']['itemdata']['savetype'] = 'multi';
		
		$model['databasemodel']['itemdata']['customimageurl'] = array(
			'img'=>'http://cdn.dota2.com/apps/dota2/images/items/{img}'
		);
		
		// json -> database : function :
		$model['databasemodel']['itemdata']['specialfields'] = array(
			'attrib'=>'attrib{striphtml}',
			'components'=>'components{arraytojson}'  //nog fixen array opslaan
		);
		
		
		$model['databasemodel']['itemdata']['dataset'] = array(
			'id'=>'valveid',
			'img'=>'img',
			'dname'=>'dname',
			'qual'=>'qual',
			'cost'=>'cost',
			'desc'=>'description',
			'notes'=>'notes',
			'mc'=>'mc',
			'cd'=>'cd',
			'lore'=>'lore',
			'attrib'=>'attrib',
			'components'=>'components',
			'created'=>'created'	
		);
		
		$this->model = $model;
		
	}
	
	
	public function NewsYoutube(){
		
		$model['config']['img_path'] = MEDIA_PATH_YOUTUBE;
		
		// keys = api naam kolom naam , value = database naam of filename ingeval van images
		$model['databasemodel']['items']['tablename'] = 'news_youtube';
		$model['databasemodel']['items']['updateonduplicate'] = true;
		//$model['databasemodel']['heroes']['updateindexkey'] = 'defindex';
		$model['databasemodel']['items']['savetype'] = 'multi';
		
		$model['databasemodel']['items']['images'] = array(
			//'default',
			//'medium'
		);
		
		// json -> database : function :
		$model['databasemodel']['items']['specialfields'] = array(
			//'default'=>'img{getfromarray}',
			//'medium'=>'imgmedium{getfromarray}',
			'channelid'=>'nid{getnid}', // news index id = nid
			'publishedAt'=>'publisheddate{convYTtimeToDate}'
		);
		
		$model['databasemodel']['items']['dataset'] = array(
			'channelId'=>'channelid',
			'title'=>'title',
			'description'=>'description',
			'url'=>'img',
			//'medium'=>'imgmedium',
			'videoId'=>'videoid',
			'id'=>'youtubeid',
			'publishedAt'=>'publisheddate',
			'channelTitle'=>'channeltitle'
		);
		
		$this->model = $model;
		
	}
	
	public function twitchtv(){
		
		$model['config']['img_path'] = MEDIA_PATH_LIVESTREAMS;
		
		// keys = api naam kolom naam , value = database naam of filename ingeval van images
		$model['databasemodel']['streams']['tablename'] = 'livestreams';
		$model['databasemodel']['streams']['updateonduplicate'] = true;
		//$model['databasemodel']['heroes']['updateindexkey'] = 'defindex';
		$model['databasemodel']['streams']['savetype'] = 'multi';
		$model['databasemodel']['streams']['truncate'] = true;
		
		/* // dit uncommenten als je lokale plaatjes wilt .. niet nodig volgens mij
		$model['databasemodel']['streams']['images'] = array(
			'small',
			'medium',
			'large'
		);
		*/
		$model['databasemodel']['streams']['specialfields'] = array(
			'display_name'=>'twitchname{strtolower}'
		);
		
		$model['databasemodel']['streams']['dataset'] = array(
			'viewers'=>'viewers',
			'display_name'=>'name',
			'status'=>'status',
			'views'=>'views',
			'logo'=>'logo',
			'url'=>'url',
			'_id'=>'twitchid',
			'small'=>'previewsmall',
			'medium'=>'previewmedium',
			'large'=>'previewlarge',
			'name'=>'twitchname'
		);
		
		$this->model = $model;
		
	}

	public function dotablog(){
		
		$model['config']['img_path'] = false;
		
		// keys = api naam kolom naam , value = database naam of filename ingeval van images
		$model['databasemodel']['newsitems']['tablename'] = 'news_dotablog';
		$model['databasemodel']['newsitems']['updateonduplicate'] = true;
		//$model['databasemodel']['heroes']['updateindexkey'] = 'defindex';
		$model['databasemodel']['newsitems']['savetype'] = 'multi';
		
		$model['databasemodel']['newsitems']['dataset'] = array(
			'gid'=>'gid',
			'title'=>'title',
			'url'=>'url',
			'is_external_url'=>'is_external_url',
			'author'=>'author',
			'contents'=>'contents',
			'feedlabel'=>'feedlabel',
			'date'=>'date',
			'feedname'=>'feedname'
		);
		
		$this->model = $model;
		
	}
	
	public function Reddit(){
		
		$model['config']['img_path'] = false;
		
		// keys = api naam kolom naam , value = database naam of filename ingeval van images
		$model['databasemodel']['children']['tablename'] = 'news_reddit';
		$model['databasemodel']['children']['updateonduplicate'] = true;
		//$model['databasemodel']['heroes']['updateindexkey'] = 'defindex';
		$model['databasemodel']['children']['savetype'] = 'multi';
		
		$model['databasemodel']['children']['dataset'] = array(
			'selftext'=>'selftext',
			'title'=>'title',
			'permalink'=>'permalink',
			'author'=>'author',
			'html'=>'html',
			'name'=>'name',
			'url'=>'url',
		);
		
		$this->model = $model;
		
	}
	
	
}

?>