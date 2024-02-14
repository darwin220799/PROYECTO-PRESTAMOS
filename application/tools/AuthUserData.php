<?php

include(APPPATH."/controllers/User.php");

class AuthUserData
{

    private static function getData($data){
        $propertie = null;
        if(isset($_SESSION[$data])){
            if($_SESSION[$data]!=null){
              $propertie = $_SESSION[$data];
            }
          }else{
            try {
                session_start();
                $propertie = $_SESSION[$data];
            } catch (Exception $e) {
                echo $e->getMessage().'\n';
            }
          }
        return $propertie;
    }

    public static function getId(){
        if(AuthUserData::getData('user_id') != null){
            return AuthUserData::getData('user_id');
        }else{
            $sesion = new User();
            $sesion->logout();
        }
    }

    public static function getFullName(){
        if(AuthUserData::getData('full_name') != null){
            return AuthUserData::getData('full_name');
        }else{
            $sesion = new User();
            $sesion->logout();
        }
    }

    public static function isAuthor($probable_author_id){
        if($probable_author_id != null){
            return $probable_author_id == AuthUserData::getData('user_id');
        }else{
            return FALSE;
        }
    }

    public static function isAuthorX($model, $model_id){
        $query = $model->getAuthorId($model_id);
        $id = NULL;
        if($query != NULL){
            $id = $model->getAuthorId($model_id)->user_id;
        }
        if($id != NULL){
            return $id == AuthUserData::getData('user_id');
        }else{
            return FALSE;
        }
    }

}
