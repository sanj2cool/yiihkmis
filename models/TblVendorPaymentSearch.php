<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblVendorPayment;

/**
 * TblVendorPaymentSearch represents the model behind the search form of `app\models\TblVendorPayment`.
 */
class TblVendorPaymentSearch extends TblVendorPayment
{
    public $vendor;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_vendor_invoice_id', 'fk_vendor_id', 'fk_payment_method_id', 'fk_location_id', 'status'], 'integer'],
            [['amount_received'], 'number'],
            [['ar_date', 'notes', 'ip', 'crt_by', 'mod_by', 'crt_time', 'mod_time','vendor'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $session = Yii::$app->session;
        $user_company = $session['userCompany'];
        $query = TblVendorPayment::find()
                  ->where(['tbl_vendor_payment.status'=>1])
                  ->andWhere(['tbl_vendor_payment.fk_location_id'=>$user_company]);
        $query->joinWith('vendor');
        $query->distinct();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
                'attributes' => [
                    'id',
                    'amount_received',
                    'vendor' => [
                        'asc' => ['tbl_vendor.company_name' => SORT_ASC],
                        'desc' => ['tbl_vendor.company_name' => SORT_DESC],
                    ],

                    'fk_payment_method_id',
                    'fk_location_id',
                    'ar_date',
                    'notes'
                    // Add other sortable attributes
                ],
              ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fk_vendor_invoice_id' => $this->fk_vendor_invoice_id,
            'fk_vendor_id' => $this->fk_vendor_id,
            'amount_received' => $this->amount_received,
            'fk_payment_method_id' => $this->fk_payment_method_id,
            'fk_location_id' => $this->fk_location_id,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'ar_date', $this->ar_date])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'crt_by', $this->crt_by])
            ->andFilterWhere(['like', 'mod_by', $this->mod_by])
            ->andFilterWhere(['like', 'tbl_vendor.company_name', $this->vendor]);

        return $dataProvider;
    }
}
