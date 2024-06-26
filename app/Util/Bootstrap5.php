<?php

class UtilBootstrap5
{
  public static function getBreadcrumb($breadcrumb)
  {
    $bc = '<ol class="breadcrumb pull-right">';
    $link = AifRequest::url();
    $max = count($breadcrumb);
    foreach ($breadcrumb as $idx => $item) {
      $url = $link . $item[1];
      $active = ($idx + 1 >= $max) ? ' active' : '';
      $ancla = $active ? $item[0] : '<a href="' . $url . '">' . $item[0] . '</a>';
      $bc .= '<li class="breadcrumb-item' . $active . '">' . $ancla . '</li>';
    }
    return $bc .= '</ol>';
  }

  /**
   * metodos de renderizado de menus
   */
  public static function itmMenu($title, $class, $method, $param = NULL)
  {
    return array(
      'title' => $title,
      'link' => "/$class/$method" . ($param ? "/$param/index" : '') . ".html",
      'active' => Aif::$class == $class && Aif::$method == $method
    );
  }


  /**
   * Metodos de renderizado de titulos y subtitulos de pagina
   */
  public static function title()
  {
    $class = Aif::$className;
    $title = isset($class::$title) ? $class::$title : '&nbsp;';
    $subTitle = isset($class::$subTitle) ? '<br>' . $class::$subTitle : '';
    return '<h1 class="page-header' . ($title == '' ? ' d-none d-md-block' : '') . '">' . $title . '&nbsp;<small>' . $subTitle . '</small></h1>';
  }


  public static function getSubMenu($menu, $url)
  {
    return '<ul class="sub-menu">' . self::getItemMenu($menu, $url) . '</ul>';
  }


  public static function getItemMenu($menu, $url, $read = 0, $write = 0, $perm = 1)
  {
    $ht = '';
    foreach ($menu as $idx => $item) {
      $id = isset($item['id']) ? $item['id'] : 0;
      if ($perm || ($id & $read || $id & $write)) { //permisos
        $link = isset($item['link']) ? $url . $item['link'] : 'javascript:;';
        $ico = isset($item['ico']) ? $item['ico'] : '';
        $name = isset($item['title']) ? $item['title'] : '';
        $active = isset($item['active']) ? $item['active'] : FALSE;
        if (isset($item['child'])) {
          $ht .= '<li class="has-sub' . ($active ? ' active' : '') . '">' .
            '<a href="javascript:;">' .
            '<b class="caret"></b>' .
            ($ico ? '<i class="' . $ico . '"></i> <span>' . $name . '</span>' : $name) .
            '</a>' .
            self::getSubMenu($item['child'], $url) .
            '</li>';
        } else {
          $link = $url . $item['link'];
          $ht .= '<li class="' . ($active ? ' active' : '') . '"><a href="' . $link . '">' .
            ($ico ? '<i class="' . $ico . '"></i> <span>' . $name . '</span>' : $name) .
            '</a></li>';
        }
      }
    }
    return $ht;
  }


  public static function getMenu($menu, $read = 0, $write = 0, $perm = 1)
  {
    $url    = AifRequest::url();
    $ht = '<ul class="nav">' .
     '';// '<li class="nav-header">Menú</li>';
    $ht .= self::getItemMenu($menu, $url, $read, $write, $perm);
    $ht .= '</ul>';
    return $ht;
  }



