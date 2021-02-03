<?php

namespace App\Console\Commands\Log;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class logAutoVerval
{
    static function index($auto_verval_id, $sekolah_id,  $pengguna_id, $interval_minggu){
        $uuid = DB::connection('sqlsrv_pmp')
        ->table('log.auto_verval')
        ->insert([
            'auto_verval_id' => $auto_verval_id,
            'sekolah_id' => $sekolah_id,
            'tanggal' => DB::raw('getdate()'),
            'pengguna_id' => $pengguna_id,
            'interval_minggu' => $interval_minggu
        ]);

        if($uuid){
            echo "insert log auto verval berhasil".PHP_EOL;
        }else{
            echo "insert log auto verval gagal".PHP_EOL;
        }

        return date('Y-m-d H:i:s');
    }

    static function update($auto_verval_id, $waktu_mulai, $verval_total, $verval_insert, $verval_update, $verval_gagal, $timeline_3, $timeline_4){
        $date_mulai = strtotime($waktu_mulai);
        $date_selesai = strtotime(date('Y-m-d H:i:s'));

        $uuid = DB::connection('sqlsrv_pmp')
        ->table('log.auto_verval')
        ->where('auto_verval_id','=', $auto_verval_id)
        ->update([
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'durasi' => floor( abs($date_mulai - $date_selesai) ),
            'verval_total' => $verval_total,
            'verval_insert' => $verval_insert,
            'verval_update' => $verval_update,
            'verval_gagal' => $verval_gagal,
            'timeline_3' => $timeline_3,
            'timeline_4' => $timeline_4
            // 'durasi' => floor( abs($date_mulai - $date_selesai) / 60 )
        ]);

        if($uuid){
            echo "update log auto verval berhasil".PHP_EOL;
        }else{
            echo "update log auto verval gagal".PHP_EOL;
        }

        return true;
    }
}