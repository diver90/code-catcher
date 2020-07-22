<?php

use yii\db\Migration;

/**
 * Class m200407_185814_create_table_kuna_code_deals
 */
class m200407_185814_create_table_kuna_deals extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('kuna_deals', [
            "order_id" => $this->string(),
            "amount" => $this->float(),
            "percent" => $this->float(),
            "price" => $this->float(),
            "bank" => $this->string(),
            "executed" => $this->boolean()->defaultValue(false),
            "created_at" => $this->integer()->notNull(),
            "updated_at" => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('kuna_deals_pk', 'kuna_deals', 'order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
