<?php

class m160918_124512_addDefaultValuesToUser extends CDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp() {
        $this->alterColumn('users', 'add_contact_info', "varchar(512) NOT NULL DEFAULT '' COMMENT 'Дополнительная контактная информация'");
        $this->alterColumn('users', 'terms_delivery', "text NULL COMMENT 'Условия передачи товара'");
        $this->alterColumn('users', 'balance', "decimal(19,4) NOT NULL DEFAULT 0.00 COMMENT 'Баланс'");
        $this->alterColumn('users', 'certified', "tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Проверенный продавец'");
        $this->alterColumn('users', 'ban', "tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Бан пользователя: 0 - нет, 1 - да'");
      }

    public function safeDown() {
        // ничего не нужно делать, нет смысла возвращать как было
        return true;
    }

}