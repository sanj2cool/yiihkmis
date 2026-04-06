<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_expense".
 *
 * @property int $id
 * @property string $date
 * @property float $amount
 * @property int $fk_location_id
 * @property int $fk_expense_category_id
 * @property string|null $vendor_name
 * @property int $fk_payment_method_id
 * @property string|null $receipt_no
 * @property string|null $notes
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property string $crt_time
 * @property int|null $mod_by
 * @property string|null $mod_time
 */
class TblExpense extends \yii\db\ActiveRecord
{
  public $crt_time_display;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_expense';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'amount', 'fk_location_id', 'fk_expense_category_id', 'fk_payment_method_id','tax_amount','final_amount','fk_tax_rate_id','fk_vendor_id'], 'required'],
            [['date', 'crt_time', 'mod_time'], 'safe'],
            [['amount','tax_amount','final_amount'], 'number'],
            [['fk_location_id', 'fk_expense_category_id', 'fk_payment_method_id', 'status', 'crt_by', 'mod_by','fk_tax_rate_id','fk_vendor_id'], 'integer'],
            [['notes'], 'string'],
            [['vendor_name', 'receipt_no'], 'string', 'max' => 255],
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
            'date' => 'Date',
            'amount' => 'Amount',
            'fk_tax_rate_id' => 'Tax Rate',
            'fk_location_id' => 'Location',
            'fk_expense_category_id' => 'Expense Category',
            'fk_vendor_id' => 'Vendor',
            'vendor_name' => 'Vendor Name',
            'fk_payment_method_id' => 'Payment Method',
            'receipt_no' => 'Receipt No',
            'notes' => 'Notes',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Created By',
            'crt_time' => 'Created Time',
            'mod_by' => 'Mod By',
            'mod_time' => 'Mod Time',
        ];
    }
    public function getVendor()
    {
        return $this->hasOne(TblVendor::className(), ['id' => 'fk_vendor_id'])->andOnCondition(['tbl_vendor.status'=>1]);
    }
    public function getExpenseCategory()
    {
        return $this->hasOne(TblExpenseCategory::className(), ['id' => 'fk_expense_category_id'])->andOnCondition(['tbl_expense_category.status'=>1]);
    }
    public function getTaxRate()
    {
        return $this->hasOne(TblTaxRate::className(), ['id' => 'fk_tax_rate_id'])->andOnCondition(['tbl_tax_rate.status'=>1]);
    }
    public function getLocation()
    {
        return $this->hasOne(TblLocation::className(), ['id' => 'fk_location_id'])->andOnCondition(['tbl_location.status'=>1]);
    }
    public function getPayMethod()
    {
        return $this->hasOne(TblPreferredPaymentMethod::className(), ['id' => 'fk_payment_method_id']);
    }
    public function getExpenseFiles()
    {
        return $this->hasMany(TblExpenseFile::className(), ['fk_expense_id' => 'id'])
            ->andWhere(['tbl_expense_file.status' => 1]);
    }
    public function getCreatedByUser()
  {
      return $this->hasOne(TblUser::className(), ['id' => 'crt_by'])
          ->andOnCondition(['tbl_user.status' => 1]);
  }


}
