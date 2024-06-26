<?php

class UtilResponse
{

  static public function json($rs)
  {
    header('Content-type: application/json');
    $data = json_encode($rs);
    die($data);
  }
}
