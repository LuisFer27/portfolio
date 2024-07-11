
<?php
/**
 * Clase  UsrConfig
 * 
 *  Controlador que permite publicar el proyecto a producción de acuerdo al estado en
 * el que se encuentra trabajando.
 * 
 * @author Luis Fernando Mendez Barrera
 * @copyright Copyright (c) 2024,{@link}
 * @since 2024-07-10
 * @version 1.0.0
 */
class UsrConfig{
    
 public function __construct()
{
  AifResponse::none();
}
static public function init()
{ //return self::production();
  //switch (AIF_APP) {
  //  case '/mnt/stor10-wc1-dfw1/408277/carfianza.anemona.com/lib/app':
  //    return self::production();
  //  case '/mnt/stor10-wc1-dfw1/408277/www.carfianza.com/lib/app':
  //    return self::productionGood();
  //}
 return self::development();
}
static public function production()
{ 
  $name = Usr::$title;
  UtilEmail::$test= true;
  Usr::$production = 1;
  Aif::$cfg->DataBase = 'mysqli://root@127.0.0.1:3306/portfolio';  
    //Aif::$cfg->mail = "sendGrid://notmail.us/tramites@notmail.us#Tramites";
}
static public function development()
{ 
  $name = Usr::$title;
  UtilEmail::$test= true;
  Usr::$production = 0;
  Aif::$cfg->DataBase = 'mysqli://root@127.0.0.1:3306/portfolio';  
    //Aif::$cfg->mail = "sendGrid://notmail.us/tramites@notmail.us#Tramites";
}

}




?>