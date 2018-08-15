<?php
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
	use yii\widgets\CActiveRecord;
	use yii\helpers\Html;
	use yii\data\Pagination;
	use yii\widgets\LinkPager;
	use app\models\productos;
	
	$this->title = "Tabla de Query's";
?>
<br/>
 <style type="text/css">
      #query{
        width: 65%;
        color: #337ab7;
      }
      #tiem-memo{
      	width: 8%;
        color: #337ab7;
      }
      #opciones{
      	text-align: center;
        color: #337ab7;
      }
    </style>
<table class="table table-bodered">
	<tr bgcolor="#FFFFFF">
		<th style="color:#337ab7">CPU</th>
		<th id="query">Query</th>
		<th id="tiem-memo">Execution time</th>
		<th id="tiem-memo">Memory used</th>
		<th style="color:#337ab7">Last Time</th>
		<th id="opciones">Options</th>
	</tr>
	<?php foreach($model as $row): ?>
		<tr id="colum" >
		<td ><?=$row->cpu?></td>
		<td ><?=substr(utf8_encode($row->sqlcommand),0,135)?>...</td>
		<td ><?=$row->wait?></td>
		<td ><?=$row->usedmemory?></td>
		<td ><?=$row->FechaO?></td>
		<th style="text-align: center"><a href="<?= Url::toRoute(["reportocu","IdOcurrencia" => $row['IdOcurrencia']])?>" ><span title="Detalles" class="glyphicon glyphicon-eye-open"></span></a></th>

	</tr>

       
	<?php endforeach ?>
	
</table>
  <?=LinkPager::widget([
	"pagination" => $pages,
	]);
?>
	
	<?php $form = ActiveForm::begin([
		"method" => "post",
		"action" => Url::toRoute("ocurrencias/tocurrencias"),
		'enableClientValidation' => true,
	]);
		foreach($segundos as $fila){
			echo'
			<h4><strong>Tiempo Total:</strong> '.$fila.'  /  <strong>Memoria Total:</strong> '.$memoria.'<h4>
			<br>';	

		}
	?>

	<?php $form->end() ?>
<!--  Html::submitButton("Generar PDF",array("class" => "pdf", "onclick" => "js:document.location.href=print/".$row->id.'"','id' => $row->id));  ES PHP-->
<!--  Html::submitButton("Generar PDF",array("class" => "pdf", "onclick" => "js:document.location.href=print/".$row->id.'"','id' => $row->id));  ES PHP-->


