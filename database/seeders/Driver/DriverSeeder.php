<?php

namespace Database\Seeders\Driver;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Driver\Driver;
use App\Models\Driver\DokumenDriver;
use Illuminate\Support\Str;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        // driver____________________________________________
        Driver::create([
            'nama_lengkap' => 'John Doe',
            'email' => 'johndoe@example.com',
            'no_telepon' => '081234567890',
            'password' => Hash::make('password123'),
            'nomor_ktp' => '1234567890123456',
            'status_aktif' => 'offline',
            'latitude_terakhir' => '-7.7828',
            'longitude_terakhir' => '110.3671',
        ]);

        // dokumen driver______________________________________________
        $driverId = \App\Models\Driver\Driver::first()?->driver_id;

        if (!$driverId) {
            $this->command->warn('Tidak ada driver di database. Seeder DokumenDriver dilewati.');
            return;
        }

        $dokumenList = ['KTP', 'SIM', 'STNK', 'SKCK'];

        foreach ($dokumenList as $jenis) {
            DokumenDriver::create([
                'driver_id' => $driverId,
                'jenis_dokumen' => $jenis,
                'nomor_dokumen' => strtoupper(Str::random(12)),
                'file_dokumen' => 'dokumen_driver/contoh_' . strtolower($jenis) . '.pdf',
                'status_verifikasi' => 'pending',
            ]);
        }
    }
}
