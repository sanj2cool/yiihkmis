<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_client_receipt".
 *
 * @property int $id
 * @property int $fk_client_id
 * @property int|null $fk_invoice_id
 * @property float $amount_received
 * @property string $ar_date
 * @property int $fk_payment_method_id
 * @property int|null $fk_bill_from_id
 * @property string|null $notes
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblClientReceipt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_client_receipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fk_client_id', 'amount_received', 'ar_date', 'fk_payment_method_id'], 'required'],
            [['fk_client_id', 'fk_invoice_id', 'fk_payment_method_id', 'fk_bill_from_id', 'status'], 'integer'],
            [['amount_received'], 'number'],
            [['notes'], 'string'],
            [['crt_time', 'mod_time'], 'safe'],
            [['ar_date', 'mod_by'], 'string', 'max' => 50],
            [['ip', 'crt_by'], 'string', 'max' => 40],
            [['transaction_reference_number'], 'string', 'max' => 100],
        ];
    }
    public function beforeSave($insert) {
        $session = Yii::$app -> session;
        if ($insert) {
          $this -> ip = Yii::$app -> getRequest() -> getUserIp();
          $this -> crt_by = $session -> get('userId');
          $this->fk_bill_from_id = $session->get('userCompany');
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
            'fk_client_id' => 'Customer',
            'fk_invoice_id' => 'Fk Invoice ID',
            'amount_received' => 'Received Amount',
            'ar_date' => 'Receivable Date',
            'fk_payment_method_id' => 'Payment Method',
            'fk_bill_from_id' => 'Receiving Company',
            'transaction_reference_number' => 'Transaction Reference Number',
            'notes' => 'Notes',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
    public function getClient()
    {
        return $this->hasOne(TblClient::className(), ['id' => 'fk_client_id']);
    }
    public function getReceiptFiles()
    {
        return $this->hasMany(TblClientReceiptImage::className(), ['fk_receipt_id' => 'id'])
            ->andWhere(['tbl_client_receipt_image.status' => 1]);
    }
}
