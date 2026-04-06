<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_preferred_payment_method".
 *
 * @property int $id
 * @property string $title
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property int|null $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblPreferredPaymentMethod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_preferred_payment_method';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'crt_by'], 'required'],
            [['status', 'crt_by', 'mod_by'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title'], 'string', 'max' => 100],
            [['ip'], 'string', 'max' => 40],
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
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
}
