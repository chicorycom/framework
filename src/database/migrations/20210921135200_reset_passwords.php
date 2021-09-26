<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ResetPasswords extends AbstractMigration
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
        $table = $this->table('a_s_reset_passwords');

        $table->addColumn('key', 'string', ['limit' => 250])
            ->addColumn('admin_id', 'integer', ['limit' => 128])
            ->addTimestamps()
            ->create();
    }
}
