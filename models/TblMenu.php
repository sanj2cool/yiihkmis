<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_menu".
 *
 * @property int $id
 * @property string $title
 * @property int $parent_id
 * @property string $url
 * @property string $class
 * @property int $priority
 * @property int $status
 * @property string $ip
 * @property int $crt_by
 * @property string $crt_time
 * @property int $mod_by
 * @property string $mod_time
 */
class TblMenu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'parent_id', 'url', 'class'], 'required'],
            [['parent_id', 'priority', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 75],
            [['class', 'ip'], 'string', 'max' => 30],
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
            'parent_id' => 'Parent ID',
            'url' => 'Url',
            'class' => 'Class',
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
