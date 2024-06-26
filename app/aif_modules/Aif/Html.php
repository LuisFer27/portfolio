<?php
/**
 * Manipulación y construcción de contenido HTML
 *
 * Manipulación y construcción de contenido HTML
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Core
 * @version $Id$
 */

/**
 * Clase para construir contenido HTML
 *
 * @package Aif
 * @subpackage Core
 */
class AifHtml {
    /**
     * Arreglo que contiene los archivos
     * @var array
     */
    public static $files = array();
    /**
     * Arreglo que contiene bloques de codigo
     ** @var array
     */
    public static $source = array();

    /**
     *
     * @var unknown
     */
    public static $cache = array("script"=>FALSE, "link"=>FALSE);

    /**
     *
     * @var unknown
     */
    public static $scriptHead = array();

    /**
     * [$cssHead description]
     * @var array
     */
    public static $cssHead = array();

    /**
     * Tipo de documento para el HTML
     * @var string
     */
    public static $docType = 'html4';
    /**
     * Agregar el Tag Body en el contenido web
     * @var boolean
     */
    public static $isBody = false;
    /**
     * Contiene todas las etiquetas html construidas
     * @var array
     */
    public static $element = array('head' => '', 'header' => '', 'footer' => '');
    /**
     * Caracter para fin de liena
     * @var string
     */
    public static $ln = '';

    public static $isHtml = FALSE;


    public static $isAppCache = FALSE;
    /**
     * Asigna el salto de linea entre etiquetas
     */
    public static function setLineBreak($ln) {
        self::$ln = $ln;
    }
    /**
     * Construye una etiqueta HTML
     * @param string $tag Nombre de la etiqueta
     * @param array $atr Arreglo asociativo de las propiedades o Contenido
     * @param string $ty Etiqueta vacia
     * @return string Regresa la etiqueta
     */
    public static function getTag($tag, $atr = array(), $ty = 'one') {
        $str = '<' . $tag;
        $value = '';
        if (is_array($atr)) {
            foreach ($atr as $key => $val) {
                $str .= ' ' . $key . '="' . addslashes($val) . '"';
            }
        } else {
            $value = (string) $atr;
        }
        switch ($ty) {
        case 'open':
            return $str . '>';
        case 'tag':
            return $str . '>' . $value . '</' . $tag . '>';
        }
        return $str . ' />';
    }

    public static function check(){
        return self::$isHtml;
    }


    public static function useHtml($ty=TRUE){
        self::$isHtml=$ty;
    }

    /**
     * Regresa el contenido del encabezado de HTML
     * @return string
     */
    public static function getHeader() {
        $html = array();
        $ln = self::$ln;
        $isBlock = array('title' => 1, 'script' => 1);
        switch (mb_strtolower(self::$docType)) {
        case 'xhtml':
            $doc = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"'
                    . ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
                    . $ln;
            $html = array('xmlns' => 'http://www.w3.org/1999/xhtml',
                    'lang' => 'es');
            break;
        case 'html5':
            $doc = '<!DOCTYPE html>' . $ln;
            $html = array('lang' => 'es');
            self::$isAppCache && ( $html['manifest']= Aif::ecsc('aif/cache','getAppFileName'));
            break;
        case 'html4':
        default:
            $doc = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"'
                    . ' "http://www.w3.org/TR/html4/strict.dtd">' . $ln;
        }
        $body = '';
        $head = '<head>';
        $els = self::$element['head'];
        $if = '@';
        if (isset($els[$if])) {
            $ele = $els[$if];
            unset($els[$if]);

            if (isset($ele['html'])) {
                $html = array_merge($html, $ele['html']);
                unset($ele['html']);
            }
            if (isset($ele['head'])) {
                $head = self::getTag('head', $ele['head'], 'open');
                unset($ele['head']);
            }
            if (isset($ele['body'])) {
                $body = self::getTag('body', $ele['body'], 'open');
                self::$isBody = true;
                unset($ele['body']);
            }
            $doc .= self::getTag('html', $html, 'open') . $ln . $head . $ln;

            do {
                $htm = '';
                foreach ($ele as $tag => $arr) {
                    if (isset($isBlock[$tag])) {
                        $htm .= self::getTag($tag, $arr, 'tag') . $ln;
                    } else {
                        if (isset($arr[0])) {
                            foreach ($arr as $dat) {
                                $htm .= self::getTag($tag, $dat) . $ln;
                            }
                        } else {
                            //$htm .= self::getTag($tag, $arr) . $ln;
                        }
                    }
                }
                $doc .= $if !== '@' ? '<!--[if ' . $if . ']>' . $ln . $htm
                                . '<![endif]-->' . $ln : $htm;
                $if = key($els);
            } while (($ele = array_shift($els)));
        } else {
            $doc = self::getTag('html', $html, 'open') . $ln . '<head>' . $ln
                    . '<title>' . $ln . Aif::$class . '</title>' . $ln;
        }
//print_r(self::$scriptHead);die();
        return $doc.self::getScriptTag('prepend') . self::getFiles() . self::getSource()
        		. self::getCssTag('head'). self::getScriptTag('head'). '</head>' . $ln
                . $body . self::$element['header'] . $ln;
    }

