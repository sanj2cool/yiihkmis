<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblProduct;

/**
 * TblProductSearch represents the model behind the search form of `app\models\TblProduct`.
 */
class TblProductSearch extends TblProduct
{
  public $crossRef;
  public $brands;
  public $has_image;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
      return [
          [['id', 'fk_category_id', 'quantity_in_stock', 'status', 'crt_by', 'mod_by', 'current_qty','default_tier','show_on_website'], 'integer'],
          [['name', 'description', 'sku', 'ip', 'crt_time', 'mod_time','crossRef','internal_sku','brands','has_image'], 'safe'],
          [['cost_price', 'default_price','tier_1_markup','tier_2_markup','tier_3_markup','tier_4_markup','tier_5_markup','tier_6_markup','tier_7_markup','tier_8_markup','tier_9_markup','tier_10_markup','tier_11_markup'], 'number'],
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
        $query = TblProduct::find()
        ->select([
              'tbl_product.*', // Select all product fields
              '(IFNULL((SELECT SUM(pr.no_of_items)
                      FROM tbl_product_receiving pr
                      WHERE pr.fk_product_id = tbl_product.id AND pr.status = 1 AND pr.fk_location_id = 1), 0)
              - IFNULL((SELECT SUM(pa.quantity)
                       FROM tbl_invoice_item pa
                       WHERE pa.fk_product_id = tbl_product.id AND pa.status = 1 AND pa.fk_invoice_id IN (SELECT id FROM tbl_invoice WHERE status = 1 and fk_bill_from_id = 1)), 0)) AS current_qty',
                       '(IFNULL((SELECT SUM(pr.no_of_items)
                               FROM tbl_product_receiving pr
                               WHERE pr.fk_product_id = tbl_product.id AND pr.status = 1 AND pr.fk_location_id = 2), 0)
                       - IFNULL((SELECT SUM(pa.quantity)
                                FROM tbl_invoice_item pa
                                WHERE pa.fk_product_id = tbl_product.id AND pa.status = 1 AND pa.fk_invoice_id IN (SELECT id FROM tbl_invoice WHERE status = 1 and fk_bill_from_id = 2)), 0)) AS current_qty_montreal'
              ])
                 ->joinWith('crossRef')
                 ->where('tbl_product.status != 0');
                 // ->groupBy('tbl_product.id');
        // add conditions that should always apply here
        $query->joinWith('brands');
        // $query->joinWith(['activeImages ai']);
        $query->distinct();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
            'defaultOrder' => [
                'id' => SORT_DESC, // Change 'your_column_name' to the column you want to sort by
            ],
        ],
        ]);
        // Add sorting configuration for 'current_qty'
        $dataProvider->sort->attributes['current_qty'] = [
            'asc' => ['current_qty' => SORT_ASC],
            'desc' => ['current_qty' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['current_qty_montreal'] = [
            'asc' => ['current_qty_montreal' => SORT_ASC],
            'desc' => ['current_qty_montreal' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['crossRef'] = [
                  'asc' => ['tbl_product_cross_ref.cross_ref' => SORT_ASC],
                  'desc' => ['tbl_product_cross_ref.cross_ref' => SORT_DESC],
              ];
      $dataProvider->sort->attributes['brands'] = [
                'asc' => ['tbl_brand.title' => SORT_ASC],
                'desc' => ['tbl_brand.title' => SORT_DESC],
            ];

            // Restore any saved state first
            $this->restoreSearchState();
        $this->load($params);


    // Filter by category_id if set
    if (!empty($this->fk_category_id)) {
        $query->andFilterWhere(['fk_category_id' => $this->fk_category_id]);
    }

      // --- Filter by has_image ---
      if ($this->has_image !== null && $this->has_image !== '') {
          $query->andWhere(
              $this->has_image == 1
                  ? 'EXISTS (SELECT 1 FROM tbl_product_image pi WHERE pi.fk_product_id = tbl_product.id AND pi.status = 1)'
                  : 'NOT EXISTS (SELECT 1 FROM tbl_product_image pi WHERE pi.fk_product_id = tbl_product.id AND pi.status = 1)'
          );
      }


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
            'fk_category_id' => $this->fk_category_id,
            'quantity_in_stock' => $this->quantity_in_stock,
            'cost_price' => $this->cost_price,
            'default_price' => $this->default_price,
            'tier_1_markup' => $this->tier_1_markup,
            'tier_2_markup' => $this->tier_2_markup,
            'tier_3_markup' => $this->tier_3_markup,
            'tier_4_markup' => $this->tier_4_markup,
            'tier_5_markup' => $this->tier_5_markup,
            'tier_6_markup' => $this->tier_6_markup,
            'tier_7_markup' => $this->tier_7_markup,
            'tier_8_markup' => $this->tier_8_markup,
            'tier_9_markup' => $this->tier_9_markup,
            'tier_10_markup' => $this->tier_10_markup,
            'tier_11_markup' => $this->tier_11_markup,
            'default_tier' => $this->default_tier,
            'show_on_website' => $this->show_on_website,
            'status' => $this->status,
            'crt_by' => $this->crt_by,
            'mod_by' => $this->mod_by,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tbl_product.description', $this->description])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'internal_sku', $this->internal_sku])
            ->andFilterWhere(['like', 'ip', $this->ip]);
            if($this->crossRef != ""){
                  $query->joinWith(['crossRef' => function ($q) {
                      $q->where('tbl_product_cross_ref.cross_ref LIKE "%' . $this->crossRef . '%"');
                  }]);
                }
                $query->andFilterWhere(['like', 'tbl_brand.title', $this->brands]);

        // --- Filter by has_image ---
        if ($this->has_image !== null && $this->has_image !== '') {
            $query->andWhere(
                $this->has_image == 1
                    ? 'EXISTS (SELECT 1 FROM tbl_product_image pi WHERE pi.fk_product_id = tbl_product.id AND pi.status = 1)'
                    : 'NOT EXISTS (SELECT 1 FROM tbl_product_image pi WHERE pi.fk_product_id = tbl_product.id AND pi.status = 1)'
            );
        }
        return $dataProvider;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $attributes['crossRef'] = $this->crossRef; // Include the virtual column
        $attributes['brands'] = $this->brands; // Include the virtual column
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
              $this->crossRef = $searchData['crossRef'] ?? null; // Restore the virtual column
              $this->brands = $searchData['brands'] ?? null; // Restore the virtual column
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
