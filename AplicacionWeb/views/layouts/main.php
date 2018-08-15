<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\models\User;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\CActiveRecord;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <link rel="icon" type="image/png" href="/images/logo1.png" />
    <title><?= Html::encode($this->title = 'Palace Resorts') ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
  NavBar::begin([
  'brandLabel' => Html::img('@web/images/logo1.png' , [  'width' => '90px', 'style'=>'position:absolute;top:0px;left:100px','title'=>"Palace Resorts"  
            ]) ,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-default navbar-fixed-top',
        ],
    ]);
 //RERCUPERA EL ID 
    $user_id =  Yii::$app->session->get('user_id'); 
// VALIDACION DE SI TIENE UN VALOR
  if (isset($_SESSION['user_id'])) {

        $Items[]= ['label' => 'Bitacora', 'url' => ['/bitacora/tprocess']];
           $Items[]= ['label' => 'Logout (' . $user_id . ')', 'url' => ['/site/salir'], 'post'];
    }
    else
    {

         $Items=[['label' => ' ', 'url' => [' ']]];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right','id'=>'opciones'],
        'items' => $Items,
    ]);
    NavBar::end();
    ?>
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
