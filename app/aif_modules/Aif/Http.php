<?php
/**
 * Manejo de solicitudes del protocolo HTTP Manejo de solicitudes del protocolo HTTP
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Lib
 * @version $Id$
 */
//
//  
//
/**
 * Clase de manejo de solicitudes del protocolo HTTP Clase de manejo de solicitudes del protocolo HTTP
 * @package Aif
 * @subpackage Lib
 */
class AifHttp {
  /**
   * Obtien el contenido de una pagina web
   * @param unknown_type $url
   */
  public static function getContent($url,$noVerify=FALSE,$header=array()){
    $hd = (count($header)?implode("\r\n", $header)."\r\n":'');
    $hd .= 'Content-Type: text/plain;charset=UTF-8' . "\r\n";
    $hd .= 'Referer:' . dirname($url) . "\r\n";
    $hd .= 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1;';
    $hd .= ' es-ES; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6' . "\r\n";
    $ctx['http']['method']='GET';
    $ctx['http']['header']= $hd;
    if($noVerify){//No valida un certificado, o permite certificados propios
      $ctx['ssl']['verify_peer']=false;
      $ctx['ssl']['verify_peer_name']=false;
      $ctx['ssl']['allow_self_signed']=true;
    }
    $rs = file_get_contents($url, FALSE,stream_context_create($ctx));
    
    return $rs;
  }
  /**
   * Envia solicitudes a una página web
   */
  public static function send(){
  }
  /**
   * Realiza una llamada a un URL externo y le lo que retorna.
   * @param string $url URL a consultar
   * @param array $postdata Arreglo de datos a enviar
   * @param array $files Archivos a enviar.
   * @return mixed
   */
  public static function sendPost($url, $postdata = array(), $files = array(),$header=array()){
    $data = "";
    $boundary = "---------------------" . substr(md5(rand(0, 32000)), 0, 10);
    foreach ($postdata as $key => $val){
      if (is_array($val)){
        foreach ($val as $subkey => $subval){
          $data .= "--" . $boundary . "\n";
          $data .= "Content-Disposition: form-data; name=\"" . $key . '[' .
                   $subkey . ']' . "\"\n\n" . $subval . "\n";
        }
      }else{
        $data .= "--" . $boundary . "\n";
        $data .= "Content-Disposition: form-data; name=\"" . $key . "\"\n\n" .
                 $val . "\n";
      }
    }
    if (count($files) > 0){
      $data .= "--" . $boundary . "\n";
      foreach ($files as $key => $file){
        $fileContents = file_get_contents($file['tmp_name']);
        $data .= "Content-Disposition: form-data; name=\"" . $key .
                 "\"; filename=\"" . $file['name'] . "\"\n";
        $data .= "Content-Type: image/jpeg\n";
        $data .= "Content-Transfer-Encoding: binary\n\n";
        $data .= $fileContents . "\n";
        $data .= "--" . $boundary . "\n";
      }
    }
    $header[] = 'From: ' . Aif::$site;
    $header[] = 'Content-Type: ' . 'multipart/form-data; boundary=' . $boundary;
    $params = array (
        'http' => array (
            'method' => 'POST',
            'header' => implode("\r\n", $header),
            'content' => $data
        )
    );

    $ctx = stream_context_create($params);
    $fp = fopen($url, 'rb', FALSE, $ctx);
    $response = stream_get_contents($fp);
    return $response;
  }


  public static function parseHeaders( $headers )
  {
      $head = array();
      foreach( $headers as $k=>$v )
      { $t = explode( ':', $v, 2 );
          if( isset( $t[1] ) )
              $head[ trim($t[0]) ] = trim( $t[1] );
          else
          {
              $head[] = $v;
              if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                  $head['reponse_code'] = intval($out[1]);
          }
      }
      return $head;
}

  public static function post($url, $postdata = array(), $files = array(),$header=array()){
    $hd = (count($header)?implode("\r\n", $header)."\r\n":'');
    $hd.= 'Content-type: application/x-www-form-urlencoded';
    $postdata = http_build_query($postdata );
    $opts = array('http' => array(
            'method'  => 'POST',
            'header'  => $hd,
            'content' => $postdata
        )
      );
      $context  = stream_context_create($opts);
      
      $rs = file_get_contents($url, FALSE, $context);
      $GLOBALS['aifhttp_last_header']= self::parseHeaders($http_response_header);
      return $rs;
    //return $rs;
  }

  public static function getLastHeader(){
    return isset($GLOBALS['aifhttp_last_header'])?$GLOBALS['aifhttp_last_header']:null;
  }

  /**
   * http://www.forosdelweb.com/wiki/PHP:_file_get_contents(),_cURL,_HTTP_Request Subir un archivo pormedio del file_get_contents
   * @param unknown $url
   * @param unknown $file
   */
  public static function upload($url, $file, $name = 'uploaded_file', $type = 'application/zip',$header=array()){
    $mb = '--------------------------' . microtime(true);
    $hd = (count($header)?implode("\r\n", $header)."\r\n":'');
    $hd.= "Content-Type: multipart/form-data; boundary=$mb";
    $file_contents = file_get_contents($file);
    $content = "--$mb\r\n";
    $content .= "Content-Disposition: form-data; ";
    $content .= "name=\"$name\"; ";
    $content .= "filename=\"" . basename($file) . "\"\r\n";
    $content .= "Content-Type: $type\r\n\r\n";
    $content .= "$file_contents\r\n";
    $content .= "--$mb\r\n";
    $content .= "Content-Disposition: form-data; name=\"foo\"\r\n\r\n";
    $content .= "bar\r\n";
    $content .= "--$mb--\r\n";
    $context = stream_context_create(
      array (
          'http' => array (
              'method' => 'POST',
              'header' => $hd,
              'content' => $content
          )
      ));
    return file_get_contents($url, FALSE, $context);
  }
  /**
   * Descarga un archivo de remoto
   * @param unknown $url
   * @param unknown $file
   * @return boolean
   */
  public static function download($url, $file){
    $content = self::getContent($url);
    return ($content !== FALSE) ? file_put_contents($file, $content) : FALSE;
  }
}
