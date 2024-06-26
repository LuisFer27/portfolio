<?php
/**
 * Prepara el despliege de las vistas de la aplicación
 *
 * Agrupa los métodos necesarios para el despliege de las vistas de
 * las aplicaciones
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Core
 * @version $Id$
 */

/**
 * Clase principal de despliege de la vista
 *
 * @package Aif
 * @subpackage Core
 */
class AifView {
    /**
     * Define la constante para asignar la contenedor principal
     * @var integer
     */
    const INDEX = 31200;
    /**
     * Vistas principales
     * @var integer
     */
    const PAGE = 31201;
    /**
     * Define la constante para definir el contenedor principal para ajax
     * @var integer
     */
    const AJAX = 31202;

    public static $isView=0;
    /**
     * Variables devueltas por el método en ejecución
     * @var array
     */
    public static $__vars = array();
    /**
     * Nombres de los archivos de las vistas a cargar
     * @var array
     */
    private static $__views = array();

    /**
     * Contenido html devuelto por el método en ejecución
     * @var string
     */
    public static $__return = '';
    /**
     * Variable para bloquear la salida o return de una vista
     * @var unknown
     */
    public static $LOCK_VIEWS = FALSE;
    /**
     * Arreglo de path de las vistas, [0]= Ruta absoluta, [1]= Ruta web,
     * [2] = Nombre completo de la vista dependiendo el metodo
     * @var array
     */
    public static $__path = array('', '', '');
    /*
     * ************************************************************************
     * INICIO -Implementacion para manejar objetos de vistas
     * ************************************************************************
     */
    /**
     * Almacena los valores del objeto vista
     * @var array
     */
    private $__vthis = array();
    /**
     * Carga las vistas para static
     */
    public function __construct() {

        $ar = func_get_args();
        is_array($ar) && count($ar) && ($this->__vthis[0] = $ar);
    }
    /**
     * Devuelve el resultado del un objeto de vista en una cadena
     */
    public function __toString() {
        if (!isset($this->__vthis[2])) {
            $tmp = ob_get_contents();
            $isHtml = Aif::ecsc('aif/html','check');
            if(!$this->__vthis[0][0]){
                $isHtml = FALSE;
                array_shift($this->__vthis[0]);
            }
            @ob_clean();
            ob_start();
            isset($this->__vthis[1]) && extract($this->__vthis[1]);
            self::$__path[0] = self::$__path[0] ? self::$__path[0] : AIF_WEB;
            $isHtml && self::outHeader();
            if (isset($this->__vthis[0])) {
                foreach ($this->__vthis[0] as $page) {
                    Aif::ecsc('aif/debugger','set',Array('Objet/View', $page),2);
                    require( self::getPathView($page) );
                }
            }
             Aif::ecsc('aif/html','check') && self::outFooter();
            $this->__vthis[2] = ob_get_contents();
            ob_end_clean();
            echo $tmp;
        }
        return $this->__vthis[2];
    }
    /**
     * Registra variables para ser usadas en instancias de objetos de vista
     * @param string $var
     * @param string $val
     */
    public function __set($key, $val) {
        if ($key)
            $this->__vthis[1][$key] = $val;
    }
    /*
     * ************************************************************************
     * ************************************************************************
     */
    /**
     * Asigna el path de las vistas y contenido publico
     * @param string $path Directorio relativo o absoluto
     * @param boolean $abs Asigna ruta absoluta sin considerar el directorio de publicacion
     */
    public static function setPath($path = '', $abs = FALSE) {
        if ($abs) {
            self::$__path[0] = realpath($path);
        } else {
            self::$__path[0] = realpath(AIF_WEB . $path);
            self::$__path[1] = Aif::$site . $path;
        }
    }
    /**
     * Regresa la ruta web de publicación, también regresa el path de las vistas
     * @param string $app (opcional) TRUE = Path de aplicacion o, FALSE patha web de la vista
     * @return string
     */
    public static function getPath($app = FALSE) {
        if (self::$__path[0] == '')
            self::$__path[0] = AIF_WEB;
        return $app ? ( realpath( ($app==='VIEW' && isset(self::$__path[3]) ? self::$__path[3] :
				(self::$__path[0] . ($app !== TRUE ? $app : ''))) ) ) : (self::$__path[1]);
    }
    /**
       * Asigna el path de todas las vistas phtml
       * @param string $path Directorio relativo o absoluto
       * @param boolean $abs Asigna ruta absoluta sin considerar el directorio de publicacion
       */
      public static function setPathView($path = '', $abs = FALSE) {
        self::$__path[3] = $abs ? realpath($path) : realpath(AIF_WEB . $path);
      }
      /**
       * [getPathView description]
       * @param  [type] $file [description]
       * @return [type]       [description]
       */
      public static function getPathView($file=''){
        return (isset(self::$__path[3]) ? self::$__path[3] : self::$__path[0])."".$file;
      }
    /**
     * Agrega las variables para su disponibilidad en el despliege de la vista
     * @param mixid $__vars Nombre de la etiqueta, Arreglo asociativo u objeto
     * @param mixid $val (opcional) Valor de la etiqueta
     */
    public static function add($vars = NULL, $val = NULL) {
        if (!$vars)
            return false;
        elseif ($val === NULL) {
            if (is_array($vars)) {
                foreach ($vars as $key => $val) {
                    if (is_object($val) && method_exists($val, '__render')) {
                        if (is_numeric($key))
                            self::addObject($val);
                        else
                            self::addObject($key, $val);
                    } else {
                        Aif::ecsc('aif/debugger','set',Array('Var/' . $key, gettype($val)),2);
                        self::$__vars[$key] = $val;
                    }
                }
            } elseif (is_object($vars)) {
                if (method_exists($vars, '__render'))
                    self::addObject($vars);
                else {
                    $arr = get_object_vars($vars);
                    foreach ($arr as $key => $val) {
                        if (is_object($val) && method_exists($val, '__render')) {
                            if (is_numeric($key))
                                self::addObject($val);
                            else
                                self::addObject($key, $val);
                        } else {
                            Aif::ecsc('aif/debugger','set',Array('Var/' . $key, gettype($val)),2);
                            self::$__vars[$key] = $val;
                        }
                    }
                }
            } else {
                self::$__vars['@'][] = $vars;
            }
        } else {
            switch ($vars) {
            case self::PAGE:
                Aif::ecsc('aif/debugger','set',Array('View', $val),2);
               // self::PAGE($val);
                break;
            default:
                Aif::ecsc('aif/debugger','set',Array('Var/' . $vars, gettype($val)),2);
                self::$__vars[$vars] = $val;
            }
        }
    }
    /**
     * Agrega objetos como variables para el despliege de la vista
     * @param string|object $vars Nombre de la etiqueta, tambien puede ser un objeto
     * @param object $obj (opcional) Objeto tipo widget
     */
    public static function addObject($vars, $obj = NULL) {
        if (!$obj) {
            $obj = $vars;
            $vars = $obj->getName();
        }
        Aif::ecsc('aif/debugger','set',Array('Object/' . $vars, get_class($obj)),2);
        $obj->__render();
        self::$__vars[$vars] = $obj;
    }
    /**
     * Devuelve el valor de una variable
     * @param string $name Nombre de la variable
     * @return string Valor de la etiqueta
     */
    public static function getVal($name) {
        return (isset(self::$__vars[$name]) ? self::$__vars[$name] : FALSE);
    }
    /**
     * Registra un archivo de vista para su despliege
     * @param string $page nombre de archivo de vista
     */
    public static function set($page, $ty = NULL) {
    	if(self::$LOCK_VIEWS) return FALSE;
        switch ($ty) {
        case self::INDEX:
        case self::AJAX:
            Aif::ecsc('aif/debugger','set',Array('View/Set/Index', $page),2);
            self::$__views[$ty] = $page;
            break;
        default:
            Aif::ecsc('aif/debugger','set',Array('View/Set', $page),2);
            self::$__views[$page] = $page;
        }
        self::$isView = count(self::$__views) ? TRUE : FALSE;
    }
    /**
     * De todos las vistas recibidas por parametro solo carga la primera que existe
     * @return boolean
     */
    public static function setFirst() {
        if (func_num_args()) {
            self::$__path[0] = self::$__path[0] ? self::$__path[0] : AIF_WEB;
            $ar = func_get_args();
            foreach ($ar as $page) {
                if (is_readable(self::getPathView($page))) {
                    self::set($page);
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    /**
     * Activa la salida en la vista los elementos construidos con el AifHTML
     * @param string $type
     */
    public static function setAifHtml($type = 'html5') {
        Aif::ecsc('aif/html','useTypeHtml',$type);
    }
    /**
     * [clearLayout description]
     * @param  [type] $page [description]
     * @return [type]       [description]
     */
    public static function clearLayout() {
      self::setNull(self::INDEX);
    }
    /**
     * Asigna el archivo de vista contenedor (index)
     * @param String $page Achivo de visa principal
     */
    public static function setLayout($page) {
        self::set($page, self::INDEX);
    }
    /**
     * Carga la vista contenedora para los métodos de salida ajax
     * @param String $page Vista a cargar
     */
    public static function setLayoutAjax($page) {
        self::set($page, self::AJAX);
    }
    /**
     * Reemplaza un archivo de vista para su despliege
     * @param String $ant Vista a utilizar en caso de $new sea NULL
     * @param string $new Vista a reemplazar
     */
    public static function change($ant, $new = NULL) {
    	if(self::$LOCK_VIEWS) return FALSE;
        if ($new == NULL) {
            if (Aif::$render == 'ajax') {
                self::$__views = array(
                        self::AJAX => self::$__views[self::AJAX], $ant => $ant);
            } else {
                self::$__views = array(
                        self::INDEX => (isset(self::$__views[self::INDEX]) ? self::$__views[self::INDEX] : 0),
                        $ant => $ant);
            }
        } else {
            $arr = array();
            foreach (self::$__views as $key => $url) {
                if ($ant == $key)
                    $key = $url = $new;
                $arr[$key] = $url;
            }
            self::$__views = $arr;
        }
    }

    /**
     * Borrar una o varias vistas de un grupo registrada
     * @parama integer $ty (Opcional)Grupo de vista a eliminar, si no se especifica
     * borra todas las vistas
     */
    public static function setNull($ty = NULL) {
      unset(self::$__views[self::INDEX]);
        switch ($ty) {
        case self::PAGE:
            $arr = array();
            if (isset(self::$__views[self::INDEX]))
                $arr[self::INDEX] = self::$__views[self::INDEX];
            if (isset(self::$__views[self::AJAX]))
                $arr[self::AJAX] = self::$__views[self::AJAX];
            self::$__views = $arr;
            break;
        case self::INDEX:
            if (isset(self::$__views[self::INDEX]))
                unset(self::$__views[self::INDEX]);
            break;
        case self::AJAX:
            if (isset(self::$__views[self::AJAX]))
                unset(self::$__views[self::AJAX]);
            break;
        default:
            self::$__views = array();
            break;
        }
        self::$isView = count(self::$__views) ? TRUE : FALSE;
    }

    public static function clean($ty=NULL){
        self::setNull($ty);
    }
    /**
     * Imprime la salida HTML de los encabezados formado por medio de la clase AifHtml
     */
    public static function outHeader() {
        echo Aif::ecsc('aif/html','getHeader');
    }
    /**
     * Imprime la salida HTML devuelto del método en ejecución
     */
    public static function outContent() {
        echo self::$__return;
    }
    /**
     * Imprime la salida HTML devuelto del método en ejecución
     */
    public static function getReturn() {
        echo self::$__return;
    }
    /**
     * Imprime la salida HTML del pie de pagina formado por medio de la clase AifHtml
     */
    public static function outFooter() {
        echo  Aif::ecsc('aif/html','getFooter');
    }
    /**(isset(self::$__path[3]) ? self::$__path[3] : self::$__path[0])
     * Hace el despliege de la vista especifica, ó bien el despliege de todas las vistas
     * @param string|integer $idxPagBody (opcional) nombre de archivo o grupos de vistas
     */
    public static function content($idxPagBody = NULL, $onlyHtml_=FALSE) {
        if (isset(self::$__vars['@'])) {
            self::$__return .= implode(' ', self::$__vars['@']);
            unset(self::$__vars['@']);
        }
        self::$__path[0] = self::$__path[0] ? self::$__path[0] : AIF_WEB;
        if ($idxPagBody === NULL) {
            $isVar = false;
            foreach (self::$__views as $idx => $idxPagBody) {
                if ($idx != self::INDEX && $idx != self::AJAX
                        && file_exists(self::getPathView($idxPagBody))) {
                    if (!$isVar) {
                        //print_r(self::$__vars); die();
                        extract(self::$__vars);
                        $isVar = true;
                    }
                    Aif::ecsc('aif/debugger','set',Array('View/out', $idxPagBody),2);
                    require(self::getPathView($idxPagBody));
                }
            }
            if($onlyHtml_){
            	return ob_get_clean();
            }
            if (!$isVar) {
                self::outContent();
            }
        } else {
            if (isset(self::$__views[$idxPagBody])) {
                switch ($idxPagBody) {
                case self::INDEX:
                case self::AJAX:
                    $idxPagBody = self::$__views[$idxPagBody];
                    break;
                }
            }
            $onlyHtml = $onlyHtml_ ? FALSE :  Aif::ecsc('aif/html','check');
            if (file_exists(self::getPathView($idxPagBody)) ) {
                extract(self::$__vars);
                $onlyHtml && self::outHeader();
                require(self::getPathView($idxPagBody));
                $onlyHtml && self::outFooter();
                Aif::ecsc('aif/debugger','set',Array('View/out/Index', $idxPagBody),2);
            } else {
                $onlyHtml && self::outHeader();
                if($onlyHtml_)
                	return self::content(NULL, TRUE);
                else
                	self::content();
                $onlyHtml && self::outFooter();
                Aif::ecsc('aif/debugger','set',Array('View/out/HTML', 'AifHtml'),2);
            }
        }
    }
    /**
     * Undocumented function
     *
     * @param [type] $section
     * @return void
     */
    public static function section($section, $vars=NULL, $vars2=array()) {
        if (file_exists(self::getPathView($section)) ) {
            if(is_array($vars)) extract($vars);
            if(is_array($vars2)) extract($vars2);
            require(self::getPathView($section));
            Aif::ecsc('aif/debugger','set',Array('View/out/section',$section),2);
        }else {
            Aif::ecsc('aif/debugger','set',Array('View/not found',$section),2);
        }
    }
    /**
     * Permite habilitar la salida construida por medio de la clase AifHtml
     * @param boolean $ty Activa o desactiva la creacion del header y footer
     */
    public static function isHtml($ty = true) {
         Aif::ecsc('aif/html','useHtml',$ty);
    }
    /**
     * Permite autodetectar las vistas para ser utilizadas en los metodos en
     * relacion las variables dentro del $path
     * %a = Nombre de la Aplicación
     * %c = Nombre de la clase
     * %m = Nombre del metodo
     * @param string $path Cadena del nombre de la vista para autocarga
     * @param string $ext (Opcional)Extension del archivo a auto cargar
     */
    public static function load($path = '%a/%c/%m', $ext = 'phtml') {
    	if(self::$LOCK_VIEWS) return FALSE;
        if ($path === NULL && isset(self::$__views[self::$__path[2]])) {
            unset(self::$__views[self::$__path[2]]);
        } else {
            self::$__path[2] = str_replace(array('%a', '%c', '%m'),
                    array(Aif::$application, Aif::$class, Aif::$method), $path). '.' . $ext;
            self::set(self::$__path[2]);
        }
    }
    /**
     * [exists description]
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    public static function exists($file){
      return  Aif::ecsc('aif/lib/file','exists',self::getPathView($file));
    }


    public static function send($result){
        if(self::$isView ){
            Aif::$render == 'ajax' && self::setNull(self::INDEX);
            self::add($result);
            self::content(self::INDEX);
            return TRUE;
        }
        return FALSE;
    }
}