    /**
     *
     * @param unknown $if
     * @param string $html
     */
    public static function setIf($if,$html=''){

    }

    public static function clean($ty=FALSE){
        self::$isHtml=$ty;
        self::$isBody = FALSE;
        self::$ln = '';
        self::$files=array();
        self::$source = array();
        self::$element = array('head' => '', 'header' => '', 'footer' => '');
        self::$scriptHead=array();
    }
    /**
     * Borra todo el contenido HTML de la clase AifHtml
     */
    public static function setNull() {
        self::clean();
    }
    /**
     * Regresa el contenido del cierre de HTML
     * @return string
     */
    public static function getFooter() {
        return self::$element['footer']
                . (self::$isBody ? self::$ln . '</body>' : '') . self::$ln
                . '</html>' . self::$ln;
    }
    /**
     * Pone contenido en el cuerpo del HTML en la parte superior
     * @param string $html
     * @param string $replace
     */
    public static function header($html = '', $replace = false) {
        if ($replace)
            self::$element['header'] = $html . self::$ln;
        else
            self::$element['header'] .= $html . self::$ln;
    }
    /**
     * Pone contenido en el cuerpo del HTML en la parte inferior
     * @param string $html
     * @param string $replace
     */
    public static function footer($html = '', $replace = false) {
        if ($replace)
            self::$element['footer'] = $html . self::$ln;
        else
            self::$element['footer'] .= $html . self::$ln;
    }
    /**
     * Asigna el tipo de documento HTML y habilita la salida en HTML
     * @param string $type
     */
    public static function setDocType($type) {
        self::$docType = $type;
    }
    /**
     * Registra una etiqueta en el head del HTML
     * @param string $tag
     * @param array $atr
     * @parama string $if Bloques condicionales validos para IE <!--IF..
     */
    public static function set($tag, $atr = NULL, $if = '@', $by = 'head') {
        $tag = mb_strtolower($tag);
        if ($atr != NULL) {
            switch ($tag) {
            case 'html':
            case 'head':
            case 'body':
            //No se considera los tag condicioales para estos elementos
                self::$element[$by]['@'][$tag] = $atr;
                break;
            default:
                if (is_array($atr) && count($atr)) {
                    if (isset($atr[0])) {
                        foreach ($atr as $ar) {
                            self::$element[$by][$if][$tag][] = $ar;
                        }
                    } else {
                        self::$element[$by][$if][$tag][] = $atr;
                    }
                } else {
                    is_array(self::$element[$by]) || (self::$element[$by]=array());
                    isset(self::$element[$by][$if]) || (self::$element[$by][$if]=array());
                    self::$element[$by][$if][$tag] = $atr;
                }
            }
        } else {
            $tags = array('body');
            if (in_array($tag, $tags)) {
                self::$element[$by]['@'][$tag] = array();
            }
        }
    }
    /**
     * Asigna el titulo
     * @param string $title
     */
    public static function setTitle($title) {
        self::head('title', $title);
    }
    /**
     * construye una etiqueta en head
     * @param string $tag Nombre de la etiqueta
     * @param array|string $atr Propiedades o valor de la etiquea
     * @param string $if Etiqueta de comentario condicional para IE
     */
    public static function head($tag, $atr = NULL, $if = '@') {
        self::set($tag, $atr, $if, 'head');
    }
    /**
     * Asigna la ruta web base del HTML
     * @param string $href
     * @param string $if Etiqueta de comentario condicional para IE
     */
    public static function setBase($href, $if = '@') {
        self::set('base', array('href' => $href), $if);
    }
    /**
     * Asigna un icono tipo Apple Toch Icon
     * @param string $href
     * @param string $if Etiqueta de comentario condicional para IE
     */
    public static function setAppleTouchIcon($href, $if = '@') {
        self::set('link',
                array('rel' => 'apple-touch-icon',
                        'href' => Aif::ecsc('aif/view','getPath') . $href), $if);
    }
    /**
     * Asigna un icono short cut icon
     * @param string $url del archivo
     * @param string $type Formato del icono
     */
     public static function setShortcutIcon($file='',$type="image/png"){
        self::head( 'link',array(
            'rel'=>"shortcut icon",
            'type'=>$type,
            'href'=>$file)
        );
    }
    /**
     * Asigna una etiueta meta en el encabezado HTML
     * @param string $name
     * @param string $content
     * @param string $if Etiqueta de comentario condicional para IE
     */
    public static function setMeta($name, $content, $if = '@') {
        self::set('meta', array('name' => $name, 'content' => $content), $if);
    }
    /**
     * Asigna una etiqueta meta http equiv
     * @param string $name
     * @param string $content
     * @param string $if Etiqueta de comentario condicional para IE
     */
    public static function setMetaHttpEquiv($name, $content, $if = '@') {
        self::set('meta', array('http-equiv' => $name, 'content' => $content),
                $if);
    }

