<?php

use yii\db\Migration;

/**
 * Class m200414_135438_create_table_kuna_code_bot
 */
class m200414_135438_create_table_kuna_code_bot extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('kuna_code_bot',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(),
                'bank' => $this->string(),
                'max_percent' => $this->float(),
                'available_sum' => $this->float(),
                'min_sum' => $this->float(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('kuna_code_bot');
    }

}
