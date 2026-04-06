<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblVendorInvoice;

/**
 * TblVendorInvoiceSearch represents the model behind the search form of `app\models\TblVendorInvoice`.
 */
class TblVendorInvoiceSearch extends TblVendorInvoice
{
    public $vendor;
    public $payment_status;
    public $total_received;
	public $po_number;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_vendor_id', 'fk_terms_id', 'status', 'crt_by', 'mod_by','fk_purchase_order_id','discount_type'], 'integer'],
            [['vendor_invoice_number', 'invoice_date', 'due_date', 'comments', 'ip', 'crt_time', 'mod_time','vendor','purchase_invoice_no', 'payment_status','total_received'], 'safe'],
            [['hst', 'subtotal', 'hst_amount', 'total_amount','discount_value'], 'number'],[['po_number'], 'safe'],
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

        $query = TblVendorInvoice::find()
                ->where('tbl_vendor_invoice.status != 0')
                ->andWhere(['tbl_vendor_invoice.fk_location_id'=>$user_company]);

        // add conditions that should always apply here
        $query->joinWith('vendor');

        $subQuery = (new \yii\db\Query())
            ->select(['fk_vendor_invoice_id', 'SUM(amount_received) as total_received'])
            ->from('tbl_vendor_account_payable')
            ->where('status != 0')
            ->groupBy('fk_vendor_invoice_id');

        $query->leftJoin(['ar' => $subQuery], 'ar.fk_vendor_invoice_id = tbl_vendor_invoice.id');

        $query->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
                'attributes' => [
                    'id',
                    'vendor_invoice_number',
                    'vendor' => [
                        'asc' => ['tbl_vendor.company_name' => SORT_ASC],
                        'desc' => ['tbl_vendor.company_name' => SORT_DESC],
                    ],

                    'fk_terms_id',
                    'invoice_date',
                    'due_date',
                    'total_amount',
                    'purchase_invoice_no',
                    'crt_by',
                    // Add other sortable attributes
                    'payment_status' => [
                        'asc' => ['total_received' => SORT_ASC],
                        'desc' => ['total_received' => SORT_DESC],
                        'label' => 'Payment Status',
                    ],
                    // Sorting for total_received
                     'total_received' => [
                         'asc' => ['ar.total_received' => SORT_ASC],
                         'desc' => ['ar.total_received' => SORT_DESC],
                         'label' => 'Amount Paid',
                     ],
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
            'tbl_vendor_invoice.fk_terms_id' => $this->fk_terms_id,
            // 'invoice_date' => $this->invoice_date,
            // 'due_date' => $this->due_date,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'hst' => $this->hst,
            'subtotal' => $this->subtotal,
            'hst_amount' => $this->hst_amount,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'tbl_vendor_invoice.crt_by' => $this->crt_by,
            'mod_time' => $this->mod_time,
            'mod_by' => $this->mod_by,
            'fk_purchase_order_id' => $this->fk_purchase_order_id,
        ]);

        $query->andFilterWhere(['like', 'vendor_invoice_number', $this->vendor_invoice_number])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'purchase_invoice_no', $this->purchase_invoice_no])

            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'invoice_date', $this->invoice_date])
            ->andFilterWhere(['like', 'due_date', $this->due_date])
            ->andFilterWhere(['like', 'tbl_vendor.company_name', $this->vendor]);

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
        if ($this->total_received) {
            $query->andFilterWhere(['ar.total_received' => $this->total_received]);
        }
        return $dataProvider;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $attributes['vendor'] = $this->vendor; // Include the virtual column
        $attributes['payment_status'] = $this->payment_status; // Include the virtual column
        $attributes['total_received'] = $this->total_received; // Include the virtual column
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
              $this->payment_status = $searchData['payment_status'] ?? null; // Restore the virtual column
              $this->total_received = $searchData['total_received'] ?? null; // Restore the virtual column
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
