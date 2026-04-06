<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_product_category".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int|null $parent_id
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property int|null $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblProductCategoryCopy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_product_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','show_on_website'], 'required'],
            [['description'], 'string'],
            [['parent_id', 'status', 'crt_by', 'mod_by','show_on_website','fk_assembly_category_id'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title','image_url'], 'string', 'max' => 100],
            [['ip'], 'string', 'max' => 40],
        ];
    }
    public function beforeSave($insert) {
        $session = Yii::$app -> session;
        if ($insert) {
          $this -> ip = Yii::$app -> getRequest() -> getUserIp();
          $this -> crt_by = $session -> get('userId');
          $this -> crt_time = date('Y-m-d H:i:s');
          $this -> status = 1;
        } else {
          $this -> ip = Yii::$app -> getRequest() -> getUserIp();
          $this -> mod_by = $session -> get('userId');
          $this -> mod_time = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
      }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'parent_id' => 'Parent Category',
            'fk_assembly_category_id' => 'Assembly Category',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
    // public function getProducts()
    // {
    //     return $this->hasMany(TblProduct::className(), ['fk_category_id' => 'id'])->where(['status' => 1]);
    // }
    public function getProducts()
    {
        return $this->hasMany(TblProduct::class, ['id' => 'fk_product_id'])
            ->viaTable(
                'tbl_product_assigned_category',
                ['fk_category_id' => 'id'],
                function($query) {
                    $query->andWhere(['tbl_product_assigned_category.status' => 1]);
                }
            )
            ->onCondition(['tbl_product.status' => 1]);
    }


    public function getProductCount()
    {
        return $this->getProducts()->count();
    }
    public function getIndentedTitle($categoryList = null, $level = 0)
    {
        // You can pass preloaded category list to avoid repeated DB calls
        if ($this->parent_id === null) {
            return str_repeat('&nbsp;&nbsp;&nbsp;', $level) . str_repeat('--', $level) . ' ' . ucwords(strtolower($this->title));
        }
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }

        return str_repeat('&nbsp;&nbsp;&nbsp;', $level) . str_repeat('--', $level) . ' ' . ucwords(strtolower($this->title));
    }
    public function getParent()
    {
        return $this->hasOne(TblProductCategory::class, ['id' => 'parent_id']);
    }
    public function getAssemblycat()
    {
        return $this->hasOne(TblProductAssemblyCategory::class, ['id' => 'fk_assembly_category_id']);
    }

}
