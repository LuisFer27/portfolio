<?php
/**
 * Manejo de sesiones
 *
 * Provee los metodos necesarios para el manejo de las sesiones
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com
 *            AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Lib
 * @version $Id$
 */

/**
 * Clase para manejo de sesiones
 *
 * @package Aif
 * @subpackage Lib
 */
class AifSession {
	/**
	 * Identificador de sesion activa
	 *
	 * @var string
	 */
	public static $id = NULL;
	/**
	 * Identificador de autenticacion de la sesion
	 *
	 * @var string
	 */
	public static $seg = NULL;
	/**
	 * DEFAULT: TRUE, identificador para eliminar sesión si NO esta autentificado
	 * CHANGE : FALSE, identificador para NO eliminar sesión si NO esta
	 * autentificado
	 *
	 * @var boolean
	 */
	public static $authOn = TRUE;
	/**
	 * Construye la sesion
	 *
	 * @param string $id
	 *        	(Opcional) Nombre de la sesion
	 */
	public static function create($id = NULL) {
		self::$seg = '_' . md5 ( Aif::ecsc('aif/request','getIp'));
		self::$id = '_' . md5 ( $id ? $id : Aif::$application );
		@session_start ();
		//Aif::$safe & 8 && self::safe ();
		if (! self::isAuth ()) {
			if (self::$authOn)
				$_SESSION [self::$id] = array();
			elseif (! isset ( $_SESSION [self::$id] ))
				$_SESSION [self::$id] = array();
		}
		//elseif(Aif::$safe & 64 && !AifSafe::safeDomain()){
		//	self::remove ();
		//	AifResponse::redirect ();
		//}
	}
	/**
	 * Comprueba si existe una session
	 *
	 * @return boolean
	 */
	public static function isSession() {
		return self::$id ? TRUE : FALSE;
	}
	/**
	 * Evita el robo de session del usuario
	 */
	public static function safe() {
		$uid = md5 ( Aif::ecsc('aif/request','getIp') . Aif::ecsc('aif/agent','agent'). '' );
		if (! isset ( $_SESSION ['__safe_uid'] ))
			$_SESSION ['__safe_uid'] = $uid;
		elseif ($_SESSION ['__safe_uid'] != $uid) {
			self::remove ();
			unset ( $_SESSION ['__safe_uid'] );
			Aif::ecsc('aif/response','redirect');
		}
	}
	/**
	 * Comprueba si existe una variable en la sesion
	 *
	 * @param string $var
	 *        	Nombre de la variable
	 * @return boolean
	 */
	public static function isVar($var) {
		return isset ( $_SESSION [self::$id] ) && isset ( $_SESSION [self::$id] [$var] ) ? true : false;
	}
	/**
	 * Regresa un valor de una variable en la sesion
	 *
	 * @param string $var
	 *        	Nombre de la variable de session
	 * @return mixed Regresa el valor de la variable de session
	 */
	public static function get($var) {
		return self::isVar ( $var ) ? $_SESSION [self::$id] [$var] : false;
	}
	/**
	 * Registra una variable en la sesion
	 *
	 * @param string $var
	 *        	Nombre de la variable de session
	 * @param mixed $val
	 *        	Valor que sera asignado a la session
	 */
	public static function set($var, $val) {
		$_SESSION [self::$id] [$var] = $val;
	}
	/**
	 * Elimina un variable de la session
	 *
	 * @param unknown $var
	 */
	public static function del($var) {
		if (self::isVar ( $var ))
			unset ( $_SESSION [self::$id] [$var] );
	}
	/**
	 * Autentica la sesion
	 */
	public static function setAuth() {
		self::set ( self::$seg, md5 ( session_id () ) );
	}
	/**
	 * Verificar si existe autentificacion
	 * return bool Regresa verdadero si esta autenticada la sesion
	 */
	public static function isAuth() {
		$sId = md5 ( session_id () );
		return ( boolean ) $sId && self::get ( self::$seg ) === $sId ? true : false;
	}
	/**
	 * Elimina la sesion activa
	 */
	public static function remove() {
		unset ( $_SESSION [self::$id] );
	}
}
