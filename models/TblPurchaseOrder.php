<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_purchase_order".
 *
 * @property int $id
 * @property string $po_number
 * @property int $fk_vendor_id
 * @property string $po_date
 * @property int $hst
 * @property float $subtotal
 * @property float $hst_amount
 * @property float $total_amount
 * @property string|null $comments
 * @property int $purchase_status
 * @property int $payment_status
 * @property int $approved_by
 * @property int|null $status
 * @property string|null $ip
 * @property string|null $crt_time
 * @property int $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblPurchaseOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_purchase_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['po_number', 'fk_vendor_id', 'po_date', 'hst', 'subtotal', 'hst_amount', 'total_amount', 'purchase_status', 'payment_status', 'approved_by'], 'required'],
            [['fk_vendor_id', 'hst', 'purchase_status', 'payment_status', 'approved_by', 'status', 'crt_by', 'mod_by','fk_location_id'], 'integer'],
            [['po_date','expected_delivery_date', 'crt_time', 'mod_time'], 'safe'],
            [['subtotal', 'hst_amount', 'total_amount'], 'number'],
            [['comments','internal_notes'], 'string'],
            [['po_number', 'ip'], 'string', 'max' => 20],
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
            'po_number' => 'PO #',
            'fk_vendor_id' => 'Vendor',
            'po_date' => 'PO Date',
            'hst' => 'HST',
            'subtotal' => 'Subtotal',
            'hst_amount' => 'Hst Amount',
            'total_amount' => 'Total Amount',
            'comments' => 'Remarks',
            'internal_notes' => 'Internal Notes',
            'expected_delivery_date' => 'Exp. Delivery Date',
            'purchase_status' => 'Purchase Status',
            'payment_status' => 'Payment Status',
            'approved_by' => 'Approved By',
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
	
	//updated to convert Purchage ordrs to purchage Invoices.
	public function getItems()
{
    return $this->hasMany(TblPurchaseOrderItem::class, ['fk_purchase_order_id' => 'id'])
        ->andWhere(['status' => 1]);
}

public function getCreatedByName()
{
    if (empty($this->crt_by)) {
        return '(not set)';
    }

    // Get user
    $user = \app\models\TblUser::find()
        ->where(['id' => $this->crt_by])
        ->one();

    if ($user) {
        // If linked employee exists → show employee name
        if ($user->fk_employee_id) {
            $emp = \app\models\TblEmployee::find()
                ->where(['id' => $user->fk_employee_id])
                ->one();

            if ($emp && $emp->name) {
                return $emp->name;
            }
        }

        // fallback → username
        return $user->username;
    }

    return '(not set)';
}


}
