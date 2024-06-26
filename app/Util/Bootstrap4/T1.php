<?php

class UtilBootstrap4T1 extends UtilBootstrap4 {
  
  static public function pages($current, $pages, $url, $count = 6)
  {

    
    $ht = '<div class="pull-right"><ul class="pagination not-radius justify-content-end m-0 p-0">';
    $n = 1;
    $count = $count > $pages ? $pages : $count;
    $md = floor(($count) / 2);
    if ($current <= $md) $md = 1;
    elseif ($current + $md > $pages) $md = ($pages - $count) + 1;
    elseif ($current > $md) $md = $current - $md;
    $_url = str_replace('%page%', $current - 1, $url);
    if ($current <= 1) {
      $ht .= '<li class="page-item disabled"><a class="page-link" href="javascript:;">&#8249;</a></li>';
    } else {
      $ht .= '<li class="page-item"><a class="page-link" href="' . str_replace('%page%', 1, $url) . '">&#171;</a></li>';
      $ht .= '<li class="page-item"><a class="page-link" href="' . $_url . '">&#8249;</a></li>';
    }
    for ($x = 0; $x < $count; $x++) {
      $nn = ($x + $md);
      $_url = str_replace('%page%', $nn, $url);
      $ht .= '<li class="page-item ' . ($current == $nn ? 'active' : '') . '"><a class="page-link" href="' . $_url . '">' . ($nn) . '</a></li>';
    }
    $_url = str_replace('%page%', $current + 1, $url);
    if ($current >= $pages) {
      $ht .= '<li class=" page-item disabled"><a class="page-link" href="javascript:;">&#8250;</a></li>';
    } else {
      $ht .= '<li class="page-item"><a class="page-link" href="' . $_url . '">&#8250;</a></li>';
      $ht .= '<li class="page-item"><a class="page-link" href="' . str_replace('%page%', $pages, $url) . '">&#187;</a></li>';
    }
    //$ht.='</ul></div>'.($total?'</div>':'');
    return $ht . '</ul></div>';
  }

}
