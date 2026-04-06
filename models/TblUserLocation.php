<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_user_location".
 *
 * @property int $id
 * @property int $fk_user_id
 * @property int $fk_location_id
 * @property string $ip
 * @property int $status
 * @property int $crt_by
 * @property string $crt_time
 * @property int $mod_by
 * @property string $mod_time
 */
class TblUserLocation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_user_location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_user_id', 'fk_location_id'], 'required'],
            [['fk_user_id', 'fk_location_id', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
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
            'fk_user_id' => 'Fk User ID',
            'fk_location_id' => 'Fk Location ID',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'crt_time' => 'Crt Time',
            'mod_by' => 'Mod By',
            'mod_time' => 'Mod Time',
        ];
    }

}
