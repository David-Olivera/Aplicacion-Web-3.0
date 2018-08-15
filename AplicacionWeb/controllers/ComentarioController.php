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
use yii\data\Pagination;
use yii\helpers\Html;
use app\models\User;
use app\models\Users;
use app\models\EntryForm;
use app\models\ReporteForm;
use yii\helpers\Url;

class ComentarioController extends Controller
{
    ///// FUNCION DE ENVIAR EL COMENTARIO /////
   public function actionEnviar()
    {
        $model = new ReporteForm;
        $msg = null;
    //// RECUPERAMOS EL ID DEL REGISTRO SELECCIONADO ////
        if(Yii::$app->request->get("IdQuery"))
        {
            $IdQuery = Html::encode($_GET["IdQuery"]);
            if((string) $IdQuery)
            {
                $table = queryConec::findOne($IdQuery);
                if($table)
                {
                    $model->IdQuery = $table->IdQuery;
                }
            }
        }
        //// RECUPERAMOS EL COMENTARIO POR POST ////
        if($model->load(Yii::$app->request->post()))
        {
            if($model->validate())
            {
                // REALIZACMOS CONEXION //
                $tables = new comentariosConecs;
                if($tables)
                {
           			/// INSERTAMOS EL ID DEL REGISTRO RECUPERADO, EL COMENTARIO, Y LA FECHA EN LA QUE SE HIZO EL COMENTARIO ///
                    $tables->IdQuery = $model->IdQuery;
                    $tables->Comentarios = $model->Comentarios;
					$tables->FechaC=  new \ yii \ db \ Expression ( 'NOW()' );
                    $tables->IdUser =  Yii::$app->session->get('user_id'); 
                    if ($tables->insert())
                    {
                        // VACIAMOS EL TEXTAREA DEL COMENTARIO //
	                    $model->Comentarios = null;
                        //echo "Comentario agregado correctamente";
                        //header("Location: http://google.com");
                    	}
                    else
                    {
                        $msg = "El Comentario no a podido ser agregado";
                    }
                }
                else
                {
                    $msg = "El Comentario no a podido ser agregado";
                }
            }
            else
            {
                $model->getErrors();
            }
         }    
         /// REDIRECCIONAMOS AL REPORTE CON EL ID DEL REGISTRO ACTUAL ///
        return $this ->redirect('index.php?r=bitacora%2Fvistareport&IdQuery='.$model->IdQuery.'');                 
    }
}