<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblOrder;

/**
* TblOrderSearch represents the model behind the search form of `app\models\TblOrder`.
*/
class TblOrderSearch extends TblOrder
{
  public $client;
  /**
  * {@inheritdoc}
  */
  public function rules()
  {
    return [
      [['id', 'fk_client_id', 'order_status', 'status', 'crt_by', 'mod_by','fk_location_id'], 'integer'],
      [['order_number', 'order_date', 'comments', 'ip', 'crt_time', 'mod_time','client'], 'safe'],
      [['hst', 'subtotal', 'hst_amount', 'total_amount'], 'number'],
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

    $query = TblOrder::find()
            ->where('tbl_order.status != 0')
            ->andWhere(['tbl_order.fk_location_id'=>$user_company])
            ->joinWith('client');

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'defaultOrder' => [
          'id' => SORT_DESC, // Set default sorting
        ],
      ]
    ]);

    $dataProvider->sort->attributes['client'] = [
      'asc' => ['tbl_client.company_name' => SORT_ASC],
      'desc' => ['tbl_client.company_name' => SORT_DESC],
    ];

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
      'fk_location_id' => $this->fk_location_id,
      'fk_client_id' => $this->fk_client_id,
      'order_date' => $this->order_date,
      'hst' => $this->hst,
      'subtotal' => $this->subtotal,
      'hst_amount' => $this->hst_amount,
      'total_amount' => $this->total_amount,
      'order_status' => $this->order_status,
      'status' => $this->status,
      'crt_time' => $this->crt_time,
      'crt_by' => $this->crt_by,
      'mod_time' => $this->mod_time,
      'mod_by' => $this->mod_by,
    ]);

    $query->andFilterWhere(['like', 'order_number', $this->order_number])
    ->andFilterWhere(['like', 'comments', $this->comments])
    ->andFilterWhere(['like', 'ip', $this->ip]);
    if($this->client != ""){
      $query->joinWith(['client' => function ($q) {
        $q->where('tbl_client.company_name LIKE "%' . $this->client . '%"');
      }]);
    }
    return $dataProvider;
  }
  public function saveSearchState()
  {
    $session = Yii::$app->session;
    $attributes = $this->attributes;
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
