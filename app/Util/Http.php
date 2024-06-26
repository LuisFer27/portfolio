<?php

class UtilHttp {

  public static $error = '';

   public static function download($url, $file) { 
    //return true;//comentar en produccion
    if(!file_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       // curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        $data = curl_exec ($ch);
        self::$error = curl_error($ch); 
        curl_close ($ch);
        if(!self::$error) {
          $dir = dirname($file);
          file_exists($dir) || mkdir($dir,0777, true);
          file_put_contents($file, $data);
          return TRUE;
        }
        return FALSE;
      }
      die('curl is required');
   }
}