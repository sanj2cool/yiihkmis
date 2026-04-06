<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_estimate_item".
 *
 * @property int $id
 * @property int $fk_estimate_id
 * @property int|null $type 1- item, 2- product
 * @property int|null $fk_product_id
 * @property int|null $tier_id
 * @property string|null $item
 * @property string|null $description
 * @property float $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property int|null $status
 * @property string|null $ip
 * @property string $crt_time
 * @property int $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblEstimateItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_estimate_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_estimate_id', 'quantity', 'unit_price', 'total_price'], 'required'],
            [['fk_estimate_id', 'type', 'fk_product_id', 'tier_id', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['quantity', 'unit_price', 'total_price'], 'number'],
            [['crt_time', 'mod_time'], 'safe'],
            [['item','description'], 'string', 'max' => 1000],
            [['ip'], 'string', 'max' => 20],
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
            'fk_estimate_id' => 'Fk Estimate ID',
            'type' => 'Type',
            'fk_product_id' => 'Fk Product ID',
            'tier_id' => 'Tier ID',
            'item' => 'Item',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'total_price' => 'Total Price',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
}
