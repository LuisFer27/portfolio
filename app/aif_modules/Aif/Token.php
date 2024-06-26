<?php

class AifToken {
  
    public $alphabet = 'ieaolq8gufrntk3h0z9jvwpb4s5mc2yx6d17';
    public $size = 10;
    public $sz =0;
    public $keysplit ='';

    public function __construct($alphabet, $size){
      $this->alphabet = substr($alphabet, $size);
      $this->size = $size;
      $this->keysplit = substr($alphabet,0,$size);
    }

    public static function gen($n, $size=6, $prefix='', $alphabet='ieaolq8gufrntk3h0z9jvwpb4s5mc2yx6d17') {
      $tk = new AifToken($alphabet, $size);
      return $tk->set($n,$prefix);
    }

    public static function checksum($n, $y=false) {
      $s = 0;
      $n = intval($n); 
      $l = $y ? 2 : 1; 
      $r = $y ? 1 : 2;
      do {
          $d = $n % 10; 
          $d = $d % 2 ? $d * $l : $d * $r;
          $d = $d > 9 ? $d - 9 : $d; 
          $s += $d;
      } while (($n = floor($n / 10)));
      $rs = ($s * 9) % 10;
      return $rs?$rs:1;
    }

    public function encode($num) {
      $base = strlen($this->alphabet);
      $digits = array();
      $chars = array();
      do {
          $digits[] = $num % $base;
          $num = floor($num / $base);
      } while ($num > 0);
      while (count($digits)){
        $idx = array_pop($digits);
        $chars[]=$this->alphabet[$idx];
      }
      return implode('',$chars);
    }

    public function decode($str) {
      $base = strlen($this->alphabet);
      $pos = 0; $num = 0;
      while ( strlen($str)) {
          $c = $str[strlen($str) - 1];
          $str = substr($str,0, strlen($str) - 1);
          $num += pow($base, $pos) * strpos($this->alphabet,$c);
          $pos++;
      }
      return $num;
    }

    public function h2a($d) {
      $r = str_split($d);
      $x = array();
      while (($c = array_pop($r))!==null){
         $x[]=$this->alphabet[hexdec($c)];
      }
      return implode('',$x);
    }

    public function valid($n, $ty) {
        $n = strtolower($n);
        $a = preg_replace('/^(.*?)('.implode('|',str_split($this->keysplit)).')(.*?)$/','$1,$2,$3',$n);
        $a = explode(',',$a);
        if (count($a) > 1) {
            $n = $this->decode($a[2]) . '';
            $d = substr($n, -1);
            $i = substr($n,0, 1);
            $x = $this->keysplit[($i % 2 ? $i : $d) % $this->size];
            $v = $this->h2a(substr(md5($n . $n . $n),intval($i) + intval($d), strlen($a[0])));
            $n = substr($n,1, strlen($n) - 2);
            if (self::checksum($n) == $d && self::checksum($n, 1) == $i && $v == $a[0] && $x == $a[1])
                return $ty ? $n : true;
        }
        return $ty ? null : false;
    }

    public function get($n,$prefix='') {
        strlen($prefix) && ($n = substr($n,strlen($prefix)));
        return $this->valid($n, true);
    }

    public function set($n,$prefix='') {
      $this->sz = ($this->size-strlen($prefix));
      $d = self::checksum($n);
      $i = self::checksum($n, 1);
      $c = $i . '' . $n . '' . $d;
      $v = $this->encode(intval($c));
      $x = $this->sz - strlen($v);
      $t = $this->keysplit[($i % 2 ? $i : $d) % $this->sz];
      $a = $this->h2a(substr(md5($c . $c . $c),$i + $d, $x - 1));
        return strtoupper($prefix. $a . $t . $v);
    }

}
