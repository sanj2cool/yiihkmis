<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\models\TblProduct;

class TblProductSearch extends TblProduct
{
    public $crossRef;
    public $brands;
    public $categories;
    public $has_image;
    public $current_qty;
    public $current_qty_montreal;
    public $assemblycat;
    public $latest_bin_brampton;
    public $latest_bin_montreal;

    public function rules()
    {
        return [
            [
                [
                    'id', 'fk_category_id', 'quantity_in_stock', 'status', 'crt_by', 'mod_by',
                    'current_qty', 'current_qty_montreal', 'default_tier', 'show_on_website'
                ],
                'integer'
            ],
            [
                [
                    'name', 'description', 'sku', 'ip', 'crt_time', 'mod_time',
                    'crossRef', 'internal_sku', 'brands','categories', 'has_image','assemblycat','latest_bin_brampton','latest_bin_montreal'
                ],
                'safe'
            ],
            [
                [
                    'cost_price', 'default_price', 'tier_1_markup', 'tier_2_markup', 'tier_3_markup',
                    'tier_4_markup', 'tier_5_markup', 'tier_6_markup', 'tier_7_markup', 'tier_8_markup',
                    'tier_9_markup', 'tier_10_markup', 'tier_11_markup'
                ],
                'number'
            ],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TblProduct::find()
            ->select([
                'tbl_product.*',

                // Brampton current qty
                '(IFNULL((SELECT SUM(pr.no_of_items)
                          FROM tbl_product_receiving pr
                          WHERE pr.fk_product_id = tbl_product.id
                          AND pr.status = 1 AND pr.fk_location_id = 1), 0)
                 -
                 IFNULL((SELECT SUM(it.quantity)
                         FROM tbl_invoice_item it
                         WHERE it.fk_product_id = tbl_product.id
                         AND it.status = 1
                         AND it.fk_invoice_id IN (
                             SELECT id FROM tbl_invoice
                             WHERE status = 1 AND fk_bill_from_id = 1
                         )), 0)
                ) AS current_qty',

                // Montreal current qty
                '(IFNULL((SELECT SUM(pr.no_of_items)
                          FROM tbl_product_receiving pr
                          WHERE pr.fk_product_id = tbl_product.id
                          AND pr.status = 1 AND pr.fk_location_id = 2), 0)
                 -
                 IFNULL((SELECT SUM(it.quantity)
                         FROM tbl_invoice_item it
                         WHERE it.fk_product_id = tbl_product.id
                         AND it.status = 1
                         AND it.fk_invoice_id IN (
                             SELECT id FROM tbl_invoice
                             WHERE status = 1 AND fk_bill_from_id = 2
                         )), 0)
                ) AS current_qty_montreal',
                //----get the current bin location ----
                '(
                    SELECT pr.bin_location
                    FROM tbl_product_receiving pr
                    WHERE pr.fk_product_id = tbl_product.id
                      AND pr.status = 1
                      AND pr.fk_location_id = 1
                    ORDER BY pr.crt_time DESC
                    LIMIT 1
                ) AS latest_bin_brampton',

                '(
                    SELECT pr.bin_location
                    FROM tbl_product_receiving pr
                    WHERE pr.fk_product_id = tbl_product.id
                      AND pr.status = 1
                      AND pr.fk_location_id = 2
                    ORDER BY pr.crt_time DESC
                    LIMIT 1
                ) AS latest_bin_montreal',
            ])
            ->joinWith('crossRef')
            ->joinWith('brands')
            ->joinWith('categories')
            ->joinWith('assemblycat')
            ->where('tbl_product.status != 0')
            ->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'name',
                    'sku',
                    'internal_sku',
                    'fk_category_id',
                    'current_qty',
                    'current_qty_montreal',
                    'latest_bin_brampton',
                    'latest_bin_montreal',
                    'description',
                    'crossRef' => [
                        'asc' => ['tbl_product_cross_ref.cross_ref' => SORT_ASC],
                        'desc' => ['tbl_product_cross_ref.cross_ref' => SORT_DESC],
                    ],
                    'brands' => [
                        'asc' => ['tbl_brand.title' => SORT_ASC],
                        'desc' => ['tbl_brand.title' => SORT_DESC],
                    ],
                    'categories' => [
                        'asc' => ['tbl_product_category.title' => SORT_ASC],
                        'desc' => ['tbl_product_category.title' => SORT_DESC],
                    ],
                    'assemblycat' => [
                        'asc' => ['tbl_product_assembly_category.title' => SORT_ASC],
                        'desc' => ['tbl_product_assembly_category.title' => SORT_DESC],
                    ],
                ]
            ],
        ]);

        // Restore saved filters first
        $this->restoreSearchState();

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Save current search state
        $this->saveSearchState();

        // Normal WHERE filters
        $query->andFilterWhere([
            'tbl_product.id' => $this->id,
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

        $query->andFilterWhere(['like', 'tbl_product.name', $this->name])
            ->andFilterWhere(['like', 'tbl_product.description', $this->description])
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'internal_sku', $this->internal_sku])
            ->andFilterWhere(['like', 'ip', $this->ip]);
        $query->andFilterWhere(['like', 'tbl_product_assembly_category.title', $this->assemblycat]);
        // Filter cross references
        if (!empty($this->crossRef)) {
            $query->joinWith(['crossRef' => function ($q) {
                $q->andWhere('tbl_product_cross_ref.cross_ref LIKE "%' . $this->crossRef . '%"');
            }]);
        }

        // Filter brands
        $query->andFilterWhere(['like', 'tbl_brand.title', $this->brands]);
        $query->andFilterWhere(['like', 'tbl_product_category.title', $this->categories]);

        // Filter has image
        if ($this->has_image !== null && $this->has_image !== '') {
            $query->andWhere(
                $this->has_image == 1
                    ? 'EXISTS (SELECT 1 FROM tbl_product_image pi WHERE pi.fk_product_id = tbl_product.id AND pi.status = 1)'
                    : 'NOT EXISTS (SELECT 1 FROM tbl_product_image pi WHERE pi.fk_product_id = tbl_product.id AND pi.status = 1)'
            );
        }

        // ------------------------------
        // NEW — Qty searching (using HAVING)
        // ------------------------------
        if ($this->current_qty !== null && $this->current_qty !== '') {
            $query->andHaving(['current_qty' => $this->current_qty]);
        }

        if ($this->current_qty_montreal !== null && $this->current_qty_montreal !== '') {
            $query->andHaving(['current_qty_montreal' => $this->current_qty_montreal]);
        }
        if ($this->latest_bin_brampton != '') {
            $query->andHaving(['like', 'latest_bin_brampton', $this->latest_bin_brampton]);
        }

        if ($this->latest_bin_montreal != '') {
            $query->andHaving(['like', 'latest_bin_montreal', $this->latest_bin_montreal]);
        }


        return $dataProvider;
    }

    // Save state
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;

        $attributes['crossRef'] = $this->crossRef;
        $attributes['brands'] = $this->brands;
        $attributes['categories'] = $this->categories;
        $attributes['assemblycat'] = $this->assemblycat;

        $session->set($this->formName() . '_search', $attributes);

        if ($sort = Yii::$app->request->get('sort')) {
            $session->set($this->formName() . '_sort', $sort);
        }
    }

    // Restore state
    public function restoreSearchState()
    {
        $session = Yii::$app->session;
        $saved = $session->get($this->formName() . '_search');

        if ($saved) {
            $this->attributes = $saved;
            $this->crossRef = $saved['crossRef'] ?? null;
            $this->brands = $saved['brands'] ?? null;
            $this->categories = $saved['categories'] ?? null;
            $this->assemblycat = $saved['assemblycat'] ?? null;
        }

        if (!Yii::$app->request->get('sort')) {
            if ($sort = $session->get($this->formName() . '_sort')) {
                Yii::$app->request->setQueryParams(
                    array_merge(Yii::$app->request->queryParams, ['sort' => $sort])
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
