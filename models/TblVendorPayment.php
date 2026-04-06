<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_vendor_account_payable".
 *
 * @property int $id
 * @property int $fk_vendor_invoice_id
 * @property float $amount_received
 * @property string $ar_date
 * @property int $fk_payment_method_id
 * @property string|null $notes
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblVendorPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_vendor_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_vendor_id', 'amount_received', 'ar_date', 'fk_payment_method_id','fk_location_id'], 'required'],
            [['fk_vendor_invoice_id', 'fk_payment_method_id', 'status','fk_vendor_id','fk_location_id'], 'integer'],
            [['amount_received'], 'number'],
            [['notes'], 'string'],
            [['crt_time', 'mod_time'], 'safe'],
            [['ar_date', 'mod_by'], 'string', 'max' => 50],
            [['ip', 'crt_by'], 'string', 'max' => 40],
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
            'fk_vendor_id' => 'Vendor',
            'fk_vendor_invoice_id' => 'Vendor Invoice',
            'amount_received' => 'Paid Amount',
            'ar_date' => 'Paid Date',
            'fk_payment_method_id' => 'Payment Method',
            'notes' => 'Notes',
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
      return $this->hasOne(TblVendor::className(), ['id' => 'fk_vendor_id']);
    }
    public function getReceiptFiles()
    {
        return $this->hasMany(TblVendorPaymentFile::className(), ['fk_vendor_payment_id' => 'id'])
            ->andWhere(['tbl_vendor_payment_file.status' => 1]);
    }
}
