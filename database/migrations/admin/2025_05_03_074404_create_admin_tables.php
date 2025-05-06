<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::connection('mysql_admin')->getSchemaBuilder()->create('ADMIN', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('no_telepon');
            $table->timestamps();
        });

        DB::connection('mysql_admin')->getSchemaBuilder()->create('AREA_OPERASIONAL', function (Blueprint $table) {
            $table->id('area_id');
            $table->string('nama_area');
            $table->text('deskripsi')->nullable();
            // $table->polygon('koordinat_area')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        DB::connection('mysql_admin')->getSchemaBuilder()->create('TARIF', function (Blueprint $table) {
            $table->id('tarif_id');
            $table->decimal('tarif_dasar', 10, 2);
            $table->decimal('tarif_per_km', 10, 2);
            $table->decimal('tarif_minimum', 10, 2);
            $table->decimal('biaya_platform', 10, 2);
            $table->dateTime('berlaku_sejak');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        DB::connection('mysql_admin')->getSchemaBuilder()->create('TIKET_BANTUAN', function (Blueprint $table) {
            $table->id('tiket_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('subjek');
            $table->text('deskripsi');
            $table->enum('status', ['open', 'in-progress', 'closed'])->default('open');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
        });

        DB::connection('mysql_admin')->getSchemaBuilder()->create('TIKET_RESPON', function (Blueprint $table) {
            $table->id('respon_id');
            $table->foreignId('tiket_id')->constrained('TIKET_BANTUAN', 'tiket_id');
            $table->unsignedBigInteger('admin_id');
            $table->text('pesan');
            $table->timestamps();
        });

        DB::connection('mysql_admin')->getSchemaBuilder()->create('PROMOSI', function (Blueprint $table) {
            $table->id('promosi_id');
            $table->string('judul');
            $table->text('deskripsi');
            $table->decimal('nilai_diskon', 10, 2);
            $table->enum('tipe_diskon', ['persentase', 'nominal']);
            $table->string('kode_promo')->unique();
            $table->dateTime('berlaku_mulai');
            $table->dateTime('berlaku_sampai');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::connection('mysql_admin')->getSchemaBuilder()->dropIfExists('PROMOSI');
        DB::connection('mysql_admin')->getSchemaBuilder()->dropIfExists('TIKET_RESPON');
        DB::connection('mysql_admin')->getSchemaBuilder()->dropIfExists('TIKET_BANTUAN');
        DB::connection('mysql_admin')->getSchemaBuilder()->dropIfExists('TARIF');
        DB::connection('mysql_admin')->getSchemaBuilder()->dropIfExists('AREA_OPERASIONAL');
        DB::connection('mysql_admin')->getSchemaBuilder()->dropIfExists('ADMIN');
    }
};