<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::connection('mysql_user')->getSchemaBuilder()->create('PENUMPANG', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('no_telepon');
            $table->string('password');
            $table->string('foto_profil')->nullable();
            $table->boolean('verified')->default(false);
            $table->decimal('saldo_ewallet', 12, 2)->default(0);
            $table->timestamps();
        });

        DB::connection('mysql_user')->getSchemaBuilder()->create('ALAMAT_FAVORIT', function (Blueprint $table) {
            $table->id('alamat_id');
            $table->foreignId('user_id')->constrained('PENUMPANG', 'user_id');
            $table->string('nama_alamat', 100);
            $table->text('alamat_lengkap');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });

        DB::connection('mysql_user')->getSchemaBuilder()->create('METODE_PEMBAYARAN', function (Blueprint $table) {
            $table->id('metode_id');
            $table->foreignId('user_id')->constrained('PENUMPANG', 'user_id');
            $table->enum('tipe', ['e-wallet', 'kartu']);
            $table->string('provider');
            $table->string('nomor_akun');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->boolean('utama')->default(false);
            $table->timestamps();
        });

        DB::connection('mysql_user')->getSchemaBuilder()->create('TOPUP_SALDO', function (Blueprint $table) {
            $table->id('topup_id');
            $table->foreignId('user_id')->constrained('PENUMPANG', 'user_id');
            $table->decimal('jumlah', 12, 2);
            $table->string('metode');
            $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->string('referensi_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::connection('mysql_user')->getSchemaBuilder()->dropIfExists('TOPUP_SALDO');
        DB::connection('mysql_user')->getSchemaBuilder()->dropIfExists('METODE_PEMBAYARAN');
        DB::connection('mysql_user')->getSchemaBuilder()->dropIfExists('ALAMAT_FAVORIT');
        DB::connection('mysql_user')->getSchemaBuilder()->dropIfExists('PENUMPANG');
    }
};