  public static function menu($rs = array(), $text = 'Acciones', $ico = 'align-justify', $color = 'white')
  {
    $ht = '<div class="btn-group">' .
      '<a href="#" class="btn btn-' . $color . ' btn-sm dropdown-toggle no-caret" data-toggle="dropdown">' .
      '<i class="fa fa-' . $ico . '"></i>' .
      ($text ? '<span> ' . $text . ' </span>' : '') .
      '<span class="caret text-title">' . '</span>' .
      '</a>' .
      '<ul class="dropdown-menu pull-right">';
    foreach ($rs as $idx => $rr) {
      if (is_array($rr)) {
        $text = isset($rr[0]) ? $rr[0] : ($idx + 1);
        $url = isset($rr[1]) ? $rr[1] : '#actions' . $idx;
        $ico = isset($rr[2]) ? '<i class="fa fa-' . $rr[2] . '"></i> ' : '';
        $attr = isset($rr[3]) ? ' ' . $rr[3] : '';
        $ht .= '<li><a href="' . $url . '"' . $attr . '>' . $ico . $text . '</a></li>';
      } elseif ($rr == '-') $ht .= '<li class="divider"></li>';
    }
    return $ht . '</ul></div>';
  }


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
      $ht .= '<li class="disabled"><a href="javascript:;">&#8249;</a></li>';
    } else {
      $ht .= '<li><a href="' . str_replace('%page%', 1, $url) . '">&#171;</a></li>';
      $ht .= '<li><a href="' . $_url . '">&#8249;</a></li>';
    }
    for ($x = 0; $x < $count; $x++) {
      $nn = ($x + $md);
      $_url = str_replace('%page%', $nn, $url);
      $ht .= '<li class="' . ($current == $nn ? 'active' : '') . '"><a href="' . $_url . '">' . ($nn) . '</a></li>';
    }
    $_url = str_replace('%page%', $current + 1, $url);
    if ($current >= $pages) {
      $ht .= '<li class="disabled"><a href="javascript:;">&#8250;</a></li>';
    } else {
      $ht .= '<li class=""><a href="' . $_url . '">&#8250;</a></li>';
      $ht .= '<li><a href="' . str_replace('%page%', $pages, $url) . '">&#187;</a></li>';
    }
    //$ht.='</ul></div>'.($total?'</div>':'');
    return $ht . '</ul></div>';
  }

  public static function link($url, $title = 'abrir', $ico = 'link', $color = 'gray', $target = '')
  {
    return self::btnActions(
      'link',
      array('0', '', '', '', $url),
      'text-' . $color,
      $ico,
      'title="' . $title . '"' .
        ($target ? ' target="' . $target . '"' : '')
    );
  }

  public static function btnActions($op, $id, $css = '', $ico = '', $attr = '')
  {
    $attr = $attr ? $attr : '';
    $content = '<i class="fa fa-' . $ico . '"></i>';
    if (is_array($id)) {
      $content = $id[1] ? (($ico ? $content . ' ' : '') . $id[1]) : $content;
      $attr .= isset($id[2]) && $id[2] ? ' actions-op="' . $id[2] . '"' : '';
      $attr .= isset($id[3]) && $id[3] ? ' actions-url="' . $id[3] . '"' : '';
      $attr .= isset($id[4]) && $id[4] ? ' actions-link="' . $id[4] . '"' : '';
      $id = $id[0];
    }
    //<a href="javascript:;" title="Editar" data-modal="modal-add" class="btn-crud btn-edit" data-type="edit-ask" data-val="4"><i class="fa fa-edit"></i> Editar</a>
    return '<span class="btn btn-sm width-32 ' . $css . ' actions-' . $op . '"' .
      ' actions-id="' . $id . '"' . $attr . '>' . $content . '</span>';
  }

  public static function menuActions($rs = array(), $ico = 'align-justify', $color = 'white')
  {
    $ht = '<div class="btn-group">' .
      '<a href="#" class="btn btn-' . $color . ' btn-sm dropdown-toggle  no-caret" data-toggle="dropdown"  data-bs-toggle="dropdown">' .
      '<i class="fa fa-' . $ico . '"></i>' .
      '<span class="caret text-title"></span>' .
      '</a>' .
      '<ul class="dropdown-menu pull-right">';
    foreach ($rs as $idx => $rr) {
      if (is_array($rr)) {
        $text = isset($rr[0]) ? $rr[0] : ($idx + 1);
        $url = isset($rr[1]) ? $rr[1] : '#actions' . $idx;
        $ico = isset($rr[2]) ? '<i class="fa fa-' . $rr[2] . '"></i> ' : '';
        $attr = isset($rr[3]) ? ' ' . $rr[3] : '';
        $ht .= '<li><a href="' . $url . '"' . $attr . '>' . $ico . $text . '</a></li>';
      } elseif ($rr == '-') $ht .= '<li class="divider"></li>';
    }
    return $ht . '</ul></div>';
  }

  public static function setPage($id, $name, $rr, $url, $btns = '')
  {
    $lt = '';
    foreach ($rr as $nn) {
      $_url = str_replace('%count%', $nn, $url);
      $lt .= '<a class="dropdown-item" href="' . $_url . '">' . $nn . '</a>';
    }


    $ht = '<div class="btn-group" role="group" >
        <div class="btn-group" role="group">
          <button id="btnGroupDrop' . $id . '" type="button" class="btn btn-sm btn-white dropdown-toggle" data-toggle="dropdown"  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ' . $name . '
          </button>
          <div class="dropdown-menu" aria-labelledby="btnGroupDrop' . $id . '">
            ' . $lt . '
          </div>
        </div>
        ' . $btns . '
      </div>';

    return $ht;
  }

  public static function row($id, $rr, $tag = 'td', $srt = NULL, $multi = NULL)
  {
    if (is_array($id)) {
      $ht = '<tr id="' . $id[0] . '"' . (isset($id[1]) ? ' class="' . $id[1] . '"' : '') . '>';
    } else {
      $ht = '<tr id="' . $id . '">';
    }
    $ord = $multi ? 'actions-sortable all-columns' : 'actions-sortable';
    $cs = array($ord . ' ', $ord . ' asc ', $ord . ' desc ');
    foreach ($rr as $x => $el) {
      $attr = '';
      if (is_array($el)) {
        $content = $el[0];
        $attr = ' ' . (isset($el[1])?$el[1]:'');
      } else $content = $el;
      if (is_array($srt)) {
        if (isset($srt[$x])) {
          //$attr = $attr?$attr:' class=""';
          if (stripos($attr, 'class') === FALSE) $attr .= ' class=""';
          if ($srt[$x] == '-' || $srt[$x] == '')
            $attr = str_replace('class="', 'class="actions-sortable none ', $attr);
          else $attr = str_replace('class="', 'class="' . $cs[$srt[$x]], $attr);
          //$_attr = 'class="fa "';
          //if ($srt[$x] == '-' || $srt[$x] == '')
          //  $_attr = str_replace('class="', 'class="actions-sortable none ', $_attr);
          //else $_attr = str_replace('class="', 'class="' . $cs[$srt[$x]], $_attr);
          //$content= '<i '.$_attr.'"></i><span>'. $content.'</span>';
        }
      }
      $ht .= '<' . $tag . $attr . '>' . $content . '</' . $tag . '>';
    }
    return $ht . '</tr>';
  }

  public static function trSwitcher($name, $idx = 0, $chek = FALSE, $label = NULL, $action = NULL)
  {
    return '<tr>
            <td style="width:1%">
                <div class="switcher switcher-success switcher-sm">
                    <input class="actions-check" actions-event="enabled/labelSwitcher' . $idx . ($action ? '|' . $action : '') . '"
                        type="checkbox" name="' . $name . '" id="inputSwitcher' . $idx . '"
                        ' . ($chek ? ' checked="checked"' : '') . ' value="1">
                    <label for="inputSwitcher' . $idx . '"></label>
                </div>
            </td>
            ' . ($label ? '<td><label class="h5 semi-bold m-0 d-sm-block" for="inputSwitcher' . $idx . '" id="labelSwitcher' . $idx . '">
                ' . $label . '</label></td>' : '') . '
        </tr>';
  }

  public static function trAlert($title, $text = '', $trAttr = '', $tdAttr = '')
  {
    return '<tr' . ($trAttr ? " $trAttr" : '') . '>
            <td' . ($tdAttr ? " $tdAttr" : '') . '>
                <div class="alert alert-warning fade show">
                    <strong>' . $title . '</strong>
                    ' . $text . '
                </div>
            </td>
        </tr>';
  }

  public static function tableSection($name, $id, $content = '', $animation = '', $prepend = false, $append = FALSE)
  {
    return array(
      "idSection" => '#idTable' . $name,
      "idContent" => '#idTable' . $name . ' tbody',
      "idElement" => '#idRow' . $name . $id,
      "animation" => $animation,
      "prepend" => $prepend ? true : false,
      "append" => $append ? TRUE : FALSE,
      "content" => $content
    );
  }

  public static function dataContent($name, $id, $content = '', $animation = '', $prepend = false)
  {
    return array(
      "idContent" => '#idContent' . $name,
      "idElement" => '#idElement' . $name . $id,
      "animation" => $animation,
      "prepend" => $prepend ? true : false,
      "content" => $content
    );
  }

  public static function elementContent($name, $content = '', $animation = '')
  {
    return array(
      "idElement" => '#idElement' . $name,
      "animation" => $animation,
      "content" => $content
    );
  }

  public static function dataRemove($id, $animation = 'bounceOutLeft')
  {
    return array(
      'idElement' => $id,
      'animation' => $animation
    );
  }

  static public function btnsPanel()
  {
    return ' <div class="panel-heading-btn">
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand">
            <i class="fa fa-expand"></i>
        </a>
        <a href="javascript:location.reload();" class="btn btn-xs btn-icon btn-circle btn-success">
            <i class="fa fa-redo"></i>
        </a>
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-toggle="collapse" data-target="#idInfo">
            <i class="fa fa-question"></i>
        </a>
    </div>';
  }
}
