<?php

use yii\db\Migration;

/**
 * Handles the creation for table `flows`.
 */
class m160630_122830_create_flows extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%flows}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'operation_id' => $this->integer()->notNull(),
            'begin' => $this->decimal(20, 2)->notNull(),
            'debit' => $this->decimal(10, 2)->notNull(),
            'credit' => $this->decimal(10, 2)->notNull(),
            'end' => $this->decimal(20, 2)->notNull(),
            'datetime' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_user_datetime', '{{%flows}}', ['user_id', 'datetime']);

        $this->addForeignKey('fk-flows-user_id', '{{%flows}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-flows-operation_id', '{{%flows}}', 'operation_id', '{{%operation}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%flows}}');
    }
}
