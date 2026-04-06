<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_bin_locations".
 *
 * @property int $id
 * @property string $area
 * @property string $row
 * @property string $bay
 * @property string $level
 * @property string $position
 * @property string $ip
 * @property int $status 1 active 2 blocked 0 inactive
 * @property string $crt_by
 * @property string $mod_by
 * @property string $crt_time
 * @property string|null $mod_time
 */
class TblBinLocations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_bin_locations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area', 'row', 'bay', 'level', 'position'], 'required'],
            [['status'], 'integer'],
            [['crt_time', 'mod_time'], 'safe'],
            [['area', 'row', 'bay', 'level', 'position'], 'string', 'max' => 100],
            [['ip', 'crt_by'], 'string', 'max' => 40],
            [['mod_by'], 'string', 'max' => 50],
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
            'area' => 'Area',
            'row' => 'Location',
            'bay' => 'Bay',
            'level' => 'Level',
            'position' => 'Position',
            'ip' => 'Ip',
            'status' => 'Status',
            'crt_by' => 'Crt By',
            'mod_by' => 'Mod By',
            'crt_time' => 'Crt Time',
            'mod_time' => 'Mod Time',
        ];
    }
}
