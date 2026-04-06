<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_product_assembly_category".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $image_url
 * @property int $show_on_website 1- yes, 2- no
 * @property int|null $img_width
 * @property int|null $translate_x
 * @property int|null $translate_y
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property int $crt_by
 * @property int|null $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblProductAssemblyCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_product_assembly_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['show_on_website', 'img_width', 'translate_x', 'translate_y', 'status', 'crt_by', 'mod_by','parent_id'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title', 'image_url'], 'string', 'max' => 100],
            [['exploded_image_url'], 'string', 'max' => 255],
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
            'image_url' => 'Image',
            'exploded_image_url' => 'Second Level Image',
            'show_on_website' => 'Show On Website',
            'img_width' => 'Img Width',
            'translate_x' => 'Translate X',
            'translate_y' => 'Translate Y',
            'parent_id' => 'Parent Category',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
}
