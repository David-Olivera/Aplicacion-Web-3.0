<?php
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
	use yii\widgets\CActiveRecord;
	use yii\helpers\Html;
	use yii\data\Pagination;
	use yii\widgets\LinkPager;
	use app\models\productos;
	use app\models\comentariosConecs;
	use yii\helpers\Arrayhelper;

	$this->title = 'Formulario de Reporte';
	?>
		<script type="text/javascript">
		function CopyToClipboard(containerid) {
			if (document.selection) { 
			    var range = document.body.createTextRange();
			    range.moveToElementText(document.getElementById(containerid));
			    range.select().createTextRange();
			    document.execCommand("copy"); 

			} else if (window.getSelection) {
			    var range = document.createRange();
			     range.selectNode(document.getElementById(containerid));
			     window.getSelection().addRange(range);
			     document.execCommand("copy");
			     alert("Text Copied") 
			}
		}	
		</script>
	   <style>
        .container-fluid {
        	height: 400px;
          overflow: scroll;	
        }
        #reporte{
        	width: 90%;
        	margin-left: 5%;
        }
      </style>
	<div >
		<div >
			<div id="reporte" class="panel-body">
				<?php $form = ActiveForm::begin([
					"method" => "post",
					// accion del formulario ("controlador/funcion")//
					"action" => Url::toRoute("ocurrencia/reportocu"),
					'enableClientValidation' => true,
				]);
				?>
				<h2>Report of Occurrence</h2>
				
				<?= $form->field($model, "IdQuery")->input("hidden")->label(false) ?>

				<div class="col-md-3" class ="form-group">

				<?= $form->field($model, "C_P_U")->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>	
				<div class="col-md-3" class ="form-group">
					<?= $form->field($model, 'step_of_time')->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>
				<div class="col-md-3"  class ="form-group">
					<?= $form->field($model, 'memory')->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>
				<div class="col-md-3"  class ="form-group">
					
					<?= $form->field($model, 'Date_Ocurrence')->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>

				<div id="div1" class ="form-group">
					<?= $form->field($model, 'query')->textArea(['class' => 'form-control class-content-proceso','rows'=> '10', 'disabled' => 'disabled']) ?>
				</div>

				<button id="button1" class="btn btn-primary" onclick="CopyToClipboard('div1')">Click to copy query</button>
			

			</div>
		</div>
	</div>