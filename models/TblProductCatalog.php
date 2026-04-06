<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;
/**
 * This is the model class for table "tbl_product_catalog".
 *
 * @property int $id
 * @property string $title
 * @property string $file_name
 * @property string $file_path
 * @property string|null $catalog_year
 * @property string|null $description
 * @property int|null $is_active
 * @property int $fk_location_id
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblProductCatalog extends \yii\db\ActiveRecord
{
    public $pdf_file; // upload instance
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_product_catalog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['is_active', 'fk_location_id', 'status'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['title', 'file_name', 'file_path'], 'string', 'max' => 255],
            [['catalog_year'], 'string', 'max' => 10],
            [['ip', 'crt_by'], 'string', 'max' => 40],
            [['mod_by'], 'string', 'max' => 50],
            // File validation
            [['pdf_file'], 'file', 'extensions' => 'pdf', 'skipOnEmpty' => false, 'maxSize' => 10 * 1024 * 1024], // 10MB
        ];
    }
    public function beforeSave($insert) {
        $session = Yii::$app -> session;
        if ($insert) {
          $this -> ip = Yii::$app -> getRequest() -> getUserIp();
          $this -> crt_by = $session -> get('userId');
          $this -> crt_time = date('Y-m-d H:i:s');
          $this -> status = 1;
          $this->fk_location_id = $session->get('userCompany');
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
            'file_name' => 'File Name',
            'file_path' => 'File Path',
            'catalog_year' => 'Catalog Year',
            'description' => 'Description',
            'is_active' => 'Is Active',
            'fk_location_id' => 'Fk Location ID',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
    public function upload()
    {
        if (!$this->pdf_file) {
            return false;
        }

        $uniqueName = time() . '_' . uniqid() . '.pdf';
        $path = Yii::getAlias('@webroot/catalog-files/' . $uniqueName);

        if ($this->pdf_file->saveAs($path)) {
            $this->file_name = $uniqueName;
            $this->file_path = '/catalog-files/' . $uniqueName;

            return true;
        }

        return false;
    }
}
