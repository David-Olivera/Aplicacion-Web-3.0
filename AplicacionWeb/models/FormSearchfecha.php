<?php
	namespace app\models;
	use Yii;
	use yii\base\Model;
	
	class FormSearchfecha extends Model
	{
		public $date;
		
		public function rules()
		{
			return[
								
				[['date'], 'date', 'format' => 'yyyy-mm-dd']
			];
		}
		public function attributeLabels()
		{
			return[
			'date' => "Search :  ",
			];
		}
	}
?>