    public static function headCharSet($charset='utf-8'){
        self::head ( 'meta', array ('charset' => $charset ) );
    }

    public static function headMobileWebApp($content='yes'){
        self::head ( 'meta', array ('name' => 'apple-mobile-web-app-capable','content'=>$content) );
        self::head ( 'meta', array ('name' => 'mobile-web-app-capable','content'=>$content) );
    }

   public static function  headViewport($ty='scale',$val='no',$init=1,$min=1,$max=1){
        self::head ( 'meta', array ('name' => 'viewport',
                'content'=>'user-scalable='.$val.', '.
                'initial-scale='.$init.', '.
                'maximum-scale='.$max.', '.
                'minimum-scale='.$min.', '.
                'width=device-width, height=device-height'
            ) 
        );
   }

   /**
    * Undocumented function
    *
    * @param string $type
    * @return void
    */
   public static function useTypeHtml($type='html5'){
    if ($type) {
        self::setDocType($type);
        self::useHtml(TRUE);
    } else
        self::useHtml(FALSE);
   }

   /**
    * Undocumented function
    *
    * @param string $type
    * @param string $title
    * @param string $favicon
    * @return void
    */
   public static function layoutHeader($type='HTML5',$title='',$favicon=''){
        self::useTypeHtml($type);
        self::setTitle ( $title );
        self::head ( 'meta', array ('charset' => 'utf-8') );
        self::head ( 'meta', array ('name' => 'apple-mobile-web-app-capable','content'=>'yes') );
        self::head ( 'meta', array ('name' => 'mobile-web-app-capable','content'=>'yes') );
        self::head ( 'meta', array ('name' => 'viewport','content'=>'user-scalable=no, initial-scale=1,'.
        ' maximum-scale=1, minimum-scale=1, width=device-width, height=device-height') );
        self::head ( 'link',array('rel'=>"shortcut icon", 'type'=>"image/png",'href'=>Aif::ecsc('aif/view','getPath').$favicon));
   }
    /**
     * Carga archivos de estilos para que se cargen en la vista
     * @param string $tag
     * @param array $ar
     * @param array $atr
     * @param string $app
     */
    public static function setFile($tag, $ar, $atr = array(), $app = FALSE,
            $remote = FALSE) {
        if (count($ar)) {
            $url = array_shift($ar);
            $link = ($remote ? '' : ($app ? Aif::$site : Aif::ecsc('aif/view','getPath')))
                    . $url;
            $pt = array_shift($ar);
            $if = '@';
            $ty = 'one';
            if ($pt) {
                if (is_array($pt)) {
                    $atr = array_merge($atr, $pt);
                    $it = array_shift($ar);
                    $if = $it ? $it : $if;
                } else {
                    $if = (string) $pt;
                }
            }
            $hashCache = (isset(self::$cache[strtolower($tag)]) ? self::$cache[strtolower($tag)] : FALSE);
            $hashCache = !$remote && $hashCache ? "?hash=".(is_bool($hashCache) ? md5(date("ymdhis")) : $hashCache) : '';
            switch ($tag) {
            case 'script_remote':
            case 'script':
                $atr['src'] = $link . $hashCache;
                $ty = 'tag';
                break;
            default:
                $atr['href'] = $link . $hashCache;
            }
            $remote || (self::$isAppCache && Aif::ecsc('aif/cache','appCache',$link.$hashCache));
            self::$files[$if][$link] = self::getTag($tag, $atr, $ty);
            Aif::ecsc('aif/debugger','set',Array('File/' . $tag . ':', $link),2);
        }
    }
    /**
     * Asigna un script resuelto por aplicacion para los recurso del Aif/Src
     */
    public static function setAppScript() {
        self::setFile('script', func_get_args(),
                array('type' => 'text/javascript'), TRUE);
    }
    /**
     * Asigna un script desde los recursos del directorio web publico
     */
    public static function setScript() {
        self::setFile('script', func_get_args(),
                array('type' => 'text/javascript'));
    }
    /**
     * Asigna un script desde de una dirección electrónica
     */
    public static function setHttpScript() {
        self::setFile('script', func_get_args(),
                array('type' => 'text/javascript'), FALSE, TRUE);
    }

