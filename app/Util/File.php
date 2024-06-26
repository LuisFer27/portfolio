<?php
class UtilFile  {

  public static $error = '';   

  /**
   * descomprime un archivo zip dado por el primer argumento en una 
   * carpeta en su mismo nivel o bien si se define el $path como segundo
   * argumento esta ruta. 
   *
   * @param [type] $file
   * @param [type] $path
   * @return bool
   */
  public static function unzip($file, $path = NULL) {
    $path = $path ? $path : pathinfo(realpath($file), PATHINFO_DIRNAME);
    $zip = new ZipArchive;
    $res = $zip->open($file);
    if ($res === TRUE) {
      $zip->extractTo($path);
      $zip->close();
      return TRUE;
    } 
    self::$error =  "Doh! I couldn't open $file";
    return FALSE;
  }


}