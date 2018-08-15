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
      </style>
	<div class="input-sm">
		<div  style="width: 50%"   class="panel panel-default col-md-7">
			<div class="panel-body">
				<h2>Report of Register</h2>
				<?php $form = ActiveForm::begin([
					"method" => "post",
					// accion del formulario ("controlador/funcion")//
					"action" => Url::toRoute("comentario/enviar"),
					'enableClientValidation' => true,
				]);
				?>
				<?= $form->field($model, "IdQuery")->input("hidden")->label(false) ?>

				<div class="col-md-3" class ="form-group">

				<?= $form->field($model, "C_P_U")->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>	
				<div class="col-md-3"  class ="form-group">
					<?= $form->field($model, "hostname")->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>		
				<div  class="col-md-3" class ="form-group">
					<?= $form->field($model, "status")->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled'])?>
				</div>
				<div class="col-md-3"  class ="form-group">
					<?= $form->field($model, 'memory')->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>
				<div class="col-md-6" class ="form-group">
					<?= $form->field($model, 'step_of_time')->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>
				<div   class="col-md-6" class ="form-group">
					<?= $form->field($model, "dataBase")->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled'])?>
				</div>	
	
				<div class ="form-group">
					
					<?= $form->field($model, 'Date_Ocurrence')->textInput(['class' => 'form-control class-content-proceso', 'disabled' => 'disabled']) ?>
				</div>

				<div id="div1" class ="form-group">
					<?= $form->field($model, 'query')->textArea(['class' => 'form-control class-content-proceso','rows'=> '10', 'disabled' => 'disabled']) ?>
				</div>
				<button id="button1" class="btn btn-primary" onclick="CopyToClipboard('div1')">Click to copy query</button>

			</div>
		</div>
		<div  style="width: 50%" class="panel panel-default col-md-7">
			<div class="content container-fluid" class="panel-body">
				<h2>Comments</h2><br>
				<div data-spy="scroll" data-target =".navbar" data-offset="2">
				<?php 
				// consulta con todos los comentarios relacionados con el id del query del registro seleccionado 
				$table2=Arrayhelper::index(comentariosConecs::find()->where(["IdQuery" => $model->IdQuery])->asArray()->all(),'IdComentario');
				foreach ($table2 as $comentario) {
				// imprimimnos todos los comentarios
					echo'
					  <div>
					  <p >Usuario: '.$comentario["IdUser"].'</p>
					  <p >Fecha: '.$comentario["FechaC"].'</p>
					  <p >Comentario: '.$comentario["Comentarios"].'</p>
					  </div><br/>';
				}
				 ?>          
				</div>

				</div>

					<?php $mens = ActiveForm::begin([
					"method" => "post",
					"action" => Url::toRoute("comentario/enviar"),
					'enableClientValidation' => true,
				]);
				?>
				<div class="form-group">
			 		
			 		<?= $mens->field($model, "Comments")->textArea(['value' => null]) ?>   
			 		
				</div>
					
				<?= Html::submitButton("Agregar",["class" => "btn btn-success"])?>
				<?php $mens->end() ?>

				<?php $form->end() ?>
			</div>
		</div>
	</div>