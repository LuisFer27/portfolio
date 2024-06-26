<?php

/**
 */
class AifLibFile {
  public static $chmod = 0775;

  /**
   * Fozar retardo en sengundos a metodos para servidores clod/files
   * @var integer
   */
  public static $seg = 0;
  /**
   *
   * @param string $name
   * @param string $prefix
   * @return string
   */
  public static function getName($name, $prefix = ''){
    return AifLibConvert::toKey($name, $prefix, '\.');
  }

  /**
   * Obtiene la extension de un archivo
   * @param string $name
   */
  public static function getExtension($name){
    $info = pathinfo($name);
    return isset($info['extension']) ? $info['extension'] : '';
  }
  /**
   * Alias de uploaded.
   */
  public static function moveUploaded($filename, $destination){
    return self::uploaded($filename, $destination);
  }
  /**
   *
   * @param unknown $oFile
   * @param unknown $dFile
   * @return boolean
   */
  public static function uploaded($oFile, $dFile){
    if (!$oFile) return FALSE;

    if (is_array($oFile)){
      if (isset($oFile['tmp_name']) && isset($oFile['error']) &&
           isset($oFile['size'])){
        if ($oFile['error'] == 0 && $oFile['size'] != 0){
          $oFile = $oFile['tmp_name'];
        }else
          return FALSE;
      }else
        return FALSE;
    }
    //ob_start();
    $dir = dirname($dFile);
    !file_exists($dir) && AifLibFile::mkdirSeg($dir);
    if (move_uploaded_file($oFile, $dFile)){
      //ob_end_clean();
      return TRUE;
    }
    Aif::$debug & 2 && AifDebug::set('uploaded', ob_get_contents());
    //ob_end_clean();
    return FALSE;
  }
  /**
   *
   * @param unknown $oFile
   * @param unknown $dFile
   * @param string $var
   * @return boolean
   */
  public static function tmpUploaded($oFile, $dFile, $var = '__tempFile__'){
    if (AifSession::$id){
      if (self::uploaded($oFile, $dFile)){
        ($last = AifSession::get($var)) && @unlink($last);
        AifSession::set($var, $dFile);
        return TRUE;
      }
    }else
      Aif::$debug & 2 && AifDebug::set('tmpUploaded', 'Requires session');
    return FALSE;
  }

  /**
   *
   * @param unknown $source
   * @param unknown $dest
   * @param string $chmod
   * @return boolean
   */
  public static function copy($source, $dest, $chmod = NULL, $mk = TRUE){
    $dir = dirname($dest);
    !file_exists($dir) && AifLibFile::mkdir($dir);
    if (copy($source, $dest)){
      chmod($dest, 0775);
      return TRUE;
    }
    return FALSE;
  }

