<?php

use yii\db\Migration;

/**
 * Class m200731_170759_alter_table_kuna_deals_add_column_status
 */
class m200731_170759_alter_table_kuna_deals_add_column_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kuna_deals', 'status', $this->string()->defaultValue('unconditional'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

}
