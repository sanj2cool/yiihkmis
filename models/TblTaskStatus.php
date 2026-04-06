<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_task_status".
 *
 * @property int $id
 * @property string $status_name
 * @property string|null $icon
 * @property string|null $class
 * @property string $crt_time
 * @property int|null $crt_by
 * @property string $ip
 * @property int $status
 * @property string $mod_time
 * @property int|null $mod_by
 */
class TblTaskStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_task_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_name'], 'required'],
            [['crt_time', 'mod_time'], 'safe'],
            [['crt_by', 'status', 'mod_by'], 'integer'],
            [['status_name', 'class'], 'string', 'max' => 50],
            [['icon'], 'string', 'max' => 100],
            [['ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_name' => 'Status Name',
            'icon' => 'Icon',
            'class' => 'Class',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'ip' => 'Ip',
            'status' => 'Status',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
}
