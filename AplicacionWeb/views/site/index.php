<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
	use yii\widgets\ActiveForm;


?>
<div class="site-index">

    <div class="jumbotron">
        <h2>Welcome to the Query Administration and Monitoring System</h2>
   		<p class="lead">Please, Session Session to continue</p>
   		<br />
   		<img src="\images\monitoreo.png" title="Sistema de Administracion y Monitoreo de Querys">
   		<br/>
   		<br/>
   		<?php $mens = ActiveForm::begin([
		"method" => "post",
		"action" => Url::toRoute('site/login'),
		'enableClientValidation' => true,
		]);
		?>
				
			<?= Html::submitButton("Enter the System",["class" => "btn btn-success"])?>

		<?php $mens->end() ?>
				    </div>

</div>