    /**
     * Carga archivos de estilos para que se cargen en la vista
     */
    public static function setCss() {
        self::setFile('link', func_get_args(),
                array('type' => 'text/css', 'rel' => 'stylesheet'));
    }

     /**
     * Carga archivos de estilos para que se cargen en la vista
     */
    public static function setCssPrint() {
        self::setFile('link', func_get_args(),
                array('type' => 'text/css', 'rel' => 'stylesheet','media'=>'print' ));
    }
    /**
     * Asigna un css desde de una dirección electrónica
     */
    public static function setHttpCss() {
        self::setFile('link', func_get_args(),
                array('type' => 'text/css', 'rel' => 'stylesheet'), FALSE,
                TRUE);
    }
    /**
     * Asigna un script resuelto por aplicacion para los recurso del Aif/Src
     */
    public static function setAppCss() {
        self::setFile('link', func_get_args(),
                array('type' => 'text/css', 'rel' => 'stylesheet'), TRUE);
    }
    /**
     * Obtine las etiquetas de todos los archivos como etiquetas en el head
     * @return string
     */
    public static function getFiles() {
        $doc = '';
        if (count(self::$files)) {
            $nt = '@';
            foreach (self::$files as $if => $ar) {
                if ($if != $nt) {
                    if ($nt != '@')
                        $doc .= '<![endif]-->' . self::$ln;
                    if ($if != '@')
                        $doc .= '<!--[if ' . $if . ']>' . self::$ln;
                    $nt = $if;
                }
                foreach ($ar as $htm) {
                    $doc .= $htm . self::$ln;
                }
            }
            if ($nt != '@')
                $doc .= '<![endif]-->' . self::$ln;
        }
        return $doc;
    }
    /**
     * Registra un recurso src
     * @param string $tag
     * @param array $ar
     * @param array $atr
     */
    public static function setSrc($tag, $ar, $atr = array()) {
        if (count($ar)) {
            $src = array_shift($ar);
            $pt = array_shift($ar);
            $if = '@';
            $ty = 'tag';
            if ($pt) {
                if (is_array($pt)) {
                    $atr = array_merge($atr, $pt);
                    $it = array_shift($ar);
                    $if = $it ? $it : $if;
                } else {
                    $if = (string) $pt;
                }
            }
            $sAtr = implode(',', $atr);
            if (isset(self::$source[$if][$tag][$sAtr])) {
                self::$source[$if][$tag][$sAtr]['src'] .= $src;
            } else {
                self::$source[$if][$tag][$sAtr]['src'] = $src;
            }
            self::$source[$if][$tag][$sAtr]['atr'] = $atr;
            Aif::ecsc('aif/debugger','set',Array('Source/' . $tag . ':', addslashes($src)));
        }
    }

