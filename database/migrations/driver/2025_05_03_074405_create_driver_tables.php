<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::connection('mysql_driver')->getSchemaBuilder()->create('DRIVER', function (Blueprint $table) {
            $table->id('driver_id');
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('no_telepon');
            $table->string('password');
            $table->string('foto_profil')->nullable();
            $table->string('nomor_ktp')->unique();
            $table->float('rating_rata_rata', 3, 2)->default(0);
            $table->integer('jumlah_perjalanan')->default(0);
            $table->enum('status_verifikasi', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->enum('status_aktif', ['online', 'offline'])->default('offline');
            $table->decimal('saldo_penghasilan', 12, 2)->default(0);
            $table->decimal('latitude_terakhir', 10, 8)->nullable();
            $table->decimal('longitude_terakhir', 11, 8)->nullable();
            $table->dateTime('last_online')->nullable();
            $table->timestamps();
        });

        DB::connection('mysql_driver')->getSchemaBuilder()->create('KENDARAAN', function (Blueprint $table) {
            $table->id('kendaraan_id');
            $table->foreignId('driver_id')->constrained('DRIVER', 'driver_id');
            $table->string('nomor_polisi')->unique();
            $table->string('nomor_rangka')->unique();
            $table->string('merk');
            $table->string('model');
            $table->string('warna');
            $table->year('tahun_produksi');
            $table->string('foto_kendaraan')->nullable();
            $table->timestamps();
        });

        DB::connection('mysql_driver')->getSchemaBuilder()->create('DOKUMEN_DRIVER', function (Blueprint $table) {
            $table->id('dokumen_id');
            $table->foreignId('driver_id')->constrained('DRIVER', 'driver_id');
            $table->enum('jenis_dokumen', ['KTP', 'SIM', 'STNK', 'SKCK']);
            $table->string('nomor_dokumen');
            $table->string('file_dokumen');
            $table->enum('status_verifikasi', ['pending', 'valid', 'invalid'])->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
        });

        DB::connection('mysql_driver')->getSchemaBuilder()->create('PENARIKAN_DANA', function (Blueprint $table) {
            $table->id('penarikan_id');
            $table->foreignId('driver_id')->constrained('DRIVER', 'driver_id');
            $table->decimal('jumlah', 12, 2);
            $table->string('bank_tujuan');
            $table->string('nomor_rekening');
            $table->string('nama_pemilik');
            $table->enum('status', ['pending', 'diproses', 'berhasil', 'gagal'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::connection('mysql_driver')->getSchemaBuilder()->dropIfExists('PENARIKAN_DANA');
        DB::connection('mysql_driver')->getSchemaBuilder()->dropIfExists('DOKUMEN_DRIVER');
        DB::connection('mysql_driver')->getSchemaBuilder()->dropIfExists('KENDARAAN');
        DB::connection('mysql_driver')->getSchemaBuilder()->dropIfExists('DRIVER');
    }
};