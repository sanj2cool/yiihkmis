<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\TblProductCategoryCopy;
use yii\db\Query;
/**
 * TblProductCategorySearch represents the model behind the search form of `app\models\TblProductCategory`.
 */
class TblProductCategoryCopySearch extends TblProductCategory
{
  public $productCount;
  public $has_image;
  public $assemblycat;
  // public $product_count;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'status', 'crt_by', 'mod_by','show_on_website'], 'integer'],
            [['title', 'description', 'ip', 'crt_time', 'mod_time','productCount','has_image','image_url','assemblycat'], 'safe'],
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
      $query = TblProductCategory::find()
            ->alias('c')
            ->select([
                'c.*',
                '(SELECT COUNT(*) FROM tbl_product p WHERE p.fk_category_id = c.id AND p.status = 1) AS productCount'
            ])
            ->where(['!=', 'c.status', 0]); // Exclude deleted/inactive if needed
        // $query->joinWith('assemblycat');
        // $query->distinct();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'title',
                    'description',
                    'parent_id',
                    'show_on_website',
                    'productCount' => [
                        'asc' => ['productCount' => SORT_ASC],
                        'desc' => ['productCount' => SORT_DESC],
                    ],
                    // 'assemblycat' => [
                    //   'asc' => ['tbl_product_assembly_category.title' => SORT_ASC],
                    //   'desc' => ['tbl_product_assembly_category.title' => SORT_DESC],
                    // ]
                ]
            ]
        ]);
        // Restore any saved state first
        $this->restoreSearchState();

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1'); // No results if validation fails
            return $dataProvider;
        }

        // Save the current state after loading and validating
        $this->saveSearchState();

        $query->andFilterWhere(['c.id' => $this->id]);
        $query->andFilterWhere(['c.parent_id' => $this->parent_id]);
        $query->andFilterWhere(['c.status' => $this->status]);
        $query->andFilterWhere(['c.show_on_website' => $this->show_on_website]);
        $query->andFilterWhere(['like', 'c.title', $this->title]);
        $query->andFilterWhere(['like', 'c.description', $this->description]);
        // $query->andFilterWhere(['like', 'tbl_product_assembly_category.title', $this->assemblycat]);
        // --- Filter by has_image ---
        // --- Filter by has_image ---
        if ($this->has_image !== null && $this->has_image !== '') {
            if ($this->has_image == 1) {
                // Image exists
                $query->andWhere(['and', ['!=', 'image_url', ''], ['status' => 1]]);
            } else {
                // No image
                $query->andWhere(['or', ['image_url' => null], ['image_url' => '']]);
            }
        }
        if ($this->productCount !== null && $this->productCount !== '') {
            $query->having(['productCount' => $this->productCount]);
        }


        return $dataProvider;
    }
    private function getHierarchicalCategories($parent_id = null, $level = 0, $categories = [])
    {
        // Create a query to fetch categories
        $query = (new Query())
            ->select(['id', 'title', 'parent_id','description','show_on_website', 'status'])
            ->from('tbl_product_category')
            ->where(['parent_id' => $parent_id])
            ->andWhere('status != 0')
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
                'description' => $category['description'],
                'show_on_website' => $category['show_on_website'],
                'status' => $category['status'],
                'productCount' => $productCount, // Add product count
            ];

            // Recursively fetch child categories
            $categories = $this->getHierarchicalCategories($category['id'], $level + 1, $categories);
        }

        return $categories;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $attributes['productCount'] = $this->productCount; // Include the virtual column
        // $attributes['assemblycat'] = $this->assemblycat; // Include the virtual column
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
              $this->productCount = $searchData['productCount'] ?? null; // Restore the virtual column
              // $this->assemblycat = $searchData['assemblycat'] ?? null; // Restore the virtual column
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
