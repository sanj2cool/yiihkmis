<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_product_receiving".
 *
 * @property int $id
 * @property int $fk_product_id
 * @property int $fk_vendor_invoice_id
 * @property int $no_of_items
 * @property int|null $fk_bin_id
 * @property string|null $remarks
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property int|null $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblProductReceiving extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_product_receiving';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_product_id', 'no_of_items','price_per_item'], 'required'],
            [['fk_product_id', 'fk_vendor_invoice_id','fk_vendor_id', 'no_of_items', 'fk_bin_id', 'status', 'crt_by', 'mod_by','fk_location_id','fk_vendor_invoice_item_id'], 'integer'],
            [['remarks'], 'string'],
            [['crt_time', 'mod_time','price_per_item'], 'safe'],
            [['ip'], 'string', 'max' => 40],
            [['bin_location'], 'string', 'max' => 100],
              [['shipping', 'other_charges', 'custom_charges'], 'number'],
            [['shipping', 'other_charges', 'custom_charges'], 'safe'],

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
            'fk_product_id' => 'Item',
            'product' => 'Product',
            'vendorinvoice' => 'Purchase Invoice',
            'bin_location' => 'Bin Location',
            'bin' => 'BIN(Area - Row - Bay - Level - Position)',
            'fk_vendor_invoice_id' => 'Vendor Invoice',
            'fk_vendor_id' => 'Vendor',
            'no_of_items' => 'No Of Items',
            'fk_bin_id' => 'Bin #',
            'remarks' => 'Remarks',
            'fk_location_id' => 'Location',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
    public function getVendor()
    {
        return $this->hasOne(TblVendor::className(), ['id' => 'fk_vendor_id'])->andOnCondition(['tbl_vendor.status'=>1]);
    }
    public function getVendorinvoice()
    {
        return $this->hasOne(TblVendorInvoice::className(), ['id' => 'fk_vendor_invoice_id'])->andOnCondition(['tbl_vendor_invoice.status'=>1]);
    }
    public function getProduct()
    {
        return $this->hasOne(TblProduct::className(), ['id' => 'fk_product_id'])->andOnCondition(['tbl_product.status'=>1]);
    }
    public function getBin()
    {
        return $this->hasOne(TblBinLocations::className(), ['id' => 'fk_bin_id'])->andOnCondition(['tbl_bin_locations.status'=>1]);
    }

    public function getCostPerItem()
{
    $qty = $this->no_of_items ?: 1;

    // product cost (USD if USA vendor)
    $product_total = $this->price_per_item * $qty;

    if ($this->vendor && $this->vendor->is_usa) {
        $usd_to_cad = 1.35;
        $product_total = $product_total * $usd_to_cad;
    }

    // charges ALWAYS in CAD
    $extra = ($this->shipping ?? 0)
           + ($this->other_charges ?? 0)
           + ($this->custom_charges ?? 0);

    $final = $product_total + $extra;

    return round($final / $qty, 2);
}

}
