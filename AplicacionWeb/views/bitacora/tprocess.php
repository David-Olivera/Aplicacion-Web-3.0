<?php
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
	use yii\widgets\CActiveRecord;
	use yii\helpers\Html;
	use yii\data\Pagination;
	use yii\widgets\LinkPager;
	use app\models\productos;
	use kartik\date\DatePicker;

	$this->title = "Tabla de Query's";
?>
<link href="../@web/images/fonts.css" rel="stylesheet">
<section class="form-inline">

	
	<div >

	<?php 
		// buscador //
		$p = ActiveForm::begin([
		"method" => "get",
		"action" => Url::toRoute("bitacora/tprocess"),
		"enableClientValidation" => true,
	]);
	?>
	<?= $p->field($formf, 'date')->widget(DatePicker::classname(), [
	       'name' => 'date', 
		'value' => date('yyyy-mm-dd'),
		'options' => ['placeholder' => 'Select the date...'],
		'pluginOptions' => [
			'format' => 'yyyy-mm-dd',
			'todayHighlight' => true
	        ]
	    ]);?>
	<?= Html::submitButton("Search",["class" => "btn btn-primary" ])?>

	<a id="todos" class="btn  btn-success" href="<?= Url::toRoute(["tprocess"])?>">See All</a>
	<?php $p->end() ?>

	</div>
</section>
    <style type="text/css">
      #query{
        width: 45%;
        color: #337ab7;
      }
      #tiempo{
      	width: 8%;
        color: #337ab7;
      }
      #fecha{

      	width: 12%;
        color: #337ab7;
      }
    </style>
<h3><?= $msg ?></h3>
<table  class="table table-bodered"  >
	<tr bgcolor="#FFFFFF">
		<th style="color:#337ab7">CPU</th>
		<th style="color:#337ab7">Hostname</th>
		<th style="color:#337ab7">DataBase</th>
		<th style="color:#337ab7">Status</th>
		<th style="color:#337ab7">Memory</th>
		<th id="query">Query</th>
		<th id="tiempo">Execution time</th>
		<th id="fecha">Last Time</th>
		<th style="color:#337ab7">Options</th>
	</tr>
	<?php foreach($model as $row):
		// tomamos y asignamos los datos por cada columna // 
	 	?>
		<tr  id="colum" >
		<td ><?=$row->cpu?></td>
		<td ><?=$row->hostname?></td>
		<td ><?=$row->databasename?></td>
		<td ><?=$row->status?></td>
		<td ><?=$row->usedmemory?></td>
		<td ><?=substr(utf8_encode($row->sqlcommand),0,90)?>...</td>
		<td ><?=$row->wait?></td>
		<td ><?=$row->FechaQ?></td>
		<th><a href="<?= Url::toRoute(["vistareport","IdQuery" => $row['IdQuery']])?>" ><span title="Detalles" class="glyphicon glyphicon-eye-open"></span></a>    /   
		<a  href="<?= Url::toRoute(["ocurrencias/tocurrencias","IdQuery" => $row['IdQuery']])?>"><span class="glyphicon glyphicon-warning-sign" title="Ocurrencias"></span></a></th>

	</tr>

       
	<?php 
	endforeach ?>
</table>
		
  <?=LinkPager::widget([
	"pagination" => $pages,
	]);
?>

