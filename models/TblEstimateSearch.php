<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblEstimate;

/**
 * TblEstimateSearch represents the model behind the search form of `app\models\TblEstimate`.
 */
class TblEstimateSearch extends TblEstimate
{
    public $customer;
    public $emailSentStatus;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_bill_from_id', 'fk_client_id', 'fk_terms_id', 'hst', 'discount_type', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['estimate_number', 'po_number', 'estimate_date', 'due_date', 'comments', 'ip', 'crt_time', 'mod_time','customer','emailSentStatus'], 'safe'],
            [['discount_value', 'subtotal', 'hst_amount', 'total_amount'], 'number'],
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

        $query = TblEstimate::find()
                ->alias('i')
                ->where('i.status != 0')
                ->andWhere(['i.fk_bill_from_id'=>$user_company]);

        // add conditions that should always apply here
        $query->joinWith(['customer']); // Join with customer table
        $query->joinWith(['invoiceEmailLog ie'], true, 'LEFT JOIN');
        $query->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
                'attributes' => [
                    'id',
                    'fk_bill_from_id',
                    'estimate_number',
                    'customer' => [
                        'asc' => ['tbl_client.company_name' => SORT_ASC],
                        'desc' => ['tbl_client.company_name' => SORT_DESC],
                    ],

                    'fk_terms_id',
                    'estimate_date',
                    'due_date',
                    'po_number',
                    'total_amount',
                    // Custom sorting for emailSentStatus
                      'emailSentStatus' => [
                          'asc' => ['ie.id' => SORT_ASC],
                          'desc' => ['ie.id' => SORT_DESC],
                          'default' => SORT_DESC,
                          'label' => 'Email Sent Status',
                      ],

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
            'fk_bill_from_id' => $this->fk_bill_from_id,
            'fk_client_id' => $this->fk_client_id,
            'i.fk_terms_id' => $this->fk_terms_id,
            // 'estimate_date' => $this->estimate_date,
            // 'due_date' => $this->due_date,
            'hst' => $this->hst,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'subtotal' => $this->subtotal,
            'hst_amount' => $this->hst_amount,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'crt_by' => $this->crt_by,
            'mod_time' => $this->mod_time,
            'mod_by' => $this->mod_by,
        ]);

        $query->andFilterWhere(['like', 'estimate_number', $this->estimate_number])
            ->andFilterWhere(['like', 'po_number', $this->po_number])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'estimate_date', $this->estimate_date])
            ->andFilterWhere(['like', 'due_date', $this->due_date])
            ->andFilterWhere(['like', 'tbl_client.company_name', $this->customer]);
        if ($this->emailSentStatus === 'Sent') {
            $query->andWhere(['not', ['ie.id' => null]]);
        } elseif ($this->emailSentStatus === 'Not Sent') {
            $query->andWhere(['ie.id' => null]);
        }
        return $dataProvider;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $attributes['customer'] = $this->customer; // Include the virtual column
        $attributes['emailSentStatus'] = $this->emailSentStatus; // Include the virtual column

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
              $this->customer = $searchData['customer'] ?? null; // Restore the virtual column
              $this->emailSentStatus = $searchData['emailSentStatus'] ?? null; // Restore the virtual column
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
