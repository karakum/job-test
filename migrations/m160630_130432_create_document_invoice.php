<?php

use yii\db\Migration;

/**
 * Handles the creation for table `document_invoice`.
 */
class m160630_130432_create_document_invoice extends Migration
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
        $this->createTable('{{%document_invoice}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'payer_id' => $this->integer()->notNull(),
            'comment' => $this->string(),
            'value' => $this->decimal(10, 2)->notNull(),
            'datetime' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx_user_datetime', '{{%document_invoice}}', ['user_id', 'datetime']);
        $this->createIndex('idx_payer_datetime', '{{%document_invoice}}', ['payer_id', 'datetime']);

        $this->addForeignKey('fk-document_invoice-user_id', '{{%document_invoice}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('fk-document_invoice-payer_id', '{{%document_invoice}}', 'payer_id', '{{%users}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%document_invoice}}');
    }
}
