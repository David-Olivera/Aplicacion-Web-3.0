<?php

namespace app\models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    /**
     * @inheritdoc
     */
    
    /* busca la identidad del usuario a través de su $id */

	    public $role;


	 public static function isUser($id)
     {
       if (isset($_SESSION['user_id'])){
        return true;
       } else {

        return false;
       }

    }
    public static function findIdentity($id)
    {

    $user_id =  Yii::$app->session->get('user_id'); 

    return isset($user_id) ? new static($user_id) : null;
    }

    /**
     * @inheritdoc
     */
    
    /* Busca la identidad del usuario a través de su token de acceso */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /* Regresa la clave de autenticación */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    
    /* Valida la clave de autenticación */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        /* Valida el password */
        if (crypt($password, $this->password) == $this->password)
        {
        return $password === $password;
        }
    }

    public function ObtenerUsuarioValido($username, $password)
    {
         $client = new SoapClient('http://140.50.34.128/sisturws/index.php?r=autenticacion/Autenticacion/ServiceInterface');
         try{
         $session = $client->actionVerificarlogin($username, $password);
         if($session==null){
            return null;
         }
         $client = new User();
         $client->username = $session["username"];
         $client->password = $session["password"];

             return $client;
        }   catch(SoapFault $fault){
            $this->addError($atributte, 'Password o Username Incorrecto');
        }
        return null;
    }
    
}