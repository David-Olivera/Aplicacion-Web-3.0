<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\queryConec;
use app\models\comentariosConecs;
use app\models\FormSearchfecha;
use app\models\UsersConec;
use yii\data\Pagination;
use yii\helpers\Html;
use app\models\User;
use app\models\Users;
use app\models\EntryForm;
use app\models\ReporteForm;
use yii\helpers\Url;

class BitacoraController extends Controller
{

///////////FUNCION DE LA TABLA DE COONTIENE LOS QUERYS ///////////
	public function actionTprocess()
	{
      $formf = new FormSearchfecha;
		$search = null;
    $date = null;
		$msg = null;
///////////if en caso del que usario use el buscador recupera el dato ingresado por get ///////////
	if($formf->load(Yii::$app->request->get()))
	{
	       if($formf->validate())
		      {
	        $date = Html::encode($formf->date);
	        $table = queryConec::find()
	              -> where([">=","FechaQ","".$date." 00:00:00"])
                -> andWhere(["<=","FechaQ","".$date." 23:59:59"]);  
	        $table->orderBy([
	    'query.FechaQ' => SORT_DESC,
	      ]);
	        $count = clone $table;
	        $pages = new Pagination([
	          "pageSize" => 20,
	          "totalCount" => $count ->count(),
	        ]);
	        $model = $table
	        ->offset($pages->offset)
	        ->limit($pages->limit)
	        ->all();
	      	}
	      	else
			{ 
	        ///// si no se encuentra nada en la busqueda se mostrara todos los registros de manera normal /////
		      $table= queryConec::find();
		    	 $table->orderBy([
		    	'query.FechaQ' => SORT_DESC,
		      	]);

		      $count = clone $table;

		      $pages = new Pagination([
		          "pageSize" => 4,
		          "totalCount" => $count ->count(),
		      ]);
		      $model = $table
		        ->offset($pages->offset)
		        ->limit($pages->limit)
		        ->all();
			}
	}
	else
	{
      /////////  En caso de que el usuario no haga una busqueda se mostrara la tabla de manera normal //////////
	$table= queryConec::find();
	 $table->orderBy([
    'query.FechaQ' => SORT_DESC,
      ]);

    	$count = clone $table;

			$pages = new Pagination([
					"pageSize" => 20,
					"totalCount" => $count ->count(),
			]);
			$model = $table
				->offset($pages->offset)
				->limit($pages->limit)
				->all();
		}

		 return $this->render("tprocess",["model" => $model,"formf"=>$formf,"date"=>$date,"search" => $search, "pages" => $pages, "msg" => $msg]);
	}


////FUNCION DEL BOTON DETALLES DE LA TABLA DE COONTIENE LOS QUERYS PARA VIZUALIZAR LOS DATOS DE UN REGISTRO EN UN FORMULARIO/////
	public function actionVistareport()
	{
		$model = new ReporteForm;
		$msg = null;
		//// RECUPERAMOS EL ID DEL QUERY SELECCIONADO ////
		if(Yii::$app->request->get("IdQuery"))
		{
			$IdQuery = Html::encode($_GET["IdQuery"]);
      //// VALIDA QUE EL ID SEA DE TIPO STRING ////
			if((string) $IdQuery)
			{
        /// REALIZA LA CONEXION ///
				$table = queryConec::findOne($IdQuery);
				if($table)
				{
        //MODELO -> NOMBRE DE LOS INPUT = CONEXION -> COLUMNA A ASIGNAR//
					$model->IdQuery = $table->IdQuery;
					$model->C_P_U = $table->cpu;
					$model->hostname = $table->hostname;
					$model->dataBase = $table->databasename;
					$model->status = $table->status;
					$model->memory = $table->usedmemory;
					$model->query = $table->sqlcommand;
          $model->step_of_time = $table->wait;
          $model->Date_Ocurrence = $table->FechaQ;
				}
			}
		}
		
		return $this ->render("vistareport",["model" => $model, "msg" => $msg]);                	

}
/////////// PERMISOS PARA TIPOS DE USUARIO ///////////
public function behaviors()
{
  if (isset($_SESSION['user_id'])) {
    
      return [
          'access' => [
              'class' => AccessControl::className(),
              'only' => ['logout','tprocess','vistareport','reportocu', 'tocurrencias'],
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