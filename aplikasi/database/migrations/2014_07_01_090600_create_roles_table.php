<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 150);
            $table->timestamps();
        });

        DB::table('roles')->insert([
            'id' => 1,
            'role_name' => 'Adminisrator',
        ]);
        DB::table('roles')->insert([
            'id' => 2,
            'role_name' => 'Kepala Sekolah',
        ]);
        DB::table('roles')->insert([
            'id' => 3,
            'role_name' => 'Guru',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
