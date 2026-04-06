<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_product_allotment".
 *
 * @property int $id
 * @property int $fk_product_id
 * @property int $fk_vendor_invoice_id
 * @property int $no_of_items
 * @property string $allotment_date
 * @property int $allotted_by
 * @property int $fk_allotment_status_id
 * @property string|null $remarks
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property int|null $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblProductAllotment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_product_allotment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_product_id', 'no_of_items', 'allotment_date', 'allotted_by', 'fk_allotment_status_id'], 'required'],
            [['fk_product_id', 'fk_vendor_invoice_id', 'no_of_items', 'allotted_by', 'fk_allotment_status_id', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['allotment_date', 'crt_time', 'mod_time'], 'safe'],
            [['remarks'], 'string'],
            [['ip'], 'string', 'max' => 40],
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
            'fk_product_id' => 'Item',
            // 'fk_vendor_invoice_id' => 'Invoice',
            'no_of_items' => 'Qty. Allotted',
            'allotment_date' => 'Allotment Date',
            'allotted_by' => 'Allotted By',
            'fk_allotment_status_id' => 'Allotment Status',
            'remarks' => 'Remarks',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
}
