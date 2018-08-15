<?php
	namespace app\models;
	use Yii;
	use yii\base\model;
	
	class FormOcurrenciasearch extends model
	{
		public $search;
		
		public function rules()
		{
			return[
				
				["search","match","pattern" => "/^[0-9a-záéíóúñ:\s]+$/i","message" => "Solo se aceptan letras y numero"]
			];
		}
		public function attributeLabels()
		{
			return[
			'search' => "Buscar:",
			];
		}
	}
?>