<?php
include_once "../db/config.php";
include_once "../class/Db.class.php";

$db = new Db();
$locationArr = json_decode($db->getStations() );
//print_r($locationArr);

foreach($locationArr as $location){
        
    $url = "http://maps.google.com/maps/api/geocode/json?address=".  urlencode($location->cname."火車站")."&sensor=false&region=TW";
    $response = file_get_contents($url);
    $response = json_decode($response, true);

    //print_r($response);

    $lat = $response['results'][0]['geometry']['location']['lat'];
    $long = $response['results'][0]['geometry']['location']['lng'];

    echo "location: ".$location->cname."火車站"."==> latitude: " . $lat . " longitude: " . $long."\n<br/>";
    $db->updateLocation($location->stationid, $lat, $long);

}

