<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_bin_bay".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblBinBay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_bin_bay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description'], 'required'],
            [['status'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title', 'description'], 'string', 'max' => 100],
            [['ip', 'crt_by'], 'string', 'max' => 40],
            [['mod_by'], 'string', 'max' => 50],
        ];
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
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
}
