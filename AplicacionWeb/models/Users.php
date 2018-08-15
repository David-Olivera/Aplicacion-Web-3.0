<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\models\UsersConec;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $role
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class Users extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 30;

    const ROLE_USER = 10;
    const ROLE_ADMIN = 20;
    const ROLE_SUPERUSER = 30;


    /**
     * {@inheritdoc}
     */
  
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['username', 'default', 'value' => 20],
            ['role', 'default', 'value' => 20],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'value' => [self::STATUS_ACTIVE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function isUser($username)
    {
        $username =  Yii::$app->session->get('user_id'); 
          if (isset($username)) {
            return true;
        }else{
            return false;
        }
    }
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
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

    public static function roleInArray($arr_role)
    {
        $arr_role = 20;
    return $arr_role;
    }
    public static function isActive()
    { 
        $user_id =  Yii::$app->session->get('user_id'); 
        if (isset($user_id)){    
            return Yii::$app->user->identity->status == self::STATUS_ACTIVE;
        }else{
            return false;   
        }
    }
     public function actionValidarlogin($username, $password)
    {
        try{
            $client = new SoapClient('http://140.50.34.128/sisturws/index.php?r=autenticacion/autenticacion/ServiceInterface',
            ['soap_version' => SOAP_1_2,
                'exceptions' => true,
                'trace' =>1,
                'cache_wsdl' => WSDL_CACHE_NONE]);

                $result = $client->ObtenerUsuarioValido(['usuario'=> $username,
                'password' => $password]);

                $result_requet = json_encode($result);

                return(boolean)json_decode($result_requet)->usuarioValido;
        }   catch(SoapFault $fault){

        }
    }
}
