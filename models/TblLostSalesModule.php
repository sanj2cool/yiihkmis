<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_lost_sales_module".
 *
 * @property int $id
 * @property int $prepared_by
 * @property string $date
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property int|null $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 * @property int|null $fk_product_id
 * @property string|null $non_product
 * @property string|null $description
 */
class TblLostSalesModule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_lost_sales_module';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prepared_by', 'date'], 'required'],
            [['prepared_by', 'status', 'crt_by', 'mod_by', 'fk_product_id','fk_location_id'], 'integer'],
            [['date', 'crt_time', 'mod_time'], 'safe'],
            [['description'], 'string'],
            [['ip'], 'string', 'max' => 40],
            [['non_product'], 'string', 'max' => 100],
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
            'prepared_by' => 'Prepared By',
            'date' => 'Date',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
            'fk_product_id' => 'Product',
            'non_product' => 'Non-existing Product',
            'description' => 'Description',
        ];
    }
}
