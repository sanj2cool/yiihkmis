<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_client_login".
 *
 * @property int $id
 * @property int $fk_client_id
 * @property string $username
 * @property string $password
 * @property int|null $status
 * @property string|null $ip
 * @property string|null $crt_time
 * @property int|null $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblClientLogin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_client_login';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_client_id', 'username', 'password'], 'required'],
            [['fk_client_id', 'status', 'crt_by', 'mod_by','fk_location_id'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['username', 'password'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 20],
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
            'fk_client_id' => 'Fk Client ID',
            'username' => 'Username',
            'password' => 'Password',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
}
