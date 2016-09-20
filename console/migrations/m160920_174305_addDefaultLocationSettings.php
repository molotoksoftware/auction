<?php

class m160920_174305_addDefaultLocationSettings extends CDbMigration
{

    public function safeUp() {
        $this->insert('setting', [
            'name' => 'defaultLocationLock',
            'title' => 'Зафиксировать местоположение по умолчанию',
            'type' => Setting::TYPE_COMMON,
            'type_field' => Setting::TYPE_FIELD_CHECK_BOX,
            'value' => '0',
            'preload' => '1',
            'sort' => 90,
        ]);
        $this->insert('setting', [
            'name' => 'defaultLocation',
            'title' => 'Местоположение по умолчанию',
            'type' => Setting::TYPE_COMMON,
            'type_field' => Setting::TYPE_FIELD_LOCATION,
            'value' => '',
            'preload' => '1',
            'sort' => 91,
        ]);
    }

    public function safeDown() {
        $this->delete('setting', 'name LIKE :name', [
            ':name' => 'defaultLocation%',
        ]);
    }

}