<?php
 class Usr
 {
 static public $production=0;

 static public $ver='0.0.1';

 static public $name ='portfolio';

 static public $title= 'portfolio 1.0';

 static public $key = '';

 static public $session='';

 static public $data='data';

 public function __construct()
 {
    Aif::methodInApplication();
    UsrConfig::init();
    UsrTemplate::init(self::$title);
 }



 public function index(){
 AifResponse::redirect('/Dash/index.html');
 }



 }