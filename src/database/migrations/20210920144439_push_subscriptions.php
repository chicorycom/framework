<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PushSubscriptions extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('a_s_push_subscriptions');

        $table->addColumn('subscribable_id', 'integer')
            ->addColumn('subscribable_type', 'string')
             //->addForeignKey('subscribable', 'a_s_admin', 'id')
            ->addColumn('endpoint', 'string', ['limit' => 500])
            ->addColumn('public_key', 'string', ['null' => true])
            ->addColumn('auth_token', 'string', ['null' => true])
            ->addColumn('content_encoding', 'string', ['null' => true])
            ->addIndex(['endpoint'], ['unique' => true])
            ->addTimestamps()
            ->create();
    }
}
