<?php

use yii\db\Migration;

class m260407_151309_add_extra_fields_to_product_receiving extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tbl_product_receiving', 'shipping', $this->decimal(10,2)->defaultValue(0));
        $this->addColumn('tbl_product_receiving', 'other_charges', $this->decimal(10,2)->defaultValue(0));
        $this->addColumn('tbl_product_receiving', 'custom_charges', $this->decimal(10,2)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->dropColumn('tbl_product_receiving', 'shipping');
        $this->dropColumn('tbl_product_receiving', 'other_charges');
        $this->dropColumn('tbl_product_receiving', 'custom_charges');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260407_151309_add_extra_fields_to_product_receiving cannot be reverted.\n";

        return false;
    }
    */
}
