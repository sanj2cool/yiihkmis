<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_product".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $fk_category_id
 * @property string $sku
 * @property int $quantity_in_stock
 * @property float $cost_price
 * @property float $default_price
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property int|null $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblProduct extends \yii\db\ActiveRecord
{
    public $current_qty;
    public $current_qty_montreal;
    // ADD THESE 2 NEW PROPERTIES
    public $latest_bin_brampton;
    public $latest_bin_montreal;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
          [['name','tier_1_markup','show_on_website'], 'required'],
          [['description','website_description'], 'string'],
          [['fk_category_id', 'quantity_in_stock', 'status', 'crt_by', 'mod_by','fk_cycle_count_category_id','default_tier','show_on_website','max_qty_in_stock','fk_assembly_category_id'], 'integer'],
          [['cost_price', 'default_price','tier_1_markup','tier_2_markup','tier_3_markup','tier_4_markup','tier_5_markup','tier_6_markup','tier_7_markup','tier_8_markup','tier_9_markup','tier_10_markup','tier_11_markup'], 'number'],
          [['crt_time', 'mod_time'], 'safe'],
          [['name','internal_sku'], 'string', 'max' => 100],
          [['sku'], 'string', 'max' => 50],
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
            'name' => 'Number',
            'crossRef' => 'Cross References',
            'description' => 'Description',
            'fk_category_id' => 'Category',
            'fk_cycle_count_category_id' => 'Cycle Count Category',
            'fk_assembly_category_id' => 'Assembly Category',
            'sku' => 'SKU',
            'internal_sku' => 'SKU',
            'quantity_in_stock' => 'Reorder Qty',
            'cost_price' => 'Cost Price',
            'default_price' => 'Default Price',
            'default_tier' => 'Default Tier',
            'tier_1_markup' => 'Tier 1 Markup %',
            'tier_2_markup' => 'Tier 2 Markup %',
            'tier_3_markup' => 'Tier 3 Markup %',
            'tier_4_markup' => 'Tier 4 Markup %',
            'tier_5_markup' => 'Tier 5 Markup %',
            'tier_6_markup' => 'Tier 6 Markup %',
            'tier_7_markup' => 'Tier 7 Markup %',
            'tier_8_markup' => 'Tier 8 Markup %',
            'tier_9_markup' => 'Tier 9 Markup %',
            'tier_10_markup' => 'Tier 10 Markup %',
            'tier_11_markup' => 'Tier 11 Markup %',
            'show_on_website' => 'Show on Website',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
    // This function will calculate the current quantity based on your logic
  public function getCurrentQty()
  {
    $query = (new Query())
        ->select([
            'p.id',
            '(IFNULL((SELECT SUM(pr.no_of_items)
                      FROM tbl_product_receiving pr
                      WHERE pr.fk_product_id = p.id AND pr.status = 1 AND pr.fk_location_id = 1), 0)
             - IFNULL((SELECT SUM(it.quantity)
                       FROM tbl_invoice_item it
                       WHERE it.fk_product_id = p.id AND it.status = 1 AND it.fk_invoice_id IN (SELECT id FROM tbl_invoice WHERE status = 1 and fk_bill_from_id = 1)), 0)) as current_qty'
        ])
        ->from('tbl_product p')
        ->where(['p.status' => 1,'p.id' => $this->id])->one();
      return isset($query['current_qty']) && $query['current_qty'] !== "" ? $query['current_qty'] : 0;
  }
  public function getCurrentQtyMontreal()
  {
    $query = (new Query())
        ->select([
            'p.id',
            '(IFNULL((SELECT SUM(pr.no_of_items)
                      FROM tbl_product_receiving pr
                      WHERE pr.fk_product_id = p.id AND pr.status = 1 AND pr.fk_location_id = 2), 0)
             - IFNULL((SELECT SUM(it.quantity)
                       FROM tbl_invoice_item it
                       WHERE it.fk_product_id = p.id AND it.status = 1 AND it.fk_invoice_id IN (SELECT id FROM tbl_invoice WHERE status = 1 and fk_bill_from_id = 2)), 0)) as current_qty_montreal'
        ])
        ->from('tbl_product p')
        ->where(['p.status' => 1,'p.id' => $this->id])->one();
      return isset($query['current_qty_montreal']) && $query['current_qty_montreal'] !== "" ? $query['current_qty_montreal'] : 0;
  }
  //---get the receiving latest bin location -----
  public function getLatestBinBrampton()
  {
      return $this->hasOne(TblProductReceiving::class, ['fk_product_id' => 'id'])
        ->andWhere(['status' => 1,'fk_location_id'=>1])
        ->orderBy(['crt_time' => SORT_DESC]);
  }
  public function getLatestBinMontreal()
  {
      return $this->hasOne(TblProductReceiving::class, ['fk_product_id' => 'id'])
        ->andWhere(['status' => 1,'fk_location_id'=>2])
        ->orderBy(['crt_time' => SORT_DESC]);
  }
  public function getCrossRef()
  {
      return $this->hasMany(TblProductCrossRef::className(), ['fk_product_id' => 'id'])->andOnCondition(['tbl_product_cross_ref.status'=>1]);
  }
  public function getProductBrands()
  {
      return $this->hasMany(TblProductBrand::className(), ['fk_product_id' => 'id'])
                  ->onCondition(['tbl_product_brand.status'=>1]);
  }

  public function getBrands()
  {
      return $this->hasMany(TblBrand::className(), ['id' => 'fk_brand_id'])
          ->via('productBrands');
  }

  public function getProductCategories()
  {
      return $this->hasMany(TblProductAssignedCategory::className(), ['fk_product_id' => 'id'])
                  ->onCondition(['tbl_product_assigned_category.status'=>1]);
  }

  public function getCategories()
  {
      return $this->hasMany(TblProductCategory::className(), ['id' => 'fk_category_id'])
          ->via('productCategories');
  }

  public function getProductImages()
  {
      return $this->hasMany(TblProductImage::class, ['fk_product_id' => 'id']);
  }
  public function getActiveImages()
  {
      return $this->hasMany(TblProductImage::class, ['fk_product_id' => 'id'])
          ->alias('ai')
          ->andWhere(['ai.status' => 1]);
  }
  public function getFirstImage()
  {
      return $this->hasOne(TblProductImage::class, ['fk_product_id' => 'id'])
        ->andWhere(['status' => 1])
        ->orderBy(['id' => SORT_ASC]);
  }
  public function getAssemblycat()
  {
      return $this->hasOne(TblProductAssemblyCategory::class, ['id' => 'fk_assembly_category_id']);
  }
}
