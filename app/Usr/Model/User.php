<?php
class UsrModelUser{

static $lastError='';

/**
 * actualizar usuarios
 */
public static function add($reg){
 $idUser=AifDb::insert($reg,'user');
 return $idUser;
}
  /**
   * Actualiza datos del usuario
   *
   * @param ['user'] $data actualiza los datos del usuario almacenados en el controlador mediante el post.
   * @param ['idUser'] $idUser obtiene el id del usuario.
   * @return void
   */
  public static function upd($data, $idUser)
  {
    AifDb::update('user', $data, 'idUser=' . $idUser);
    return $idUser;
  }
  
  public static function getById($id = 1)
  {
    return AifDb::firstObject(
      'SELECT u.*
      FROM user u
      WHERE u.deleted = 0 AND u.idUser = ' . $id
    );
  }
    /**
   * Consulta para la eliminacion logica
   *
   * @param [type] $id identificador
   * @return void
   */
  public static function delete($id)
  {
    return AifDb::update('user', 'deleted=1', 'idUser=' . $id) !== FALSE;
  }
}




?>