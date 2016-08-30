<?php

/* 
 * 定期執行此檔，下載當日時刻表
 */

include_once 'db/config.php';
include_once 'class/File.class.php';
include_once 'class/Network.class.php';
include_once 'class/Db.class.php';

$date = date('Ymd'); // current day $date = date('Ymd', time());
//echo $date."\n";

$f = new File();
$d = new Db();
$n = new Network();


if($date !== ''){
	try{
        $d->createTable($date);
		$n->getDataFromTra($date);
		
		if( $f->extractData($date) ){
			$f->saveData($date); 		
		}
		else{
			echo "Extract file err.";
		}
	}
	catch( Exception $e){
		throw new Exception( 'Downloading err',0,$e);
	}
}
else{
	echo "Please key in the date\n";
}
