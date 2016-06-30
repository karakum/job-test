<?php

use yii\db\Migration;

/**
 * Handles the creation for table `operation`.
 */
class m160630_121740_create_operation extends Migration
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
        $this->createTable('{{%operation}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'document_id' => $this->integer()->notNull(),
            'document_type' => $this->smallInteger()->notNull(),
            'value' => $this->decimal(10, 2)->notNull(),
            'datetime' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_user_datetime', '{{%operation}}', ['user_id', 'datetime']);

        $this->addForeignKey('fk-operation-user_id', '{{%operation}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%operation}}');
    }
}
