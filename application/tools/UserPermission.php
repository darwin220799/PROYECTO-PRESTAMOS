<?php
include(APPPATH."/tools/AuthUserData.php");
include(APPPATH."/tools/Constants.php");

function loadErrorMessage($message){
    return "<script>alert('$message');
    window.history.back();
    </script>";
}

class Permission {

    private $model;
    private $user_id;
    
    public function __construct($model, $user_id){
        $this->model = $model;
        $this->user_id = $user_id;
    }

    public function getPermission($permissions, $redirect){
        $permit = FALSE;
        foreach($permissions as $permission){
            $permit = $permit || $this->model->getAuthorization($this->user_id, $permission);
        }
        return $this->denyAndRedirect($redirect, $permit);
    }

    public function getPermissionX($permissions, $redirect){
        $permit = $permissions==NULL? FALSE : (sizeof($permissions)>0? TRUE : FALSE);
        foreach($permissions as $permission){
            $permit = $permit && $this->model->getAuthorization($this->user_id, $permission);
        }
        return $this->denyAndRedirect($redirect, $permit);
    }

    public function redirectIfFalse($permission, $redirect){
        $permit = $permission;
        return $this->denyAndRedirect($redirect, $permit);
    }

    private function denyAndRedirect($redirect, $permit){
        if($redirect){
            if(!$permit){
                show_error("You don't have access to this site", 403, 'DENIED ACCESS');
            }else{
                return $permit;
            }
        }else{
            return $permit;
        }
    }
}

?>