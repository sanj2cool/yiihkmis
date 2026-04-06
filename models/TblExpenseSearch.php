<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblExpense;

/**
* TblExpenseSearch represents the model behind the search form of `app\models\TblExpense`.
*/
class TblExpenseSearch extends TblExpense
{
  public $vendor;
  public $crt_time_display;

  /**
  * {@inheritdoc}
  */
  public function rules()
  {
    return [
      [['id', 'fk_location_id', 'fk_expense_category_id', 'fk_payment_method_id', 'status', 'crt_by', 'mod_by','fk_vendor_id'], 'integer'],
      [['date', 'vendor_name', 'receipt_no', 'notes', 'ip', 'crt_time', 'mod_time','vendor','crt_time_display'], 'safe'],
      [['amount','tax_amount','final_amount'], 'number'],
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

    $query = TblExpense::find()
    ->where(['<>','tbl_expense.status',0])
    ->andWhere(['tbl_expense.fk_location_id' => $user_company])
    ->with('expenseFiles');
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
          'date',
          'amount',
          'final_amount',
          'fk_expense_category_id',
          'fk_payment_method_id',
          'fk_location_id',
          'crt_by',
          // ✅ IMPORTANT PART
          'crt_time_display' => [
            'asc' => ['tbl_expense.crt_time' => SORT_ASC],
            'desc' => ['tbl_expense.crt_time' => SORT_DESC],
            'default' => SORT_DESC,
            'label' => 'Created At',
          ],
        ],
      ]
    ]);
    $dataProvider->sort->attributes['vendor'] = [
      'asc' => ['tbl_vendor.company_name' => SORT_ASC],
      'desc' => ['tbl_vendor.company_name' => SORT_DESC],
    ];
    if (!$this->load($params) || !$this->validate()) {
      $this->restoreSearchState();
      // grid filtering conditions
      $query->andFilterWhere([
        'id' => $this->id,
        // 'date' => $this->date,
        'amount' => $this->amount,
        'tax_amount' => $this->tax_amount,
        'final_amount' => $this->final_amount,
        'tbl_expense.fk_location_id' => $this->fk_location_id,
        'fk_expense_category_id' => $this->fk_expense_category_id,
        'fk_payment_method_id' => $this->fk_payment_method_id,
        'fk_vendor_id' => $this->fk_vendor_id,
        'status' => $this->status,
        'tbl_expense.crt_by' => $this->crt_by,
        'crt_time' => $this->crt_time,
        'mod_by' => $this->mod_by,
        'mod_time' => $this->mod_time,
      ]);

      $query->andFilterWhere(['like', 'vendor_name', $this->vendor_name])
      ->andFilterWhere(['like', 'receipt_no', $this->receipt_no])
      ->andFilterWhere(['like', 'notes', $this->notes])
      ->andFilterWhere(['like', 'date', $this->date])
      ->andFilterWhere(['like', 'ip', $this->ip]);
      if($this->vendor != ""){
        $query->joinWith(['vendor' => function ($q) {
          $q->where('tbl_vendor.company_name LIKE "%' . $this->vendor . '%"');
        }]);
      }
      if (!empty($this->crt_time_display)) {
        $query->andWhere([
          'like',
          new \yii\db\Expression("DATE_FORMAT(tbl_expense.crt_time, '%Y-%m-%d %h:%i%p')"),
          $this->crt_time_display
        ]);
      }

      return $dataProvider;
    }

    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      // 'date' => $this->date,
      'amount' => $this->amount,
      'tax_amount' => $this->tax_amount,
      'final_amount' => $this->final_amount,
      'tbl_expense.fk_location_id' => $this->fk_location_id,
      'fk_expense_category_id' => $this->fk_expense_category_id,
      'fk_payment_method_id' => $this->fk_payment_method_id,
      'fk_vendor_id' => $this->fk_vendor_id,
      'status' => $this->status,
      'tbl_expense.crt_by' => $this->crt_by,
      'crt_time' => $this->crt_time,
      'mod_by' => $this->mod_by,
      'mod_time' => $this->mod_time,
    ]);

    $query->andFilterWhere(['like', 'vendor_name', $this->vendor_name])
    ->andFilterWhere(['like', 'receipt_no', $this->receipt_no])
    ->andFilterWhere(['like', 'notes', $this->notes])
    ->andFilterWhere(['like', 'date', $this->date])
    ->andFilterWhere(['like', 'ip', $this->ip]);

    if($this->vendor != ""){
      $query->joinWith(['vendor' => function ($q) {
        $q->where('tbl_vendor.company_name LIKE "%' . $this->vendor . '%"');
      }]);
    }
    if (!empty($this->crt_time_display)) {
      $query->andWhere([
        'like',
        new \yii\db\Expression("DATE_FORMAT(tbl_expense.crt_time, '%Y-%m-%d %h:%i%p')"),
        $this->crt_time_display
      ]);
    }

    $this->saveSearchState();
    return $dataProvider;
  }
  public function saveSearchState()
  {
    $session = Yii::$app->session;
    $attributes = $this->attributes;
    $attributes['vendor'] = $this->vendor; // Include the virtual column
    $session->set($this->formName() . '_search', $attributes);
  }

  public function restoreSearchState()
  {
    $session = Yii::$app->session;
    $searchData = $session->get($this->formName() . '_search');

    if ($searchData) {
      $this->attributes = $searchData;
      $this->vendor = $searchData['vendor'] ?? null; // Restore the virtual column
    }
  }
  public function clearSearchState()
  {
    $session = Yii::$app->session;
    $session->remove($this->formName() . '_search');
  }
}
