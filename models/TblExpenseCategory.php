<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_expense_category".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property string $crt_time
 * @property int|null $mod_by
 * @property string|null $mod_time
 */
class TblExpenseCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_expense_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 40],
        ];
    }
    public function beforeSave($insert) {
      $session = Yii::$app -> session;
      if ($insert) {
        $this -> ip = Yii::$app -> getRequest() -> getUserIp();
        $this -> crt_by = $session -> get('userId');
        $this -> crt_time = date('Y-m-d H:i:s');
        $this -> status = 1;
      } else {
        $this -> ip = Yii::$app -> getRequest() -> getUserIp();
        $this -> mod_by = $session -> get('userId');
        $this -> mod_time = date('Y-m-d H:i:s');
      }
      return parent::beforeSave($insert);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'crt_time' => 'Crt Time',
            'mod_by' => 'Mod By',
            'mod_time' => 'Mod Time',
        ];
    }
}
