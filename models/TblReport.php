<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_report".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $parent_id
 * @property string $url
 * @property int $is_favourite
 * @property int $priority
 * @property int $status
 * @property string $ip
 * @property int $crt_by
 * @property string $crt_time
 * @property int $mod_by
 * @property string $mod_time
 */
class TblReport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'parent_id', 'url'], 'required'],
            [['description'], 'string'],
            [['parent_id', 'is_favourite', 'priority', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title', 'url'], 'string', 'max' => 100],
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
            'title' => 'Title',
            'description' => 'Description',
            'parent_id' => 'Parent ID',
            'url' => 'Url',
            'is_favourite' => 'Is Favourite',
            'priority' => 'Priority',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_by' => 'Crt By',
            'crt_time' => 'Crt Time',
            'mod_by' => 'Mod By',
            'mod_time' => 'Mod Time',
        ];
    }
}
