<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_userlog".
 *
 * @property int $id
 * @property string $username
 * @property string $url
 * @property int $login_success_status
 * @property int $status
 * @property string $ip
 * @property int $crt_by
 * @property string $crt_time
 * @property int $mod_by
 * @property string $mod_time
 */
class TblUserlog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_userlog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'url', 'login_success_status'], 'required'],
            [['login_success_status', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['username', 'url'], 'string', 'max' => 100],
            [['ip'], 'string', 'max' => 30],
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
            'username' => 'Username',
            'url' => 'Url',
            'login_success_status' => 'Login Success Status',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_by' => 'Crt By',
            'crt_time' => 'Crt Time',
            'mod_by' => 'Mod By',
            'mod_time' => 'Mod Time',
        ];
    }
}
