<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_estimate".
 *
 * @property int $id
 * @property string $estimate_number
 * @property string|null $po_number
 * @property int $fk_bill_from_id this is ownership company
 * @property int $fk_client_id
 * @property int|null $fk_terms_id
 * @property string $estimate_date
 * @property string $due_date
 * @property int $hst
 * @property int|null $discount_type
 * @property float|null $discount_value
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
class TblEstimate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_estimate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estimate_number', 'fk_client_id', 'estimate_date', 'due_date', 'hst', 'subtotal', 'hst_amount', 'total_amount'], 'required'],
            [['fk_bill_from_id', 'fk_client_id', 'fk_terms_id', 'hst', 'discount_type', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['estimate_date', 'due_date', 'crt_time', 'mod_time'], 'safe'],
            [['discount_value', 'subtotal', 'hst_amount', 'total_amount'], 'number'],
            [['comments'], 'string'],
            [['estimate_number', 'ip'], 'string', 'max' => 20],
            [['po_number'], 'string', 'max' => 100],
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
            'estimate_number' => 'Estimate Number',
            'po_number' => 'Po Number',
            'fk_bill_from_id' => 'Bill From',
            'fk_client_id' => 'Client',
            'fk_terms_id' => 'Terms',
            'estimate_date' => 'Estimate Date',
            'due_date' => 'Due Date',
            'hst' => 'Hst',
            'discount_type' => 'Discount Type',
            'discount_value' => 'Discount Value',
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
    public function getCustomer()
    {
      return $this->hasOne(TblClient::className(), ['id' => 'fk_client_id']);
    }
    public function getInvoiceEmailLog()
    {
      return $this->hasOne(TblEstimateSentLog::class, ['fk_estimate_id' => 'id']);
    }

    public function getEmailSentStatus()
    {
      return $this->invoiceEmailLog ? '<span class="badge bg-success">Sent</span>' : '<span class="badge bg-danger">Not Sent</span>';
    }
}
