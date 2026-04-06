<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_bin".
 *
 * @property int $id
 * @property int $fk_area_id
 * @property int $fk_row_id
 * @property int $fk_bay_id
 * @property int $fk_level_id
 * @property int $fk_position_id
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblBin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_bin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_area_id', 'fk_row_id', 'fk_bay_id', 'fk_level_id', 'fk_position_id'], 'required'],
            [['fk_area_id', 'fk_row_id', 'fk_bay_id', 'fk_level_id', 'fk_position_id', 'status'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
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
            'fk_area_id' => 'Fk Area ID',
            'fk_row_id' => 'Fk Row ID',
            'fk_bay_id' => 'Fk Bay ID',
            'fk_level_id' => 'Fk Level ID',
            'fk_position_id' => 'Fk Position ID',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
}
