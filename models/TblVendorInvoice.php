<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_vendor_invoice".
 *
 * @property int $id
 * @property string $vendor_invoice_number
 * @property int $fk_vendor_id
 * @property int|null $fk_terms_id
 * @property string $invoice_date
 * @property string $due_date
 * @property float $hst
 * @property float $subtotal
 * @property float $hst_amount
 * @property float $total_amount
 * @property string|null $comments
 * @property int|null $status
 * @property string|null $ip
 * @property string|null $crt_time
 * @property int $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblVendorInvoice extends \yii\db\ActiveRecord
{
    // Define constants for the statuses
    const STATUS_PAID = 'Paid';
    const STATUS_PARTIALLY_PAID = 'Partially Paid';
    const STATUS_UNPAID = 'Unpaid';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_vendor_invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vendor_invoice_number', 'fk_vendor_id', 'invoice_date', 'due_date', 'hst', 'subtotal', 'hst_amount', 'total_amount','purchase_invoice_no'], 'required'],
            [['fk_vendor_id', 'fk_terms_id', 'status', 'crt_by', 'mod_by','fk_purchase_order_id','discount_type','fk_location_id'], 'integer'],
            [['invoice_date', 'due_date', 'crt_time', 'mod_time'], 'safe'],
            [['hst', 'subtotal', 'hst_amount', 'total_amount','discount_value'], 'number'],
            [['comments'], 'string'],
            [['vendor_invoice_number', 'ip'], 'string', 'max' => 20],
            [['invoice_file','purchase_invoice_no'], 'string', 'max' => 255],

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
            'vendor_invoice_number' => 'Purchase Invoice #',
            'purchase_invoice_no' => 'Vendor Invoice #',
            'fk_vendor_id' => 'Vendor',
            'fk_purchase_order_id' => 'Purchase Order',
            'fk_terms_id' => 'Terms',
            'invoice_date' => 'Invoice Date',
            'due_date' => 'Due Date',
            'hst' => 'HST/GST',
            'subtotal' => 'Subtotal',
            'hst_amount' => 'Hst Amount',
            'total_amount' => 'Total Amount',
            'comments' => 'Comments',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
    public function getVendor()
    {
      return $this->hasOne(TblVendor::className(), ['id' => 'fk_vendor_id']);
    }
    public function getCreatedByUser()
    {
        return $this->hasOne(TblUser::className(), ['id' => 'crt_by'])
            ->andOnCondition(['tbl_user.status' => 1]);
    }
    //---GET THE PAYMENT STATUS ------
    // Define the relationship to accountsReceivable
    public function getPayments()
    {
      return $this->hasMany(TblVendorAccountPayable::className(), ['fk_vendor_invoice_id' => 'id'])
      ->where(['status' => 1]);
    }
    public function getTotalReceived()
    {
        return array_sum(array_column($this->payments, 'amount_received'));
    }

    // Method to calculate payment status
    public function getPaymentStatus()
    {
      $totalAmount = $this->total_amount; // Assuming this is the total invoice amount
      $amountReceived = array_sum(array_column($this->payments, 'amount_received'));
      $totalReceived = (string)$this->getTotalReceived();

      if (bccomp($totalReceived, $totalAmount,2) >= 0) { // Compare with 2 decimal precision
              return self::STATUS_PAID;
          } elseif (bccomp($totalReceived, '0.00', 2) > 0) {
              return self::STATUS_PARTIALLY_PAID;
          } else {
        return self::STATUS_UNPAID;
      }
    }
    // Method to get payment status badge HTML
    public function getPaymentStatusBadge()
    {
      $status = $this->getPaymentStatus();
      switch ($status) {
        case self::STATUS_PAID:
        return '<span class="badge bg-primary">Paid</span>';
        case self::STATUS_PARTIALLY_PAID:
        return '<span class="badge bg-warning">Partially Paid</span>';
        case self::STATUS_UNPAID:
        return '<span class="badge bg-danger">Unpaid</span>';
        default:
        return '<span class="badge bg-secondary">Unknown</span>';
      }
    }
    public function getInvoiceFiles()
    {
        return $this->hasMany(TblVendorInvoiceFile::className(), ['fk_vendor_invoice_id' => 'id'])
            ->andWhere(['tbl_vendor_invoice_file.status' => 1]);
    }
	
	public function getPo()
{
    return $this->hasOne(TblPurchaseOrder::class, ['id' => 'po_id']);
}
}
