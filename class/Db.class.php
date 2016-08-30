<?php

/* 
 * 資料庫處理類別
 */


class Db
{
    private $mysqli;
    
    function __construct()
    {      
        $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME); 
        $this->mysqli->query("SET CHARACTER SET 'utf8'"); 
        $this->mysqli->query("SET NAMES 'UTF8'");
    }
    
    function __destruct()
    {
      $this->mysqli->close();
    }
    
//查詢車次代碼    
    function checkTrainNO($trainid,$date)
    {
        /* check connection */
        if ($this->mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        $sql = "SELECT DISTINCT trainID FROM timetable_$date WHERE trainid=$trainid";
        //echo "$sql\n";
        $result = $this->mysqli->query($sql);
        return $result->num_rows;
        
    }
    
// 抓取資料庫中車站代號與名稱
    function getStations()
    {
        $stationArray = array();
        
        /* Cannot show Chinese charactor correctly...... 
         $this->mysqli->query("SET NAMES 'UTF8'");  add the query can solve the problem  */
          
        if ($this->mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        $result = $this->mysqli->query("SELECT stationID,cname,ename,latitude,longitude FROM station") or die(mysql_error() );
        while( $row = $result->fetch_object() ){
            array_push($stationArray,array('stationid'=>intval($row->stationID),
                'cname'=>$row->cname,
                'ename'=>$row->ename,
                'latitude'=>  doubleval($row->latitude),
                'longitude'=>  doubleval($row->longitude)
            ));
        }

        /*$stationArray = array();
        $result = mysql_query("SELECT * FROM station") or die( mysql_error() );
        while($row = mysql_fetch_object($result) ){
            array_push($stationArray,array('stationid'=>intval($row->stationID),'cname'=>$row->cname ) );
        }*/

        return json_encode($stationArray);
    }
    
// 更新車站GPS位置    
    function updateLocation($stationid,$latitude,$longitude)
    {
        $this->mysqli->query("UPDATE station SET latitude=".doubleval($latitude).", longitude=".doubleval($longitude)."WHERE stationid=$stationid");
    }
    
// 抓取資料庫中、誤點資料的投票數
    function getVote($trainid,$delay,$stationid,$date)
    {
        /* check connection */
        if ($this->mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        $sql = "SELECT vote FROM delay_$date WHERE trainid=".$trainid.' AND stationid='.$stationid.' AND delayMinute='.$delay;
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_object();
        return intval($row->vote);
    }
    
// 新增誤點資料
    function insertDelay($trainid,$delay,$stationid,$date)
    {
        /* check connection */
        if ($this->mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        $sql = "SELECT vote FROM delay_$date WHERE trainid=".$trainid.' AND stationid='.$stationid.' AND delayMinute='.$delay;
        //echo "$sql\n";
        $result = $this->mysqli->query($sql) or die( $this->mysqli->error );
        $rowno = $result->num_rows;
        //echo "$rowno rows\n";
        if($rowno==0){
            $sql = "INSERT INTO delay_$date VALUES(NULL,$trainid,$stationid,$delay,1)";
            //echo "$sql\n";
            $this->mysqli->query($sql) or die($this->mysqli->error );
        }
        else if($rowno>0){
            $sql = "UPDATE delay_$date SET vote = vote+1 WHERE trainid=$trainid AND stationid=$stationid AND delayMinute=$delay";
            //echo "$sql\n";
            $this->mysqli->query($sql) or die( $this->mysqli->error );
        }
        //echo 1;//success

    }
    
// 建立所需表格    
    function createTable($date)
    {
        /* check connection */
        if ($this->mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $sql = "CREATE TABLE IF NOT EXISTS timetable_$date SELECT * FROM timetable_template;"
             . "ALTER TABLE `timetable_$date` ADD PRIMARY KEY ( `idx` ); "
             . "ALTER TABLE `timetable_$date` CHANGE `idx` `idx` INT( 11 ) NOT NULL AUTO_INCREMENT; "
             . "CREATE TABLE IF NOT EXISTS delay_$date SELECT * FROM delay_template;"
             . "ALTER TABLE `delay_$date` ADD PRIMARY KEY ( `idx` ); "
             . "ALTER TABLE `delay_$date` CHANGE `idx` `idx` INT( 11 ) NOT NULL AUTO_INCREMENT; ";  
        //echo $sql."<br/>";
        $this->mysqli->multi_query($sql) or die('createTable err '.$this->mysqli->error );
    }
    
// Insert into database
    function saveData($array)
    {
        $sql = "";
        foreach($array as $item){
            $sql .= "INSERT INTO timetable_".$item['date']." (trainID,stationID,station_order,arrTime,depTime,updated_time) "
                    . "VALUES (".$item['trainID'].",".$item['stationID'].",".$item['stationOrder'].",'".$item['arrTime']."','".$item['depTime']."',CURDATE() );";
        }
        $this->mysqli->multi_query($sql) or die('insert err '.$this->mysqli->error);


    }
    
    
    
}
