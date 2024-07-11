<?php
class UsrModelAbout{

    static $lastError='';
    
    /**
     * actualizar usuarios
     */
    public static function add($reg){
     $idUser=AifDb::insert($reg,'about');
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
        AifDb::update('about', $data, 'idAbout=' . $id);
        return $id;
      }
      
      public static function getById($id = 1)
      {
        return AifDb::firstObject(
          'SELECT u.*,a.*,cs.title as gradoEstudio
          FROM about a
          INNER JOIN cat_study cs ON a.idStudy=cs.idStudy
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
        return AifDb::update('about', 'deleted=1', 'idAbout=' . $id) !== FALSE;
      }
    }

?>