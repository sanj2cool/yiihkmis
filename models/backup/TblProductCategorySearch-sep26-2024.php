<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblProductCategory;
use yii\db\Query;
/**
 * TblProductCategorySearch represents the model behind the search form of `app\models\TblProductCategory`.
 */
class TblProductCategorySearch extends TblProductCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['title', 'description', 'ip', 'crt_time', 'mod_time'], 'safe'],
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
        $query = TblProductCategory::find()->where('c.status != 0')->alias('c');
        // Add a subquery to count products per category
           $query->select([
               'c.*',
               '(SELECT COUNT(*) FROM tbl_product p WHERE p.fk_category_id = c.id and p.status = 1) AS productCount'
           ]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        // Enable sorting for productCount
            $dataProvider->sort->attributes['productCount'] = [
                'asc' => ['productCount' => SORT_ASC],
                'desc' => ['productCount' => SORT_DESC],
            ];
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'crt_by' => $this->crt_by,
            'mod_by' => $this->mod_by,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'ip', $this->ip]);

        return $dataProvider;
    }
    private function getHierarchicalCategories($parent_id = null, $level = 0, $categories = [])
    {
        // Create a query to fetch categories
        $query = (new Query())
            ->select(['id', 'title', 'parent_id'])
            ->from('tbl_product_category')
            ->where(['parent_id' => $parent_id])
            ->orderBy(['title' => SORT_ASC])
            ->all();

        foreach ($query as $category) {
          // Count the products for the current category
       $productCount = (new Query())
           ->from('tbl_product')
           ->where(['fk_category_id' => $category['id'], 'status' => 1]) // Count products with status = 1
           ->count();
            // Add indentation for hierarchy and add to result array
            $categories[] = [
                'id' => $category['id'],
                'title' => str_repeat('&nbsp;&nbsp;&nbsp;', $level) . str_repeat('--', $level) . ' ' . $category['title'],
                'parent_id' => $category['parent_id'],
                'productCount' => $productCount, // Add product count
            ];

            // Recursively fetch child categories
            $categories = $this->getHierarchicalCategories($category['id'], $level + 1, $categories);
        }

        return $categories;
    }
}
