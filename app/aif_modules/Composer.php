<?php
/**
 * 
 * instalar composer: https://getcomposer.org/
 * 
 * install paquetes de composer
 * entrar: cd Composer
 * composer.bat require PAQUETE
 * 
 * Ejemplo instalar PhpSpreadsheet:
 *  composer require phpoffice/phpspreadsheet  
 */


 class Composer {
   
  public static function autoload() {
    require_once 'Composer/vendor/autoload.php';
  } 
  
 }