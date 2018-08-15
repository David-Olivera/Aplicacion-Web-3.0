<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Users;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
     public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'required'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    /*
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }
*/
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Users::findByUsername($this->username);
        }

        return $this->_user;
    }
/*
    public function validacion_informacion($username, $password){
        $Usuario = $username;
        $Clave = $password;
        $Empresa = "palace-resorts";
        $Dominio = "local";
        $ldaptree = "DC=palace-resorts,DC=local";
        $dn = 'dc=$Empresa,dc=$Dominio';

        $ldapconn = ldap_connect("$Empresa.$Dominio",389) or die("Could not connect to LDAP.");
        if ($ldapconn) {
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapconn, LDAP_OPT_PREFERRALS, 0);
            $ldapbind = @ldap_bind($ldapconn, "$Usuario@$Empresa.$Dominio", $nClave);
            if ($ldapconn) {
                $attributes = array("disolayname", "mail","sAMAccountName","title","Description");
                $filter = "(&(objectCategory=person)(sAMAccountName= $username))";
                $result = ldap_search($ldapconn, $ldaptree, $filter,$attributes);
                $entries = ldap_get_entries($ldapconn, $result);
                $arrayN = entries[0];
                $infoAll = array();
                $resultado =intval(preg_replace('/[^0-9]+/', '',$arrayN['description'][0]),10);
                array_push($infoAll, $resultado);
                array_push($infoAll, $arrayN['mail'][0]);
                return json_encode($infoAll);
            }else{
                echo "<script> alert('Usuario o Contraseña incorrectos'); </script>";
            }
        }
        ldap_close($ldapconn);
    }*/
}
