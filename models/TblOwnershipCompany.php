<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_ownership_company".
 *
 * @property int $id
 * @property string $company_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $country
 * @property string|null $hst_number
 * @property int|null $status
 * @property string|null $ip
 * @property string|null $crt_time
 * @property int|null $crt_by
 * @property string|null $mod_time
 * @property int|null $mod_by
 */
class TblOwnershipCompany extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_ownership_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_name', 'email','fk_tax_id','location_name'], 'required'],
            [['status', 'crt_by', 'mod_by','fk_tax_id'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['company_name', 'email', 'address','logo'], 'string', 'max' => 255],
            [['phone', 'hst_number'], 'string', 'max' => 50],
            [['city', 'state', 'country','location_name'], 'string', 'max' => 100],
            [['postal_code', 'ip'], 'string', 'max' => 20],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'location_name' => 'Location Name',
            'company_name' => 'Company Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => 'Postal Code',
            'country' => 'Country',
            'hst_number' => 'Hst Number',
            'status' => 'Status',
            'ip' => 'Ip',
            'crt_time' => 'Crt Time',
            'crt_by' => 'Crt By',
            'mod_time' => 'Mod Time',
            'mod_by' => 'Mod By',
        ];
    }
}
