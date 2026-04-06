<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_expense_file".
 *
 * @property int $id
 * @property int $fk_expense_id
 * @property string|null $file_upload
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property int|null $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblExpenseFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_expense_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_expense_id'], 'required'],
            [['fk_expense_id', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['file_upload'], 'string', 'max' => 255],
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
            'fk_expense_id' => 'Fk Expense ID',
            'file_upload' => 'File Upload',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
}
