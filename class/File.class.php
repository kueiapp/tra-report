<?php

/* 
 * 檔案處理類別
 */

include_once "Db.class.php";

class File
{
    private $db;
    
    function __construct() 
    {
        $this->db = new Db();
    }
    
// extract ZIP file to get XML
    function extractData($inDataName)
    {
        $zip = new ZipArchive;
        if ($zip->open("data/$inDataName.zip") === TRUE) {
            $zip->extractTo('./data');
            $zip->close();
            return 1;
        } 
        else {
            return 0;
        }
    }

// 處理與儲存台鐵時刻表資料   
    function saveData($date)
    {
        /* check connection */
        if ($this->mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $f = file_get_contents("data/$date.xml"); //以執行檔位置為主，非class檔位置
        if($f){
            $xml = new SimpleXMLElement($f);

                $trainID = 0;
                $carClass = 0;
                $arrTime = '00:00:00';
                $depTime = '00:00:00';
                $stationOrder = 0;
                $stationID = 0;
                $lineDir = 0;
                $sql = '';

            echo "Writing data...\n";            
            $array = array();
                
            for($i=0; $i<sizeof($xml->TrainInfo); $i++){
                $trainInfo = $xml->TrainInfo[$i];

            // train info
                foreach($trainInfo->attributes() as $key=>$value){
                    //echo "$key=>$value<br/>";
                    if($key=='Train'){ $trainID = $value;}
                    else if($key=='CarClass'){ $carClass = $value;}
                    else if($key=='LineDir'){ $lineDir = $value;}
                }

            // time info
                foreach($trainInfo->TimeInfo as $timeinfo){
                    foreach($timeinfo->attributes() as $key=>$value){
                        //echo "$key=>$value<br/>";    
                        if($key=='ARRTime'){ $arrTime = $value;}
                        else if($key=='DEPTime'){ $depTime = $value;}
                        else if($key=='Order'){ $stationOrder = $value;}
                        else if($key=='Station'){ $stationID = $value;}
                        $d = DateTime::createFromFormat('H:i:s',$arrTime);
                        $arrTime2 = $d->format('H:i:s');
                        $d = DateTime::createFromFormat('H:i:s',$depTime);
                        $depTime2 = $d->format("H:i:s");

                    }
                    array_push($array,array(
                        'date'=>$date,
                        'trainID'=>$trainID,
                        'stationID'=>$stationID,
                        'stationOrder'=>$stationOrder,
                        'arrTime'=>$arrTime,
                        'depTime'=>$depTime
                    ));

                }
                
            }//end for
            //
            $this->db->saveData($array);

        }
        else{
            echo "File Open Error.<br/>";
        }

    }







}