<?php
class UsrModelExperience{

    static $lastError='';
    
    /**
     * actualizar usuarios
     */
    public static function add($reg){
     $idUser=AifDb::insert($reg,'experience');
     return $idUser;
    }
      /**
       * Actualiza datos del usuario
       *
       * @param ['user'] $data actualiza los datos del usuario almacenados en el controlador mediante el post.
       * @param ['idUser'] $idUser obtiene el id del usuario.
       * @return void
       */
      public static function upd($data, $id)
      {
        AifDb::update('experience', $data, 'idExperience=' . $id);
        return $id;
      }
      
      public static function getById($id = 1)
      {
        return AifDb::firstObject(
          'SELECT u.*,e.*,ce.title as experiencias
          FROM experience e
          INNER JOIN cat_experience ce ON e.category=ce.idCategory
          INNER JOIN user u ON u.idUser=a.idUser
          WHERE u.idUser = ' . $id
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
        return AifDb::update('experience', 'deleted=1', 'idExperience=' . $id) !== FALSE;
      }
    }

?>