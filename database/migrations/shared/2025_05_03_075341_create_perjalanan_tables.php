<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Simpan di salah satu database (contoh: admin)
        DB::connection('mysql_shared')->getSchemaBuilder()->create('PERJALANAN', function (Blueprint $table) {
            $table->id('perjalanan_id');
            $table->unsignedBigInteger('user_id'); // Relasi ke PENUMPANG di database user
            $table->unsignedBigInteger('driver_id'); // Relasi ke DRIVER di database driver
            $table->decimal('lokasi_jemput_lat', 10, 8);
            $table->decimal('lokasi_jemput_long', 11, 8);
            $table->text('alamat_jemput');
            $table->decimal('lokasi_tujuan_lat', 10, 8);
            $table->decimal('lokasi_tujuan_long', 11, 8);
            $table->text('alamat_tujuan');
            $table->decimal('jarak_km', 8, 2);
            $table->integer('durasi_estimasi_menit');
            $table->decimal('harga_dasar', 12, 2);
            $table->decimal('biaya_platform', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->unsignedBigInteger('promosi_id')->nullable();
            $table->decimal('harga_final', 12, 2);
            $table->dateTime('waktu_pesan');
            $table->dateTime('waktu_jemput')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->enum('status', [
                'mencari_driver',
                'driver_ditemukan',
                'menuju_penjemputan',
                'dalam_perjalanan',
                'selesai',
                'dibatalkan'
            ])->default('mencari_driver');
            $table->text('alasan_batal')->nullable();
            $table->timestamps();
        });

        DB::connection('mysql_shared')->getSchemaBuilder()->create('PEMBAYARAN', function (Blueprint $table) {
            $table->id('pembayaran_id');
            $table->foreignId('perjalanan_id')->constrained('PERJALANAN', 'perjalanan_id');
            $table->enum('metode', ['cash', 'e-wallet']);
            $table->unsignedBigInteger('metode_pembayaran_id')->nullable(); // Relasi ke METODE_PEMBAYARAN di database user
            $table->decimal('jumlah', 12, 2);
            $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->string('referensi_pembayaran')->nullable();
            $table->dateTime('waktu_pembayaran')->nullable();
            $table->timestamps();
        });

        DB::connection('mysql_shared')->getSchemaBuilder()->create('RATING', function (Blueprint $table) {
            $table->id('rating_id');
            $table->foreignId('perjalanan_id')->constrained('PERJALANAN', 'perjalanan_id');
            $table->unsignedBigInteger('user_id'); // Relasi ke PENUMPANG
            $table->unsignedBigInteger('driver_id'); // Relasi ke DRIVER
            $table->tinyInteger('nilai_rating')->unsigned()->between(1, 5);
            $table->text('deskripsi_review')->nullable();
            $table->enum('tipe', ['user_to_driver', 'driver_to_user']);
            $table->timestamps();
        });

        DB::connection('mysql_shared')->getSchemaBuilder()->create('TRACKING_PERJALANAN', function (Blueprint $table) {
            $table->id('tracking_id');
            $table->foreignId('perjalanan_id')->constrained('PERJALANAN', 'perjalanan_id');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->dateTime('waktu');
        });

        DB::connection('mysql_shared')->getSchemaBuilder()->create('NOTIFIKASI', function (Blueprint $table) {
            $table->id('notifikasi_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Relasi ke PENUMPANG
            $table->unsignedBigInteger('driver_id')->nullable(); // Relasi ke DRIVER
            $table->string('judul');
            $table->text('pesan');
            $table->enum('tipe', ['order', 'system', 'promo']);
            $table->boolean('dibaca')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::connection('mysql_shared')->getSchemaBuilder()->dropIfExists('NOTIFIKASI');
        DB::connection('mysql_shared')->getSchemaBuilder()->dropIfExists('TRACKING_PERJALANAN');
        DB::connection('mysql_shared')->getSchemaBuilder()->dropIfExists('RATING');
        DB::connection('mysql_shared')->getSchemaBuilder()->dropIfExists('PEMBAYARAN');
        DB::connection('mysql_shared')->getSchemaBuilder()->dropIfExists('PERJALANAN');
    }
};