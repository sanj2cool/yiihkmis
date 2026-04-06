<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblVendorAccountPayable;

/**
* TblVendorAccountPayableSearch represents the model behind the search form of `app\models\TblVendorAccountPayable`.
*/
class TblVendorAccountPayableSearch extends TblVendorAccountPayable
{
  public $invoice;
  public $client;
  /**
  * {@inheritdoc}
  */
  public function rules()
  {
    return [
      [['id', 'fk_vendor_invoice_id', 'fk_payment_method_id', 'status'], 'integer'],
      [['amount_received'], 'number'],
      [['ar_date', 'notes', 'ip', 'crt_by', 'mod_by', 'crt_time', 'mod_time','invoice','client'], 'safe'],
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

    $query = TblVendorAccountPayable::find()
    ->where('tbl_vendor_account_payable.status != 0')
    ->andWhere('fk_vendor_invoice_id in (select id from tbl_vendor_invoice where fk_location_id = '.$user_company.')');
    $query->joinWith('invoice');
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
        'amount_received' => [
          'asc' => ['amount_received' => SORT_ASC],
          'desc' => ['amount_received' => SORT_DESC],
          'label' => 'Amount Received'
        ],
        'ar_date' => [
          'asc' => ['ar_date' => SORT_ASC],
          'desc' => ['ar_date' => SORT_DESC],
          'label' => 'Receivable Date'
        ],
        'client' => [
          'asc' => ['tbl_vendor.company_name' => SORT_ASC],
          'desc' => ['tbl_vendor.company_name' => SORT_DESC],
          'label' => 'Vendor'
        ],
        'invoice' => [
          'asc' => ['tbl_vendor_invoice.vendor_invoice_number' => SORT_ASC],
          'desc' => ['tbl_vendor_invoice.vendor_invoice_number' => SORT_DESC],
          'label' => 'Vendor Invoice'

        ]
      ]
    ]);
    // Restore any saved state first
    $this->restoreSearchState();

    $this->load($params);

    if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }
    // Save the current state after loading and validating
    $this->saveSearchState();
    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'fk_vendor_invoice_id' => $this->fk_vendor_invoice_id,
      'amount_received' => $this->amount_received,
      'fk_payment_method_id' => $this->fk_payment_method_id,
      'status' => $this->status,
      'crt_time' => $this->crt_time,
      'mod_time' => $this->mod_time,
    ]);

    $query->andFilterWhere(['like', 'ar_date', $this->ar_date])
    ->andFilterWhere(['like', 'notes', $this->notes])
    ->andFilterWhere(['like', 'ip', $this->ip])
    ->andFilterWhere(['like', 'crt_by', $this->crt_by])
    ->andFilterWhere(['like', 'mod_by', $this->mod_by]);
    $query->andFilterWhere(['like', 'tbl_vendor_invoice.vendor_invoice_number', $this->invoice]);
    $query->andFilterWhere(['like', 'tbl_vendor.company_name', $this->client]);
    return $dataProvider;
  }
  public function saveSearchState()
  {
    $session = Yii::$app->session;
    $attributes = $this->attributes;
    $attributes['invoice'] = $this->invoice; // Include the virtual column
    $attributes['client'] = $this->client; // Include the virtual column
    $session->set($this->formName() . '_search', $attributes);

    // Always save the current 'sort' param from request
    $sortParam = Yii::$app->request->get('sort');
    if ($sortParam) {
      $session->set($this->formName() . '_sort', $sortParam);
    }
  }


  public function restoreSearchState()
  {
    $session = Yii::$app->session;
    $searchData = $session->get($this->formName() . '_search');

    if ($searchData) {
      $this->attributes = $searchData;
      $this->invoice = $searchData['invoice'] ?? null; // Restore the virtual column
      $this->client = $searchData['client'] ?? null; // Restore the virtual column
    }

    // Only restore sort if it's NOT present in the current request
    if (!Yii::$app->request->get('sort')) {
      $sortParam = $session->get($this->formName() . '_sort');
      if ($sortParam) {
        Yii::$app->request->setQueryParams(
          array_merge(Yii::$app->request->queryParams, ['sort' => $sortParam])
        );
      }
    }
  }

  public function clearSearchState()
  {
    $session = Yii::$app->session;
    $session->remove($this->formName() . '_search');
    $session->remove($this->formName() . '_sort');
  }
}
