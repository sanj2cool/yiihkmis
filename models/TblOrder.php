<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_order".
 *
 * @property int $id
 * @property string $order_number
 * @property int $fk_client_id
 * @property string $order_date
 * @property float $hst
 * @property float $subtotal
 * @property float $hst_amount
 * @property float $total_amount
 * @property string|null $comments
 * @property int $order_status 1- order placed, 2- in process, 3- completed/delivered
 * @property int|null $status
 * @property string|null $ip
 * @property string|null $crt_time
 * @property int $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_number', 'fk_client_id', 'order_date', 'hst', 'subtotal', 'hst_amount', 'total_amount'], 'required'],
            [['fk_client_id', 'order_status', 'status', 'crt_by', 'mod_by','fk_location_id','fk_invoice_id'], 'integer'],
            [['order_date', 'crt_time', 'mod_time'], 'safe'],
            [['hst', 'subtotal', 'hst_amount', 'total_amount'], 'number'],
            [['comments'], 'string'],
            [['order_number', 'ip'], 'string', 'max' => 20],
        ];
    }
    public function beforeSave($insert) {
        $session = Yii::$app -> session;
        if ($insert) {
          $this -> ip = Yii::$app -> getRequest() -> getUserIp();
          $this -> crt_by = $session -> get('userId');
          $this -> crt_time = date('Y-m-d H:i:s');
          $this -> status = 1;
          $this->fk_location_id = $session->get('userCompany');
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
            'order_number' => 'Order Number',
            'client' => 'Client',
            'fk_client_id' => 'Customer',
            'order_date' => 'Order Date',
            'hst' => 'Hst',
            'subtotal' => 'Subtotal',
            'hst_amount' => 'Hst Amount',
            'total_amount' => 'Total Amount',
            'comments' => 'Comments',
            'order_status' => 'Order Status',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
    public function getClient()
    {
        return $this->hasOne(TblClient::className(), ['id' => 'fk_client_id'])->andOnCondition(['tbl_client.status'=>1]);
    }
}
