<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_role_master".
 *
 * @property int $id
 * @property string $title
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $crt_time
 * @property string $mod_by
 * @property string $mod_time
 */
class TblRoleMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_role_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['status'], 'integer'],
            [['title', 'crt_time'], 'string', 'max' => 100],
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
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'crt_time' => 'Crt Time',
            'mod_by' => 'Mod By',
            'mod_time' => 'Mod Time',
        ];
    }
}
