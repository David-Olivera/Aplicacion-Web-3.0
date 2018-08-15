<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\ocurrenciaConec;
use app\models\FormOcurrenciasearch;
use yii\data\Pagination;
use yii\helpers\Html;
use app\models\User;
use app\models\Users;
use app\models\EntryForm;
use app\models\ReporteForm;
use app\models\ReporteFormocu;
use app\models\EntryFormCo;
use yii\helpers\Url;
use yii\db\Query;
use yii\helpers\Arrayhelper;

class OcurrenciasController extends Controller
{

///////////FUNCION DEL BOTON "Ocurrencias" CON LA TABLA QUE COONTIENE LAS OCURRENCIA POR CADA REGISTRO ///////////
	public function actionTocurrencias()
	{
    ///RECUPERAMOS ID DE QUERY ///
      if(Yii::$app->request->get("IdQuery"))
      {
      $IdQuery = Html::encode($_GET["IdQuery"]);
      if((string) $IdQuery)
      {
        //consulta para traer todas las ocurrencias del mismo query en base al id //
    $queryOcurrencias= ocurrenciaConec::find()
    ->where(["IdQuery" => $IdQuery]);
     $queryOcurrencias->orderBy([
    'ocurrencia.fechaO' => SORT_DESC,
      ]);

      $count = clone $queryOcurrencias;

      $pages = new Pagination([
          "pageSize" => 20,
          "totalCount" => $count ->count(),
      ]);
      $model = $queryOcurrencias
        ->offset($pages->offset)
        ->limit($pages->limit)
        ->all();
         $queryMemoria = ocurrenciaConec::find()->select('sum(usedmemory)')->where(['IdQuery' => $IdQuery])->asArray()->one();
                 $suma_result = array_sum($queryMemoria);

          $queryTime = ocurrenciaConec::find()
          ->select("sec_to_time(sum(wait))")
          ->where(['IdQuery' => $IdQuery])->asArray()->one();

        if ($queryMemoria) {
          // asignamos el valor al input
          $queryMemoria;
          //$memoria->Tiempo_Total_Segundos = $queryTime;
          }
        }
      }
     return $this->render("tocurrencias",["model" => $model,"memoria" => $suma_result, "pages" => $pages, 'segundos'=>$queryTime]);
  }

  public function actionReportocu()
  {
    $model = new ReporteFormocu;
    $msg = null;
    //// RECUPERAMOS EL ID DEL QUERY SELECCIONADO ////
    if(Yii::$app->request->get("IdOcurrencia"))
    {
      $IdQuery = Html::encode($_GET["IdOcurrencia"]);
      //// VALIDA QUE EL ID SEA DE TIPO STRING ////
      if((string) $IdQuery)
      {
        /// REALIZA LA CONEXION ///
        $table = ocurrenciaConec::findOne($IdQuery);
        if($table)
        {
        //MODELO -> NOMBRE DE LOS INPUT = CONEXION -> COLUMNA A ASIGNAR//
          $model->IdQuery = $table->IdQuery;
          $model->C_P_U = $table->cpu;
          $model->memory = $table->usedmemory;
          $model->query = $table->sqlcommand;
          $model->step_of_time = $table->wait;
          $model->Date_Ocurrence = $table->FechaO;
        }
      }
    }
    
    return $this ->render("reportocu",["model" => $model, "msg" => $msg]);                  
}
	 // permisos de tipos de  usuarios
public function behaviors()
{
  if (isset($_SESSION['user_id'])) {
    
      return [
          'access' => [
              'class' => AccessControl::className(),
              'only' => ['logout','tprocess','vistareport','reportocu','tocurrencias'],
              'rules' => [
                  [
                      'actions' => ['login','tprocess','vistareport','reportocu', 'tocurrencias'],
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
	
}