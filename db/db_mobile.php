<?php

/* 
 處理手機端傳來的資料
 */
include_once "config.php"; 
include_once "../class/Db.class.php";

$db = new Db();
$date = date('Ymd');

switch( $_POST['act'] ){
    
    case 'getStations':
    {
        echo $db->getStations();
        break;
    }
    case 'sendDelay':
    {
        if(isset($_POST['trainid'])&&$_POST['trainid']!==''&&
            isset($_POST['delay'])&&$_POST['delay']!==''&&
            isset($_POST['stationid'])&&$_POST['stationid']!==''){

            $trainid = intval($_POST['trainid']);
            $delay = intval($_POST['delay']);
            $stationid = intval($_POST['stationid']);

            if( $db->checkTrainNO($trainid,$date) ){
                $db->insertDelay($trainid,$delay,$stationid,$date);
                echo $db->getVote($trainid,$delay, $stationid, $date);
            }
            else{
                echo "no car";
            }
        }
        else{
            echo 0;
        }
        break;
    }
    default:{
        break;
    }
    
}
