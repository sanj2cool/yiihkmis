<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblInvoice;

/**
 * TblInvoiceSearch represents the model behind the search form of `app\models\TblInvoice`.
 */
class TblInvoiceSearch extends TblInvoice
{
    public $customer;
    public $payment_status;
    public $emailSentStatus;
    public $total_received;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_client_id', 'fk_terms_id', 'discount_type', 'status', 'crt_by', 'mod_by','fk_bill_from_id'], 'integer'],
            [['invoice_number', 'invoice_date', 'due_date', 'comments', 'ip', 'crt_time', 'mod_time', 'customer', 'payment_status','emailSentStatus','total_received','po_number'], 'safe'],
            [['hst', 'discount_value', 'subtotal', 'hst_amount', 'total_amount'], 'number'],
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

        $query = TblInvoice::find()
                  ->alias('i')
                  ->where('i.status != 0')
                  ->andWhere(['i.fk_bill_from_id'=>$user_company]);
        $query->joinWith(['customer']); // Join with customer table
        // Use left join to include invoices without an email log
         $query->joinWith(['invoiceEmailLog ie'], true, 'LEFT JOIN');
        // Add a subquery to calculate total_received
        $subQuery = (new \yii\db\Query())
            ->select(['fk_invoice_id', 'SUM(amount_received) as total_received'])
            ->from('tbl_account_receivable')
            ->where('status != 0')
            ->groupBy('fk_invoice_id');

        $query->leftJoin(['ar' => $subQuery], 'ar.fk_invoice_id = i.id');
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
                    'fk_bill_from_id',
                    'invoice_number',
                    'customer' => [
                        'asc' => ['tbl_client.company_name' => SORT_ASC],
                        'desc' => ['tbl_client.company_name' => SORT_DESC],
                    ],

                    'fk_terms_id',
                    'invoice_date',
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
                      // Custom sorting for paymentStatus
                   'payment_status' => [
                       'asc' => ['total_received' => SORT_ASC],
                       'desc' => ['total_received' => SORT_DESC],
                       'label' => 'Payment Status',
                   ],
                   // Sorting for total_received
                    'total_received' => [
                        'asc' => ['ar.total_received' => SORT_ASC],
                        'desc' => ['ar.total_received' => SORT_DESC],
                        'label' => 'Amount Received',
                    ],

                    // Add other sortable attributes
                ],
              ]
        ]);
        // $dataProvider->sort->attributes['customer'] = [
        //           'asc' => ['tbl_client.company_name' => SORT_ASC],
        //           'desc' => ['tbl_client.company_name' => SORT_DESC],
        //       ];

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
            'fk_client_id' => $this->fk_client_id,
            'fk_terms_id' => $this->fk_terms_id,
            'fk_bill_from_id' => $this->fk_bill_from_id,
            // 'invoice_date' => $this->invoice_date,
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

        $query->andFilterWhere(['like', 'invoice_number', $this->invoice_number])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'po_number', $this->po_number])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'invoice_date', $this->invoice_date])
            ->andFilterWhere(['like', 'due_date', $this->due_date])

            ->andFilterWhere(['like', 'tbl_client.company_name', $this->customer]);
        if ($this->payment_status) {
            switch ($this->payment_status) {
                case self::STATUS_PAID:
                    $query->andWhere(['>=', 'ar.total_received', new \yii\db\Expression('i.total_amount')]);
                    break;
                case self::STATUS_PARTIALLY_PAID:
                    $query->andWhere([
                        'AND',
                        ['>', 'ar.total_received', 0],
                        ['<', 'ar.total_received', new \yii\db\Expression('i.total_amount')],
                    ]);
                    break;
                case self::STATUS_UNPAID:
                    $query->andWhere(['OR', ['ar.total_received' => null], ['<=', 'ar.total_received', 0]]);
                    $query->andWhere(['>', 'i.total_amount', 0]);
                    break;
            }
        }
        // Apply filtering conditions based on email sent status
        if ($this->emailSentStatus === 'Sent') {
            $query->andWhere(['not', ['ie.id' => null]]);
        } elseif ($this->emailSentStatus === 'Not Sent') {
            $query->andWhere(['ie.id' => null]);
        }
        if ($this->total_received) {
            $query->andFilterWhere(['ar.total_received' => $this->total_received]);
        }
        return $dataProvider;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $attributes['customer'] = $this->customer; // Include the virtual column
        $attributes['payment_status'] = $this->payment_status; // Include the virtual column
        $attributes['total_received'] = $this->total_received; // Include the virtual column
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
              $this->payment_status = $searchData['payment_status'] ?? null; // Restore the virtual column
              $this->total_received = $searchData['total_received'] ?? null; // Restore the virtual column
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
