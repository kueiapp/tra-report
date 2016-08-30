<?php

/* 
 * 下載台鐵開放資料時刻表
 */

class Network
{
// From TRA open data website
    function getDataFromTra($date)
    {
      $url  = "http://163.29.3.98/XML/$date.zip";
      $path = "data/$date.zip";     
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec ($ch);
      //print_r($data);
      curl_close ($ch);

      $file = fopen($path, "wb");
      fputs($file, $data);
    
      echo "Downloding and writing data success\n";

      fclose($file);
    
    }
    
    
    
    
}
