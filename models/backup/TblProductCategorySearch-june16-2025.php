<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
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
            [['id', 'parent_id', 'status', 'crt_by', 'mod_by','show_on_website'], 'integer'],
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
          // Get the hierarchical categories
        $categories = $this->getHierarchicalCategories();

        // Create an ArrayDataProvider for the GridView
        $dataProvider = new ArrayDataProvider([
            'allModels' => $categories,
            'pagination' => [
                'pageSize' => 20, // Adjust the page size as needed
            ],
            'sort' => [
              'defaultOrder' => [
                  'id' => SORT_DESC, // Set default sorting
              ],
                'attributes' => [
                    'title',          // Allow sorting by name
                    'description',          // Allow sorting by name
                    'id',            // Allow sorting by id
                    'parent_id',     // Allow sorting by parent_id
                    'productCount',   // Allow sorting by product count
                    'show_on_website'
                    // Add other attributes as needed
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Build a temporary array for filtering
        // Initialize a filtered categories array
        $filteredCategories = [];

        foreach ($categories as $category) {
            // Check if the category matches the filters
            $match = true; // Assume a match unless proven otherwise

            if ($this->id !== null && $this->id !== '') {
                $match = $match && ($category['id'] == $this->id);
            }

            if ($this->parent_id !== null && $this->parent_id !== '') {
                // $match = $match && ($category['parent_id'] == $this->parent_id);
                $match = $match && ((int)$category['parent_id'] === (int)$this->parent_id);

            }

            if ($this->status !== null && $this->status !== '') {
                $match = $match && ($category['status'] == $this->status);
            }
            if ($this->show_on_website !== null) {
                // You might need to define how to check status
                // Adjust the logic based on your category structure if needed
                // For instance, if status is an attribute of categories, check it
                $match = $match && ($category['show_on_website'] == $this->show_on_website);
            }

            // Check if the title filter matches (if applicable)
            if (!empty($this->title)) {
                $match = $match && (stripos($category['title'], $this->title) !== false);
            }

            // Check if the description filter matches (if applicable)
            if (!empty($this->description)) {
                $match = $match && (stripos($category['description'], $this->description) !== false);
            }

            // If the category matches all filters, add it to the filtered results
            if ($match) {
                $filteredCategories[] = $category;
            }
        }

      // Update the dataProvider with filtered results
      $dataProvider->allModels = $filteredCategories;

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
}
