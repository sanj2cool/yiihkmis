<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblClientReceipt;

/**
 * TblClientReceiptSearch represents the model behind the search form of `app\models\TblClientReceipt`.
 */
class TblClientReceiptSearch extends TblClientReceipt
{
    public $client;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_client_id', 'fk_invoice_id', 'fk_payment_method_id', 'fk_bill_from_id', 'status'], 'integer'],
            [['amount_received'], 'number'],
            [['ar_date', 'notes', 'ip', 'crt_by', 'mod_by', 'crt_time', 'mod_time','client','transaction_reference_number'], 'safe'],
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
	
	/*
    public function search($params)
    {
        $session = Yii::$app->session;
        $user_company = $session['userCompany'];
        $query = TblClientReceipt::find()
                ->where(['tbl_client_receipt.status'=>1])
                ->andWhere(['tbl_client_receipt.fk_bill_from_id'=>$user_company]);
        $query->joinWith('client');
        $query->distinct();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
          'defaultOrder' => [
              'id' => SORT_DESC, // Set default sorting
          ],
            'attributes' => [
                'id',
                'fk_payment_method_id',
                'fk_bill_from_id',
                'transaction_reference_number',
                'amount_received' => [
                   'asc' => ['amount_received' => SORT_ASC],
                   'desc' => ['amount_received' => SORT_DESC],
                   'label' => 'Amount Received'
               ],
               'notes',
               'ar_date' => [
                  'asc' => ['ar_date' => SORT_ASC],
                  'desc' => ['ar_date' => SORT_DESC],
                  'label' => 'Receivable Date'
                  ],

                'client' => [
                    'asc' => ['tbl_client.company_name' => SORT_ASC],
                    'desc' => ['tbl_client.company_name' => SORT_DESC],
                    'label' => 'Customer'
                ]
            ]
        ]);
        // $this->load($params);
        //
        // if (!$this->validate()) {
        //     // uncomment the following line if you do not want to return any records when validation fails
        //     // $query->where('0=1');
        //     return $dataProvider;
        // }

        if (!$this->load($params) || !$this->validate()) {
          $this->restoreSearchState();
          // grid filtering conditions
          $query->andFilterWhere([
              'id' => $this->id,
              'fk_client_id' => $this->fk_client_id,
              'fk_invoice_id' => $this->fk_invoice_id,
              'amount_received' => $this->amount_received,
              'fk_payment_method_id' => $this->fk_payment_method_id,
              'fk_bill_from_id' => $this->fk_bill_from_id,
              'status' => $this->status,
              'crt_time' => $this->crt_time,
              'mod_time' => $this->mod_time,
          ]);

          $query->andFilterWhere(['like', 'ar_date', $this->ar_date])
              ->andFilterWhere(['like', 'notes', $this->notes])
              ->andFilterWhere(['like', 'transaction_reference_number', $this->transaction_reference_number])
              ->andFilterWhere(['like', 'ip', $this->ip])
              ->andFilterWhere(['like', 'crt_by', $this->crt_by])
              ->andFilterWhere(['like', 'mod_by', $this->mod_by]);
          $query->andFilterWhere(['like', 'tbl_client.company_name', $this->client]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fk_client_id' => $this->fk_client_id,
            'fk_invoice_id' => $this->fk_invoice_id,
            'amount_received' => $this->amount_received,
            'fk_payment_method_id' => $this->fk_payment_method_id,
            'fk_bill_from_id' => $this->fk_bill_from_id,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'ar_date', $this->ar_date])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'transaction_reference_number', $this->transaction_reference_number])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'crt_by', $this->crt_by])
            ->andFilterWhere(['like', 'mod_by', $this->mod_by]);
        $query->andFilterWhere(['like', 'tbl_client.company_name', $this->client]);
        $this->saveSearchState();
        return $dataProvider;
    }
	**/
	public function search($params)
{
    $session = Yii::$app->session;
    $user_company = $session['userCompany'];

    $query = TblClientReceipt::find()
        ->where(['tbl_client_receipt.status' => 1])
        ->andWhere(['tbl_client_receipt.fk_bill_from_id' => $user_company])
        ->joinWith('client')
        ->distinct();

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    $dataProvider->setSort([
        'defaultOrder' => [
            'id' => SORT_DESC,
        ],
        'attributes' => [
            'id',
            'fk_payment_method_id',
            'fk_bill_from_id',
            'transaction_reference_number',
            'amount_received' => [
                'asc' => ['amount_received' => SORT_ASC],
                'desc' => ['amount_received' => SORT_DESC],
                'label' => 'Amount Received',
            ],
            'notes',
            'ar_date' => [
                'asc' => ['ar_date' => SORT_ASC],
                'desc' => ['ar_date' => SORT_DESC],
                'label' => 'Receivable Date',
            ],
            'client' => [
                'asc' => ['tbl_client.company_name' => SORT_ASC],
                'desc' => ['tbl_client.company_name' => SORT_DESC],
                'label' => 'Customer',
            ],
        ],
    ]);

    // ✅ HANDLE FILTER LOAD / RESET PROPERLY
    if (!empty($params)) {
        $this->load($params);

        if ($this->validate()) {
            $this->saveSearchState();
        }
    } else {
        // Reset clicked → clear session filters
        $this->clearSearchState();
    }

    // ✅ APPLY FILTERS ONLY ONCE
    $query->andFilterWhere([
        'id' => $this->id,
        'fk_client_id' => $this->fk_client_id,
        'fk_invoice_id' => $this->fk_invoice_id,
        'amount_received' => $this->amount_received,
        'fk_payment_method_id' => $this->fk_payment_method_id,
        'fk_bill_from_id' => $this->fk_bill_from_id,
        'status' => $this->status,
        'crt_time' => $this->crt_time,
        'mod_time' => $this->mod_time,
    ]);

    $query->andFilterWhere(['like', 'ar_date', $this->ar_date])
        ->andFilterWhere(['like', 'notes', $this->notes])
        ->andFilterWhere(['like', 'transaction_reference_number', $this->transaction_reference_number])
        ->andFilterWhere(['like', 'ip', $this->ip])
        ->andFilterWhere(['like', 'crt_by', $this->crt_by])
        ->andFilterWhere(['like', 'mod_by', $this->mod_by])
        ->andFilterWhere(['like', 'tbl_client.company_name', $this->client]);

    return $dataProvider;
}

    public function saveSearchState()
     {
         $session = Yii::$app->session;
         // $session->set($this->formName() . '_search', $this->attributes);
         $attributes = $this->attributes;
         $attributes['client'] = $this->client; // Include the virtual column

         $session->set($this->formName() . '_search', $attributes);
     }

     public function restoreSearchState()
     {
         $session = Yii::$app->session;
         $searchData = $session->get($this->formName() . '_search');

         if ($searchData) {
             $this->attributes = $searchData;
             $this->client = $searchData['client'] ?? null; // Restore the virtual column

         }
     }
     public function clearSearchState()
    {
        $session = Yii::$app->session;
        $session->remove($this->formName() . '_search');
    }
}
