<?php
namespace app\models;

use yii\base\Model;

class ReporteForm extends Model
{
		public $IdQuery;
		public $C_P_U;
		public $hostname;
		public $dataBase;
		public $status;
		public $memory;
		public $query;
		public $Comments;
		public $step_of_time;
		public $Date_Ocurrence;
		public $Memory_Total;	
	public function rules()
	{
		return[
		[['IdQuery','Comentarios'],'required', 'message' => 'No puedes enviar Comentarios vacios'],
		["Comentarios","match","pattern" => "/^[0-9a-záéíóúñ.:,\s]+$/i","message" => "Solo se aceptan letras y numero"],
			['IdQuery','string']
		];
	}
}
?>