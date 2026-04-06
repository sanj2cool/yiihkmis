<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_attendance".
 *
 * @property int $id
 * @property string $in_time
 * @property string|null $out_time
 * @property string $date
 * @property int $fk_user_id
 * @property int $att_status 1- office 2- work from home 3- day off
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $crt_time
 * @property string $mod_by
 * @property string $mod_time
 */
class TblAttendance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_attendance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['in_time', 'date', 'fk_user_id'], 'required'],
            [['fk_user_id', 'att_status', 'status','fk_location_id'], 'integer'],
            [['in_time', 'out_time', 'date', 'crt_time'], 'string', 'max' => 100],
            [['ip', 'crt_by'], 'string', 'max' => 40],
            [['mod_by'], 'string', 'max' => 50],
            [['mod_time'], 'string', 'max' => 20],
        ];
    }
    public function beforeSave($insert) {
        $session = Yii::$app -> session;
        if ($insert) {
          $this -> ip = Yii::$app -> getRequest() -> getUserIp();
          $this -> crt_by = $session -> get('userId');
          $this->fk_location_id = $session->get('userCompany');
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
            'in_time' => 'In Time',
            'out_time' => 'Out Time',
            'date' => 'Date',
            'fk_user_id' => 'User',
            'att_status' => 'Att Status',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'crt_time' => 'Crt Time',
            'mod_by' => 'Mod By',
            'mod_time' => 'Mod Time',
        ];
    }
}
