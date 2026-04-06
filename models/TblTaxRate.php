<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tax_rate".
 *
 * @property int $id
 * @property float $tax_rate
 * @property string|null $province_state
 * @property string|null $description
 * @property int|null $status
 * @property string|null $ip
 * @property string $crt_time
 * @property int $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblTaxRate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_tax_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tax_rate'], 'required'],
            [['tax_rate'], 'number'],
            [['status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['province_state'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 1500],
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
            'tax_rate' => 'Tax Rate',
            'province_state' => 'Province State',
            'description' => 'Description',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
}
