<?php

use yii\db\Migration;

/**
 * Handles the creation for table `sessions`.
 */
class m160630_115952_create_sessions extends Migration
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

        $this->createTable('{{%sessions}}', [
            'id' => 'CHAR(64) NOT NULL PRIMARY KEY',
            'expire' => $this->integer()->notNull(),
            'data' => 'BLOB NOT NULL',
        ], $tableOptions);

        $this->createIndex('idx_expire', '{{%sessions}}', 'expire');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%sessions}}');
    }
}