  /**
   *
   * @param unknown $source
   * @param unknown $dest
   * @param string $chmod
   * @return boolean
   */
  public static function move($source, $dest, $chmod = NULL, $mk = TRUE){
    $dir = dirname($dest);
    !file_exists($dir) && AifLibFile::mkdir($dir);
    // $mk && self::mkdir($dir, $chmod);
    if (rename($source, $dest)){
      chmod($dest, 0775);
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Comprueba la existencia de un path y si no lo crea
   * @param string $path El path es completo y debe empezar con /
   * @param hexadecimal $chmod Permisos de directorio.
   * @return boolean Si el path existe
   */
  public static function mkdir($path, $chmod = NULL, $seg = 0){
    $seg || ($seg = self::$seg);
    $chmod = $chmod ? $chmod : self::$chmod;
    $path = is_file($path) ? dirname($path) : $path;
    if (!file_exists($path)){
      ob_start();
      $st = FALSE;
      if (!is_dir($path) && !file_exists($path) && mkdir($path, 0775)){
        sleep($seg);
        return file_exists($path) ? TRUE : self::createPath($path);
      }
     // Aif::$debug & 2 && AifDebug::set('mkdir', ob_get_contents());
      ob_end_clean();
      return FALSE;
    }
    return TRUE;
  }
  /**
   * Crea con retardo de segundos, para cloadfiles
   * @param unknown $path
   * @param number $chmod
   * @return boolean
   */
  public static function mkdirSeg($path, $seg = NULL, $chmod = NULL){
    $seg || ($seg = self::$seg);
    $chmod = $chmod ? $chmod : self::$chmod;
    return self::mkdir($path, $chmod, $seg);
  }
  /**
   * Valida si un archivo es semanticamente correcto
   * @param string $file
   * @param string $ext extensiones permitidas Ej: html|html|css
   * @return boolean
   */
  public static function isfile($file, $ext = NULL){
    //preg_match( '/^[^.^:^?^\-][^:^?]*\.(?i)(' . ($ext ? $ext : '[a-zA-Z0-9]+') . ')$/', $file)
    return is_file($file);
  }
  /**
   *
   * @param unknown $file
   * @param string $data
   * @param number $chmod
   * @return boolean
   */
  public static function write($file, $data = NULL, $chmod = NULL, $append = FALSE){
    if ($data !== NULL){
      $chmod = $chmod ? $chmod : self::$chmod;
      $path = pathinfo($file);
      $dir = isset($path['dirname']) ? $path['dirname'] : '';
      $dir != '' && (is_readable($dir) || AifLibFile::mkdir($dir));
      if (isset($path['basename']) && $path['basename'] != ''){
        $fp = fopen($file, $append ? 'a' : 'w');
        if ($fp){
          fwrite($fp, $data);
          fclose($fp);
          $chmod && @chmod($file, 0775);
          return TRUE;
        }
      }
    }
    return FALSE;
  }
  /**
   * Agrega datos a un archivo
   * @param unknown $file
   * @param unknown $data
   * @return boolean
   */
  public static function append($file, $data){
    return self::write($file, "\n" . $data, self::$chmod, TRUE);
  }

  /**
   * Copia un directorio de forma recursiva
   * @param string $source Ruta origen
   * @param string $dest Ruta Destino
   * @param string $out Imprime salida
   */
  public static function copyDir($source, $dest){
    if (($dir = opendir($source . '/'))){
      self::mkdir($dest);
      while (($file = readdir($dir))){
        if ($file == "." || $file == "..") continue;
        if (is_dir("$source/$file")) self::copyDir("$source/$file",
          "$dest/$file");
        else self::copy("$source/$file", "$dest/$file", self::$chmod, FALSE);
      }
      closedir($dir);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Elimina un directorio de forma recursiva
   * @param string $dir
   * @return boolean
   */
  public static function removeDir($dir){
    if (!file_exists($dir)) return TRUE;
    if (!is_dir($dir) || is_link($dir)) return unlink($dir);
    $ar = scandir($dir);
    foreach ($ar as $item){
      if ($item == '.' || $item == '..') continue;
      if (!self::removeDir($dir . "/" . $item)) return FALSE;
    }
    return rmdir($dir);
  }

  /**
   *
   * @var array
   */
  private static $idxSearch = array ();
  /**
   * Busqueda de archivos por expresion o por simple comparacion
   * @param string $dir
   * @param string $text
   * @param string $ty
   */
  private static function _search($dir, $text, $ty = TRUE, $all = FALSE){
    if (($dd = opendir($dir . '/'))){
      while (($file = readdir($dd))){
        if (!$all && self::$idxSearch[$text]) return self::$idxSearch[$text];
        if ($file == "." || $file == "..") continue;
        if (is_dir("$dir/$file")) return self::_search("$dir/$file", $text, $ty,
          $all);
        elseif ($ty && preg_match($text, $file)){
          if ($all) self::$idxSearch[$text][] = "$dir/$file";
          else{
            self::$idxSearch[$text] = "$dir/$file";
            return self::$idxSearch[$text];
          }
        }elseif ($file == $text){
          if ($all) self::$idxSearch[$text][] = "$dir/$file";
          else{
            self::$idxSearch[$text] = "$dir/$file";
            return self::$idxSearch[$text];
          }
        }
      }
      closedir($dd);
      if ($all) return self::$idxSearch[$text];
    }
    return FALSE;
  }
  /**
   * Busqueda de archivos con expresiones Por ejemplo: "/^example(.*?)\.zip$/i"
   * @param string $dir Directorio base
   * @param string $text Expresion
   */
  public static function searchEx($dir, $text, $all = FALSE){
    self::$idxSearch[$text] = $all ? array () : FALSE;
    self::_search($dir, $text, TRUE, $all);
    return self::$idxSearch[$text];
  }
  /**
   * Busqueda de archivos por nombre Por ejemplo: "example.zip"
   * @param string $dir Directorio base
   * @param string $text Nombre del archivo
   */
  public static function search($dir, $text, $all = FALSE){
    self::$idxSearch[$text] = $all ? array () : FALSE;
    self::_search($dir, $text, FALSE, $all);
    return self::$idxSearch[$text];
  }

  /**
   * Devuelve un arreglo con los archivos
   * @param string $dir
   * @param string $one Forza a devolver un arreglo de una solo dimension
   * @return array
   */
  public static function toArray($dir, $one = FALSE){
    $ar = array ();
    if (($dd = opendir("$dir/"))){
      ($one === TRUE) && ($one = "$dir/");
      while (($file = readdir($dd))){
        if (!$file || $file == '.' || $file == '..') continue;
        elseif ($one){
          if (is_dir("$dir/$file")) $ar = array_merge($ar,
            self::toArray("$dir/$file", $one));
          else{
            $file = str_replace($one, '', $dir . '/') . $file;
            $ar[$file] = $file;
          }
        }else{
          $ar[$file] = !is_dir("$dir/$file") ? $file : self::toArray(
            "$dir/$file");
        }
      }
      closedir($dd);
    }
    return $ar;
  }
  /**
   * Comprime un archivo o carpeta existente
   * @param unknown $source
   * @param unknown $destination
   * @return boolean
   */
  public static function zip($source, $destination){
    if (extension_loaded('zip')){
      if (file_exists($source)){
        $zip = new ZipArchive();
        if ($zip->open($destination, ZIPARCHIVE::CREATE)){
          $source = realpath($source);
          if (is_dir($source)){
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source),
                RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file){
              $file = realpath($file);
              if (is_dir($file)){
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
              }else if (is_file($file)){
                $zip->addFromString(str_replace($source . '/', '', $file),
                  file_get_contents($file));
              }
            }
          }else if (is_file($source)){
            $zip->addFromString(basename($source), file_get_contents($source));
          }
        }
        return $zip->close();
      }
    }
    return FALSE;
  }
  /**
   * Descomprime un archivo zip en una carpeta, requiere clase ZipArchive
   * @param string $name
   * @param string $dir
   * @return boolean
   */
  public static function unZip($name, $dir){
    if (class_exists('ZipArchive')){
      $zip = new ZipArchive();
      if (file_exists($name)){
        if ($zip->open($name) === TRUE){
          self::mkdir($dir);
          $zip->extractTo($dir);
          $zip->close();
          return TRUE;
        }
      }
    }else{
      Aif::ecsc('aif/debugger','set',array('aif/lib/file', 'The ZipArchive class is required'));
    }
    return FALSE;
  }
  /**
   * Comprime un archivo en formato gzip
   * @param unknown $src
   * @param unknown $dst
   */
  public static function gzip($src, $dst, $suf = '.gz'){
    if (function_exists('gzopen') && file_exists($src)){
      AifLibFile::mkdir(dirname($dst));
      $fz = gzopen("$dst$suf", 'wb9');
      if ($fz){
        gzwrite($fz, file_get_contents($src));
        gzclose($fz);
        return TRUE;
      }
    }
    return FALSE;
  }
  /**
   * Descomprime un archivo comprimido por gzip
   * @param unknown $src
   * @param unknown $dst
   * @return boolean
   */
  private static function unGzip($src, $dst){
    $rs = FALSE;
    if (function_exists('gzopen') && file_exists($src)){
      AifLibFile::mkdir(dirname($dst));
      $fz = gzopen($src, 'rb');
      if ($fz){
        $fp = fopen($dst, 'wb');
        if ($fp){
          while (!gzeof($fz)){
            fwrite($fp, gzread($fz, 4096)); // read 4kb at a time
          }
          $rs = TRUE;
          fclose($fp);
        }
        gzclose($fz);
      }
    }
    return $rs;
  }
  /**
   *
   * @param string|array $file
   * @param string $post
   * @return boolean array
   */
  public static function getInfo($file, $post = FALSE){
    if ($post || file_exists($file)){
      if ($post){
        $type = $file['type'];
        $size = $file['size'];
        $file = $name = $file['name'];
      }else{
        list ( $type, $name ) = AifResponse::getNameType($file);
        $size = filesize($file);
      }
      $pp = pathinfo($file);
      $reg['size'] = $size;
      $reg['name'] = $name;
      $reg['file'] = $file;
      $reg['ext'] = $pp['extension'];
      $reg['type'] = $type ? $type : 'undefined';
      return $reg;
    }
    return FALSE;
  }

  /**
   * Convierte un archivo de xlsx a csv Require clase extenra PHPExternal
   * @param string $file
   * @param string $name Nombre de archivo de salida
   * @param integer Numero de hoja de excel, pordefaul es 0
   * @return boolean
   */
  public static function xlsxToCsv($file, $name = NULL, $sheet = 0){
    if (AifExternal::call('/PHPExcel/Classes/PHPExcel.php')){
      if (file_exists($file)){
        $objPHPExcel = PHPExcel_IOFactory::load($file);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV')->setDelimiter(
          ',')->setEnclosure('"')->setLineEnding("\r\n")->setSheetIndex($sheet);
        $name = $name ? $name : str_ireplace('.xlsx', '.csv', $file);
        $objWriter->save($name);
        return TRUE;
      }
      Aif::$debug & 2 && AifDebug::set('File', 'File not exist ' . $file);
      return FALSE;
    }
    Aif::$debug & 2 && AifDebug::set('Class External', 'PHPExcel required');
    return FALSE;
  }

  /**
   *
   * @param unknown $file
   * @return boolean
   */
  public static function exists($file){
    return file_exists($file);
  }
  /**
   */
  /**
   * Devielve la primer linea de un archivo
   * @param unknown $file
   * @param string $len
   */
  public static function firstLine($file, $len = 4096){
    if (file_exists($file)){
      $fp = fopen($file, "r");
      if ($fp){
        $ln = fgets($fp, $len);
        fclose($fp);
        return $ln;
      }
    }
    return FALSE;
  }

  /**
   * [generatePath description]
   * @param  [type] $path    [description]
   * @param  [type] $exclude [description]
   * @return [type]          [description]
   */
  public static function createPath ($path, $exclude=NULL){
  	$chmod = "0775";
  	$exclude = ($exclude ? $exclude.'/' : ($exclude===NULL ? '' : AifView::getPath(true).'/') );
  	$path = preg_match("/((.*?)\.(.*))/i", $path) ? dirname($path) : $path;
  	$dirname = preg_replace('/((.*)(\/|\\\))$/i',"$2",$path);
  	$pathSplit = array();
  	if($dirname){
  		$exclude = preg_replace('/((.*)(\/|\\\))$/i',"$2",$exclude);
  		$pathSplit = explode("/", str_replace("\\", "/", str_replace($exclude, "", $dirname)));
  		array_reverse($pathSplit);
  		if(!file_exists($dirname)){
  			$dirPath = $exclude;

  			foreach ($pathSplit as $v){
  				if($v){
	  				$dirPath.= preg_match("/([A-Z]:)/i", $v) ? $v : "/".$v;
						//print_r(array($dirPath, $pathSplit));die();
	  				$dirPath = str_replace("//", "/",$dirPath);
	  				if(!file_exists($dirPath)){
	  					self::mkdir($dirPath,"0775");
	  				}
	  				file_exists($dirPath) && @self::chmodFile($dirPath);
  				}
  			}
  		}
  		return file_exists($dirname);
  	}
  	return true;
  }

  /**
   * [public description]
   * @var [type]
   */
  static public function chmodFile($path=NULL, $isCreate=FALSE) {
		if($path){
      $path = str_replace(array("//"), array("/"), $path);
			$isCreate && !file_exists($path) && self::createPath($path);
			if(file_exists($path)){
				$path = preg_replace("/[\\\\|]/i", "", $path);
				$cmd = 'chmod 775 "'.$path.'"';
				system($cmd);
				return TRUE;
			}
		}
		return FALSE;
	}
}