    /**
     * Asigna un codigo fuente script
     */
    public static function setSource() {
        self::setSrc('script', func_get_args(),
                array('type' => 'text/javascript'));
    }

    public static function setSourceCss() {
        self::setSrc('style', func_get_args());
    }
    /**
     * Carga codigo script desde un archivo
     */
    public static function loadSource() {
        $ar = func_get_args();
        if (isset($ar[0])) {
            if (file_exists($ar[0])) {
                $ar[0] = file_get_contents($ar[0]);
                self::setSrc('script', $ar, array('type' => 'text/javascript'));
            }
        }
    }

    /**
     * Carga archivos de estilos para que se cargen en la vista
     */
    public static function setStyle() {
        self::setSrc('style', func_get_args(), array('type' => 'text/css'));
    }

    /**
     * Carga clases de estilo desde un archivo
     */
    public static function loadStyle() {
        $ar = func_get_args();
        if (isset($ar[0])) {
            if (file_exists($ar[0])) {
                $ar[0] = file_get_contents($ar[0]);
                self::setSrc('style', $ar, array('type' => 'text/css'));
            }
        }
    }

    /**
     * Obtiene bloque de codigo script/style
     */
    public static function getSource() {
        $doc = '';
        if (count(self::$source)) {
            $nt = '@';
            foreach (self::$source as $if => $ar) {
                if ($if != $nt) {
                    if ($nt != '@')
                        $doc .= '<![endif]-->' . self::$ln;
                    if ($if != '@')
                        $doc .= '<!--[if ' . $if . ']>' . self::$ln;
                    $nt = $if;
                }
                foreach ($ar as $tag => $ar1) {
                    foreach ($ar1 as $ht) {
                        if (is_array($ht['atr'])) {
                            $str = '';
                            foreach ($ht['atr'] as $key => $val) {
                                $str .= ' ' . $key . '="' . addslashes($val)
                                        . '"';
                            }
                        }
                        $doc .= "<$tag" . "$str>" . self::$ln . $ht['src']
                                . "</$tag>" . self::$ln;
                    }
                }
            }
            if ($nt != '@')
                $doc .= '<![endif]-->' . self::$ln;
        }
        return $doc;
    }

    /**
     * Genera un array para devolver una etiqueta script desde el método getScriptTag
     * @param string  $code  - Código JS que se insertara
     * @param array   $attr  - Array con las propiedades de la etiqueta script
     * @param string  $idx   - Id de la etiqueta generada
     * @param boolean $clean - Variable que indica si se quiere vaciar el array
     *						   con las configuraciones definidas anteriormente
     * @return boolean
     */
    public static function setScriptTag($code='', $attr=array(), $idx='head', $clean=FALSE) {
    	if($code){
    		self::$scriptHead[$idx] = !$clean && isset(self::$scriptHead[$idx]) ? self::$scriptHead[$idx] : array();
	    	if(is_array($attr) && count($attr)){
	    		$resultado  = (isset(self::$scriptHead[$idx]['attr']) ?
	    						self::$scriptHead[$idx]['attr'] : array());
	    		self::$scriptHead[$idx]['attr'] = array_merge($resultado, $attr);
	    	}
	    	$cfg = array();
	    	self::$scriptHead[$idx]['code'] = isset(self::$scriptHead[$idx]['code']) ?
	    										self::$scriptHead[$idx]['code'] : array();
	    	self::$scriptHead[$idx]['code'][] = $code;
    	}
    	return TRUE;
    }

