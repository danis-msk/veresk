<?php

use yii\db\Migration;

/**
 * Class m230716_140110_veresk
 */
class m230716_140110_veresk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('transactions', [
            'id' => $this->primaryKey(),
            'date' => $this->integer(),
            'amount' => $this->integer(),
        ]);

        $data = [
            [1, 1, 1],
            [2, 2, 2],
            [6, 3, -1],
            [8, 4, 2],
            [7, 5, -6],
            [9, 6, 1],
            [3, 7, -5],
            [4, 8, 1],
            [5, 9, 7],
        ];

        $this->batchInsert('transactions', ['id', 'date', 'amount'], $data);
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230716_140110_veresk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230716_140110_veresk cannot be reverted.\n";

        return false;
    }
    */
}
