<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_estimate_sent_log".
 *
 * @property int $id
 * @property int $fk_estimate_id
 * @property string|null $message_id
 * @property int|null $status
 * @property string|null $ip
 * @property string $crt_time
 * @property int $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblEstimateSentLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_estimate_sent_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_estimate_id'], 'required'],
            [['fk_estimate_id', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['message_id'], 'string', 'max' => 100],
            [['ip'], 'string', 'max' => 20],
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
            'fk_estimate_id' => 'Fk Estimate ID',
            'message_id' => 'Message ID',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
}