    /**
     * Carga los esitilos para cargar fuentes en etiquetas style
     *
     * @param [type] $name  Nombre de la fuente 
     * @param [type] $file Nombre del archivo sin extension
     * @param string $ty Tipos de fuentes a cargar
     * @param string $css etiquetas de estilos adicionales
     * @return void
     */
    public static function loadFontCss($name,$file,$ty='',$css='',$ver=''){
        $ty=strtolower(trim($ty)?$ty:'eot,woff,woff2,ttf,svg');
        $format = array('ttf'=>'truetype','eot'=>'embedded-opentype');
        $fix = array('eot'=>'?#iefix','svg'=>'#fontawesomeregular');
        $rs = explode(',',$ty);
        $st ="@font-face{font-family:'$name';".
            (strpos($ty,'eot')!==FALSE?"src:url('$file.eot');":'');
        $ff='';
        foreach($rs as $ex){
            $ex = trim($ex);
            $fr = isset($format[$ex])?$format[$ex]:$ex;
            $fx = isset($fix[$ex])?$fix[$ex]:'';
            $fn = $file.'.'.$ex.$ver;
            $ff.= ($ff?',':'').'url(\''.$fn.$fx.'\') format(\''.$fr.'\')';
            self::$isAppCache && Aif::ecsc('aif/cache','appCache',$fn);
        } 
        self::setSourceCss($st.($ff?$ff.';':'').$css.'}');
    }
    /**
     * [setCssTag description]
     * @param string $styles [description]
     * @param array  $attr   [description]
     */
    public static function setCssTag($styles='', $attr=array(), $idx='head', $clean=FALSE) {
    	if($styles){
    		self::$cssHead[$idx] = !$clean && isset(self::$cssHead[$idx]) ? self::$cssHead[$idx] : array();
	    	if(is_array($attr) && count($attr)){
	    		$resultado  = (isset(self::$cssHead[$idx]['attr']) ?
	    						self::$cssHead[$idx]['attr'] : array());
	    		self::$cssHead[$idx]['attr'] = array_merge($resultado, $attr);
	    	}
	    	$cfg = array();
	    	self::$cssHead[$idx]['code'] = isset(self::$cssHead[$idx]['code']) ?
	    										self::$cssHead[$idx]['code'] : array();
	    	self::$cssHead[$idx]['code'][] = $styles;
    	}
    	return TRUE;
    }

    /**
     * Regresa la etiqueta Script del método setScriptTag
     * @param string $id - Identificador de la etiqueta Script generada previamente
     * @return Ambigous string
     */
    public static function getScriptTag($id='head') {
    	$doc = '';
    	$id = strtolower($id);
        if (($id && isset(self::$scriptHead[$id]) && count(self::$scriptHead[$id])) || count(self::$scriptHead)) {
        	$script = isset(self::$scriptHead[$id]) ? self::$scriptHead[$id] : self::$scriptHead;
        	$doc = "<script#ATTR#>#CODE#\n</script>\n";
        	$attr='';
        	$code='';
            if(isset($script['attr']) && count($script['attr'])){
            	foreach ($script['attr'] as $i=>$v){
            		$attr .= " $i=\"$v\"";
            	}
            }
            if(isset($script['code']) && count($script['code'])){
            	foreach ($script['code'] as $j=>$k){
            		$j+=1;
            		$code .= "\n/*---- SCRIPT[$j] ----*/\n".$k;
            	}
            }
            $doc = str_replace(array("#ATTR#","#CODE#"), array($attr, $code), $doc);
        }
    	return $doc;
    }

    /**
     * [getCssTag description]
     * @param  string $id [description]
     * @return [type]     [description]
     */
    public static function getCssTag($id='head') {
    	$doc = '';
    	$id = strtolower($id);
        if (($id && isset(self::$cssHead[$id]) && count(self::$cssHead[$id])) || count(self::$cssHead)) {
        	$css = isset(self::$cssHead[$id]) ? self::$cssHead[$id] : self::$cssHead;
        	$doc = "<style#ATTR#>#CODE#\n</style>\n";
        	$attr='';
        	$code='';
            if(isset($css['attr']) && count($css['attr'])){
            	foreach ($css['attr'] as $i=>$v){
            		$attr .= " $i=\"$v\"";
            	}
            }
            if(isset($css['code']) && count($css['code'])){
            	foreach ($css['code'] as $j=>$k){
            		$j+=1;
            		$code .= "\n/*---- STYLE[$j] ----*/\n".$k;
            	}
            }
            $doc = str_replace(array("#ATTR#","#CODE#"), array($attr, $code), $doc);
        }
    	return $doc;
    }



    public static function send($content=NULL){
        if(self::$isHtml){
            if(is_array($content))$content = print_r($content,TRUE);
            elseif(is_object($content))$content = print_r($content,TRUE);
            echo self::getHeader().$content.self::getFooter();
            return TRUE;
        }
    }

}
