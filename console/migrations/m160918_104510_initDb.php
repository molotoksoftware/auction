<?php

class m160918_104510_initDb extends CDbMigration
{

    public function safeUp() {
        $sql = file_get_contents(dirname(dirname(dirname(__FILE__))).'/common/data/auction.sql');
        $sql = explode('CREATE TABLE', $sql);
        $this->execute($sql[0]);
        for($i = 1; $i < count($sql); $i++) {
            $sql[$i] = 'CREATE TABLE'.$sql[$i];
            $this->execute($sql[$i]);
        }
    }

    public function safeDown() {
        $tables = array_diff(Yii::app()->db->schema->tableNames, ['tbl_migration']);
        $this->execute('SET FOREIGN_KEY_CHECKS=0; DROP TABLE '.implode(', ', $tables).'; SET FOREIGN_KEY_CHECKS=1;');
    }

}