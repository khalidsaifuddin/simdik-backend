<?php

namespace App\Console\Commands\Log;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class logRekapSekolah
{
    static function index($log_rekap_id, $kode_wilayah, $induk_log_rekap_id = null, $jenjang="dikdasmen"){
        // return "oke";
        $uuid = DB::connection('sqlsrv_admin_sa_nufaza')
        ->table('log.log_rekap')
        ->insert([
            'log_rekap_id' => $log_rekap_id,
            'tanggal' => date('Y-m-d H:i:s'),
            'waktu_mulai' => date('Y-m-d H:i:s'),
            'waktu_selesai' => null,
            'kode_wilayah' => $kode_wilayah,
            'durasi' => 0,
            'jumlah_sekolah_total' => 0,
            'jumlah_sekolah_update' => 0,
            'jumlah_sekolah_insert' => 0,
            'jumlah_sekolah_gagal' => 0,
            'induk_log_rekap_id' => $induk_log_rekap_id,
            'jenjang' => $jenjang
        ]);

        if($uuid){
            echo "insert log berhasil".PHP_EOL;
        }else{
            echo "insert log gagal".PHP_EOL;
        }

        return date('Y-m-d H:i:s');
    }

    static function rekapGagal($log_rekap_id, $kode_wilayah, $sekolah_id, $pesan_error){
        // return "oke";
        $uuid = DB::connection('sqlsrv_admin_sa_nufaza')
        ->table('log.log_rekap_gagal')
        ->insert([
            'log_rekap_gagal_id' => DB::raw('newid()'),
            'log_rekap_id' => $log_rekap_id,
            'tanggal' => date('Y-m-d H:i:s'),
            'sekolah_id' => $sekolah_id,
            'kode_wilayah' => $kode_wilayah,
            'pesan_error' => $pesan_error
        ]);

        return date('Y-m-d H:i:s');
    }

    static function update($log_rekap_id, $waktu_mulai, $log_total, $log_update, $log_insert, $log_gagal){
        $date_mulai = strtotime($waktu_mulai);
        $date_selesai = strtotime(date('Y-m-d H:i:s'));

        $uuid = DB::connection('sqlsrv_admin_sa_nufaza')
        ->table('log.log_rekap')
        ->where('log_rekap_id','=', $log_rekap_id)
        ->update([
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'durasi' => floor( abs($date_mulai - $date_selesai) / 60 ),
            'jumlah_sekolah_total' => $log_total,
            'jumlah_sekolah_update' => $log_update,
            'jumlah_sekolah_insert' => $log_insert,
            'jumlah_sekolah_gagal' => $log_gagal
        ]);

        if($uuid){
            echo "update log berhasil".PHP_EOL;
        }else{
            echo "update log gagal".PHP_EOL;
        }

        return true;
    }
}