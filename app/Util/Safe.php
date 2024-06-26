<?php

class UtilSafe
{
  public static function urlsafeDecode($input)
  {
    $remainder = strlen($input) % 4;
    if ($remainder) {
      $padlen = 4 - $remainder;
      $input .= str_repeat('=', $padlen);
    }
    return strtr($input, '-_', '+/');
  }

  public static function urlsafeEncode($input)
  {
    return str_replace('=', '', strtr($input, '+/', '-_'));
  }

  static public function urlB64Encode($rs)
  {
    return $rs ? self::urlsafeEncode(base64_encode($rs)) : '';
  }

  static public function urlB64Decode($rs)
  {
    return $rs ? base64_decode(self::urlsafeDecode($rs)) : '';
  }

  public static function guid()
  {
    if (function_exists('com_create_guid') === true) {
      return trim(com_create_guid(), '{}');
    }
    return sprintf(
      '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
      mt_rand(0, 65535),
      mt_rand(0, 65535),
      mt_rand(0, 65535),
      mt_rand(16384, 20479),
      mt_rand(32768, 49151),
      mt_rand(0, 65535),
      mt_rand(0, 65535),
      mt_rand(0, 65535)
    );
  }

  public static function fxAesEncode($data, $key = NULL, $token = NULL)
  {
    if ($data && $key) {
      $iv = substr($token, 0, 16);
      $key = md5($key . $token);
      $data = base64_encode(openssl_encrypt($data, 'aes-256-cbc', $key, true, $iv));
      if (substr($data, 0, 1) === '+') $data = '~' . $data;
      return $data;
    }
    return $data;
  }

  public static function fxAesDecode($data, $key = NULL, $token = NULL)
  {
    if (substr($data, 0, 1) === '~') $data = substr($data, 1);
    if ($data && $key) {
      $iv = substr($token, 0, 16);
      $key = md5($key . $token);
      return openssl_decrypt(base64_decode($data), 'aes-256-cbc', $key, true, $iv);
    }
    return $data;
  }

  public static function aesEncode($data, $key)
  {
    if ($data) {
      $token = md5($key . uniqid(microtime()));
      return $token . self::fxAesEncode($data, $key, $token);
    }
    return $data;
  }

  public static function aesDecode($data, $key = NULL)
  {
    if ($data) {
      $token = substr($data, 0, 32);
      $data = substr($data, 32);
      return self::fxAesDecode($data, $key, $token);
    }
    return $data;
  }

  static public function send($action = NULL, $rs = NULL, $key = NULL, $name = 'data')
  {
    if ($action) {
      if ($rs) $rs = array($name => self::aesEncode(
        json_encode($rs),
        md5($key . $action)
      ));
      else $rs = $action;
    }
    header('Content-type: application/json');
    $rs = $rs ? $rs : array('success' => false);
    die(json_encode($rs));
  }

  static public function input($action = NULL, $key = NULL, $name='data')
  {
    $pp = AifRequest::post();
    if ($pp) {
      if ($action && isset($pp[$name])) {
        $pp = self::aesDecode(
          $pp[$name],
          md5($key . $action)
        );
        $pp = json_decode($pp);
      }
    }
    return $pp;
  }

  static public function inputPhp($action = NULL, $key = NULL)
  {
    $pp = file_get_contents('php://input');
    if ($pp) {
      $pp = json_decode($pp);
      if ($action) {
        $pp = self::aesDecode($pp->data, md5($key . $action));
        $pp = json_decode($pp);
      }
    }
    return $pp;
  }
}
