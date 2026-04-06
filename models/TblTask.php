<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_task".
 *
 * @property int $id
 * @property int|null $assigned_to
 * @property string $title
 * @property string|null $description
 * @property int|null $status_id
 * @property string|null $due_date
 * @property string|null $time
 * @property string $crt_time
 * @property int|null $crt_by
 * @property string $ip
 * @property int $status
 * @property string $mod_time
 * @property int|null $mod_by
 */
class TblTask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['assigned_to', 'status_id', 'crt_by', 'status', 'mod_by','fk_location_id'], 'integer'],
            [['title'], 'required'],
            [['description'], 'string'],
            [['due_date', 'crt_time', 'mod_time'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['time'], 'string', 'max' => 10],
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
            'assigned_to' => 'Assigned To',
            'title' => 'Title',
            'description' => 'Description',
            'status_id' => 'Status ID',
            'due_date' => 'Due Date',
            'time' => 'Time',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'ip' => 'Ip',
            'status' => 'Status',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
}
