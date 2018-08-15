<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\User;
use app\models\Users;
use app\models\UsersConec;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\widgets\ActiveForm;
use app\models\FormRegister;
use yii\web\Session;
use app\models\FormRecoverPass;
use app\models\FormResetPass;
use SoapClient;


class SiteController extends Controller
{   
    public function actionUser()
    {
        return $this->render("index");
    }
public function behaviors()
{
  if (isset($_SESSION['user_id'])) {
    
      return [
          'access' => [
              'class' => AccessControl::className(),
              'only' => ['logout','tprocess','vistareport','reportocu','tocurrencias'],
              'rules' => [
                  [
                      'actions' => ['login','tprocess','vistareport','reportocu','tocurrencias'],
                      'allow' => true,
                      'roles' => ['?'],
                  ],
                  [
                      'actions' => ['logout','index','tprocess'],
                      'allow' => true,
                      'roles' => ['@'],
                  ],
                  [
                      'actions' => ['about'],
                      'allow' => true,
                      'roles' => ['@'],
                      'matchCallback' => function ($rule, $action) {
                          return Users::isUser(Yii::$app->user->identity->username);
                      }
                  ],
              ],
          ],
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'logout' => ['post'],
              ],
          ],
      ];
  }
  else
  {
      return [
          'access' => [
              'class' => AccessControl::className(),
              'only' => ['logout','tprocess','vistareport','tocurrencias'],
              'rules' => [
                  [
                      'actions' => ['login'],
                      'allow' => true,
                      'roles' => ['?'],
                  ],
                  [
                      'actions' => ['logout','index'],
                      'allow' => true,
                      'roles' => ['@'],
                  ],
                  [
                      'actions' => ['about'],
                      'allow' => true,
                      'roles' => ['@'],
                      'matchCallback' => function ($rule, $action) {
                          return Users::isUser(Yii::$app->user->identity->username);
                      }
                  ],
              ],
          ],
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'logout' => ['post'],
              ],
          ],
      ];  
  }
}
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
   public function actionLogin()
    {
      if (isset($_SESSION['user_id'])) 
      {
          return $this->redirect(['bitacora/tprocess']);
        }
      else
      {
              $model = new LoginForm();
              $session = Yii::$app->session;
              $session->close();
              if ($model->load(Yii::$app->request->post()) && $this->actionValidarlogin($model->username, $model->password))
              {                    
                              $form = Yii::$app->request->post('LoginForm');
                              $info = json_decode($model->username, $model->password);
                              $username = $form['username'];
                              $session->open();
                              $session->set('user_id',$username);
                           return $this->redirect(['bitacora/tprocess']);
              }
              else
              {
              return $this->render('login',[
                  'model' => $model,
              ]);
         }
      }
    }
       
    /**
     * Logs out the current user.
     *
     * @return mixed
     */


       public function actionSalir()
    {

        $session = Yii::$app->session;
        $session->destroy();

        
        return $this->redirect('index');
    }
    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
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
