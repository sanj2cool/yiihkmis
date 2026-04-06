<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_client".
 *
 * @property int $id
 * @property string $company_name
 * @property string|null $contact_name
 * @property string|null $contact_title
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $country
 * @property int|null $fk_terms_id
 * @property int|null $fk_preferred_payment_method_id
 * @property int|null $credit_limit
 * @property string|null $notes
 * @property int|null $status
 * @property string|null $ip
 * @property string|null $crt_time
 * @property int|null $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'email'], 'required'],
            [['status', 'crt_by', 'mod_by','tax_id'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title', 'email', 'address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 50],
            [['city', 'state', 'country','company_name'], 'string', 'max' => 100],
            [['postal_code', 'ip'], 'string', 'max' => 20],
            [['email'], 'unique'],
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
            'title' => 'Title',
            'company_name' => 'Company Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => 'Postal Code',
            'country' => 'Country',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
}
