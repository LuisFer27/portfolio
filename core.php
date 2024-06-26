<?php 
/**
 * Aif core
 *
 * Este archivo es el que prepara las definiciones e instrucciones para
 * cargar el Framework AIF
 * @author Martin R. Mondragon Sotelo
 * @copyright 
 * @package Aif
 * @since $Date$
 * @subpackage Core
 * @version $Id$
 */
/**
 * Define el directorio base donde se encuentra core.php del Aif
 */
defined ( 'AIF_PATH' ) || define ( 'AIF_PATH', dirname ( __FILE__ ) );
/**
 * Define el directorio de aplicaciones
 */
defined ( 'AIF_APP' ) || define ( 'AIF_APP', AIF_PATH . '/app' );
/**
 * Detectar soporte para componentes tipo pck(app.aif)
 */
defined ( 'AIF_PCK' ) || define ( 'AIF_PCK', file_exists ( AIF_PATH . '/app.aif' ) );
/**
 * Soporte para componentes tipo pck
 */
AIF_PCK && define ( "AIF\x5FAPP\x5F", "\x70h\x61r\x3A/\x2F" . AIF_PATH . '/app.aif' );
/**
 * Path del directorio web de instalacion
 */
if (! defined ( 'AIF_WEB' )) {
	if (! empty ( $_SERVER ['SCRIPT_FILENAME'] ))
		define ( 'AIF_WEB', dirname ( $_SERVER ['SCRIPT_FILENAME'] ) );
	elseif (! empty ( $_SERVER ['PATH_TRANSLATED'] ))
		define ( 'AIF_WEB', dirname ( $_SERVER ['PATH_TRANSLATED'] ) );
	else
		die ( 'Not find the definition of AIF_WEB in index.php' );
}
/**
 * Carga el FrameWork AIF
 */
include_once (defined ( 'AIF_APP_' ) ? AIF_APP_ : AIF_APP) . "/aif_modules/Aif.php";
/**
 * Define la funcion de autocarga para las aplicaciones
 */

 //var_dump(spl_autoload_register); die();
if (function_exists ( 'spl_autoload_register' )) { spl_autoload_register ( 'Aif::autoLoad' );} 
//else { function __autoload($className) { Aif::autoLoad ( $className );} }//habilitar para versiones inferiores de php 5
/**
 * Funciones global Magic
 * install:  aif install Magic
 */
function __() { return Aif::ecsc('aif/magic','run',array(func_get_args()),'');}
/**
 * Funciones globales para manejo de idiomas
 * install:  aif install Lang
 * @return void
 */
function _l() { return Aif::ecsc('aif/lang','get',array(func_get_args()),'');}
function _lo() { Aif::ecsc('aif/lang','out',array(func_get_args()),''); }
?>
