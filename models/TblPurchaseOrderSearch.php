<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblPurchaseOrder;

/**
 * TblPurchaseOrderSearch represents the model behind the search form of `app\models\TblPurchaseOrder`.
 */
class TblPurchaseOrderSearch extends TblPurchaseOrder
{
    public $vendor;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_vendor_id', 'hst', 'purchase_status', 'payment_status', 'approved_by', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['po_number', 'po_date', 'comments','internal_notes', 'ip', 'crt_time', 'mod_time','expected_delivery_date','vendor'], 'safe'],
            [['subtotal', 'hst_amount', 'total_amount'], 'number'],
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

        $query = TblPurchaseOrder::find()
                  ->where('tbl_purchase_order.status != 0')
                  ->andWhere(['tbl_purchase_order.fk_location_id'=>$user_company]);

        // add conditions that should always apply here
        $query->joinWith('vendor');
        $query->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
                'attributes' => [
                    'id',
                    'po_number',
                    'vendor' => [
                        'asc' => ['tbl_vendor.company_name' => SORT_ASC],
                        'desc' => ['tbl_vendor.company_name' => SORT_DESC],
                    ],

                    'po_date',
                    'expected_delivery_date',
                    'total_amount',
                    'purchase_status',
                    'payment_status',
                    'approved_by',
                    'internal_notes'
                    // Add other sortable attributes
                ],
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
            'fk_vendor_id' => $this->fk_vendor_id,
            // 'po_date' => $this->po_date,
            'hst' => $this->hst,
            'subtotal' => $this->subtotal,
            'hst_amount' => $this->hst_amount,
            'total_amount' => $this->total_amount,
            'purchase_status' => $this->purchase_status,
            'payment_status' => $this->payment_status,
            'approved_by' => $this->approved_by,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'tbl_purchase_order.crt_by' => $this->crt_by,
            'mod_time' => $this->mod_time,
            'mod_by' => $this->mod_by,
        ]);

        $query->andFilterWhere(['like', 'po_number', $this->po_number])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'internal_notes', $this->internal_notes])
            ->andFilterWhere(['like', 'po_date', $this->po_date])
            ->andFilterWhere(['like', 'expected_delivery_date', $this->expected_delivery_date])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'tbl_vendor.company_name', $this->vendor]);

        return $dataProvider;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $attributes['vendor'] = $this->vendor; // Include the virtual column
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
              $this->vendor = $searchData['vendor'] ?? null; // Restore the virtual column
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
