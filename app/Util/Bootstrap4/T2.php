<?php

class UtilBootstrap4T2 extends UtilBootstrap4
{
  public static function getMenu($menu, $read = 0, $write = 0, $perm = 1)
  {
    $url    = AifRequest::url();
    $ht = '<ul class="nav">' .
      '<li class="nav-header">Menú</li>';
    $ht .= self::getItemMenu($menu, $url, $read, $write, $perm);
    $ht .= '<li>' .
      '<a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify">' .
      '<i class="fa fa-angle-double-left"></i>' .
      '</a>' .
      '</li>' .
      ' </ul>';
    return $ht;
  }
}
