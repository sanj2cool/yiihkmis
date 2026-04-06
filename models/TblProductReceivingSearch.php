<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblProductReceiving;

/**
 * TblProductReceivingSearch represents the model behind the search form of `app\models\TblProductReceiving`.
 */
class TblProductReceivingSearch extends TblProductReceiving
{
    public $vendor;
    public $product;
    public $vendorinvoice;
    // public $bin;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_product_id', 'fk_vendor_invoice_id', 'no_of_items', 'fk_bin_id', 'status', 'crt_by', 'mod_by','fk_location_id','fk_vendor_id','fk_vendor_invoice_item_id'], 'integer'],
            [['remarks', 'ip', 'crt_time', 'mod_time','price_per_item','vendor','product','vendorinvoice','bin_location'], 'safe'],
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

        $query = TblProductReceiving::find()
                  ->where('tbl_product_receiving.status != 0')
                  ->andWhere(['tbl_product_receiving.fk_location_id'=>$user_company]);
                  $query->joinWith('vendor');
                  $query->joinWith('product');
                  $query->joinWith('vendorinvoice');
                  // ->joinWith('bin');
                  // ->distinct();
        $query->distinct();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
              ]
        ]);

        $dataProvider->sort->attributes['vendor'] = [
                  'asc' => ['tbl_vendor.company_name' => SORT_ASC],
                  'desc' => ['tbl_vendor.company_name' => SORT_DESC],
              ];
      $dataProvider->sort->attributes['product'] = [
                'asc' => ['tbl_product.name' => SORT_ASC],
                'desc' => ['tbl_product.name' => SORT_DESC],
            ];
      $dataProvider->sort->attributes['vendorinvoice'] = [
                'asc' => ['tbl_vendor_invoice.vendor_invoice_number' => SORT_ASC],
                'desc' => ['tbl_vendor_invoice.vendor_invoice_number' => SORT_DESC],
            ];
      // $dataProvider->sort->attributes['bin'] = [
      //           'asc' => ['tbl_bin_locations.area' => SORT_ASC],
      //           'desc' => ['tbl_bin_locations.area' => SORT_DESC],
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
            'fk_product_id' => $this->fk_product_id,
            'fk_vendor_invoice_id' => $this->fk_vendor_invoice_id,
            'fk_vendor_id' => $this->fk_vendor_id,
            'no_of_items' => $this->no_of_items,
            'fk_bin_id' => $this->fk_bin_id,
            'status' => $this->status,
            'crt_by' => $this->crt_by,
            'mod_by' => $this->mod_by,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
            'tbl_product_receiving.fk_location_id' => $this->fk_location_id,
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks])
              ->andFilterWhere(['like', 'price_per_item', $this->price_per_item])
              ->andFilterWhere(['like', 'bin_location', $this->bin_location])
              ->andFilterWhere(['like', 'ip', $this->ip]);
        if($this->vendor != ""){
              $query->joinWith(['vendor' => function ($q) {
                  $q->where('tbl_vendor.company_name LIKE "%' . $this->vendor . '%"');
              }]);
            }
        if($this->product != ""){
              $query->joinWith(['product' => function ($q) {
                  $q->where('tbl_product.name LIKE "%' . $this->product . '%" OR tbl_product.internal_sku LIKE "%' . $this->product . '%"');
              }]);
            }
      if($this->vendorinvoice != ""){
            $query->joinWith(['vendorinvoice' => function ($q) {
                $q->where('tbl_vendor_invoice.vendor_invoice_number LIKE "%' . $this->vendorinvoice . '%"');
            }]);
          }
      // if($this->bin != ""){
      //       $query->joinWith(['bin' => function ($q) {
      //           $q->where('tbl_bin_locations.area LIKE "%' . $this->bin . '%" OR tbl_bin_locations.row LIKE "%' . $this->bin . '%" OR tbl_bin_locations.bay LIKE "%' . $this->bin . '%" OR tbl_bin_locations.level LIKE "%' . $this->bin . '%" OR tbl_bin_locations.position LIKE "%' . $this->bin . '%"');
      //       }]);
      //     }


        return $dataProvider;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $attributes['vendor'] = $this->vendor; // Include the virtual column
        $attributes['product'] = $this->product; // Include the virtual column
        $attributes['vendorinvoice'] = $this->vendorinvoice; // Include the virtual column
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
              $this->product = $searchData['product'] ?? null; // Restore the virtual column
              $this->vendorinvoice = $searchData['vendorinvoice'] ?? null; // Restore the virtual column
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
