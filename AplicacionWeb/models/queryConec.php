<?php
	
	namespace app\models;
	use Yii;
	use yii\db\ActiveRecord;
	
	class queryConec extends ActiveRecord{
		public static function getDb()
		{

			return Yii::$app->db;
			return Yii::$app->Query;
		}
		public static function tableName()
		{
			
			return 'query';
		}
	}
?>