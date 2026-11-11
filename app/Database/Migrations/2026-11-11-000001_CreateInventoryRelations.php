<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryRelations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nip' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'position' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nip');
        $this->forge->createTable('pegawais', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 30],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'address' => ['type' => 'TEXT', 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('instansis', true);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kendaraan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pegawai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'instansi_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'assigned_at' => ['type' => 'DATE'],
            'returned_at' => ['type' => 'DATE', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['assigned', 'returned'], 'default' => 'assigned'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kendaraan_id', 'kendaraans', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('pegawai_id', 'pegawais', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('instansi_id', 'instansis', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('penempatan_kendaraan', true);
    }

    public function down()
    {
        $this->forge->dropTable('penempatan_kendaraan', true);
        $this->forge->dropTable('instansis', true);
        $this->forge->dropTable('pegawais', true);
    }
}
