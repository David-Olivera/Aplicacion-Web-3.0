<?php
namespace app\models;

use yii\base\Model;

class ReporteFormocu extends Model
{
		public $IdQuery;
		public $C_P_U;
		public $query;
		public $step_of_time;
		public $memory;
		public $Date_Ocurrence;
	public function rules()
	{
		return[
			['IdQuery','string']
		];
	}
}
?>