<?
//$this->request;
if(isset($this->request['amount'])){
	$dv = new divinecourage();
	$dv->divine_me($this->request['amount']);
	
	echo $dv->outputHTML();
}
//$array = $dv->output();
//echo '<pre>';
//print_r($array);
?>