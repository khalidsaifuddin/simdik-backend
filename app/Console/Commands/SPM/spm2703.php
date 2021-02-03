<?php

namespace App\Console\Commands\SPM;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class spm2703
{
    static function index($kode_wilayah, $semester_id){
        $sql_2703 = "
        SELECT
            sekolah.sekolah_id,
            '".$semester_id."' AS semester_id,
            '2703' AS kode_instrumen_spm,
            1 AS target,
            1 AS capaian,
            1 - 1 AS gap,
            (
            CASE	
                WHEN ( ROUND( ( 1 / CAST ( 1 AS FLOAT ) * 100 ), 2 ) ) = 100 THEN
                'Tercapai' ELSE 'Belum Tercapai' 
            END 
            ) AS predikat,
            ROUND( ( 1 / CAST ( 1 AS FLOAT ) * 100 ), 2 ) AS persen,
            'Komite' as satuan 
        FROM
            sekolah
            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
            JOIN ref.bentuk_pendidikan bp WITH ( nolock ) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
        WHERE
            soft_delete = 0 
        AND sekolah.bentuk_pendidikan_id IN ( 5, 6, 9, 10, 53, 54 ) 
        AND kab.kode_wilayah = '".$kode_wilayah."'
        ";

        $fetch = DB::connection('sqlsrv')->select($sql_2703);

        for ($iFetch=0; $iFetch < sizeof($fetch); $iFetch++) { 
            // echo "[INF] ".$kode_wilayah.' - '.$fetch[$iFetch]->sekolah_id.PHP_EOL;
            
            $sql_check = "
            select 
                * 
            from 
                spm.spm_sekolah 
            where 
                sekolah_id = '".$fetch[$iFetch]->sekolah_id."' 
            and semester_id = '".$fetch[$iFetch]->semester_id."' 
            and kode_instrumen_spm = '".$fetch[$iFetch]->kode_instrumen_spm."'
            ";
            
            $fetch_check = DB::connection('sqlsrv_spm')->select($sql_check);
            
            // echo "[INF] ".sizeof($fetch_check).PHP_EOL;

            if(sizeof($fetch_check) > 0){
                // sudah ada
                // DB::connection('sqlsrv_spm')->table('spm.spm_sekolah');
                $exe = DB::connection('sqlsrv_spm')->table('spm.spm_sekolah')
                ->where('sekolah_id',$fetch[$iFetch]->sekolah_id)
                ->where('semester_id',$fetch[$iFetch]->semester_id)
                ->where('kode_instrumen_spm',$fetch[$iFetch]->kode_instrumen_spm)
                ->update([
                    'target' => $fetch[$iFetch]->target,
                    'capaian' => $fetch[$iFetch]->capaian,
                    'gap' => $fetch[$iFetch]->gap,
                    'predikat' => $fetch[$iFetch]->predikat,
                    'persen' => $fetch[$iFetch]->persen,
                    'last_update' => date('Y-m-d H:i:s'),
                    'updater_id' => null,
                    'satuan' => $fetch[$iFetch]->satuan
                ]);

                $label = "[UPDATED]";

            }else{
                // belum ada
                $exe = DB::connection('sqlsrv_spm')->table('spm.spm_sekolah')->insert([
                    'sekolah_id' => $fetch[$iFetch]->sekolah_id,
                    'semester_id' => $fetch[$iFetch]->semester_id,
                    'kode_instrumen_spm' => $fetch[$iFetch]->kode_instrumen_spm,
                    'target' => $fetch[$iFetch]->target,
                    'capaian' => $fetch[$iFetch]->capaian,
                    'gap' => $fetch[$iFetch]->gap,
                    'predikat' => $fetch[$iFetch]->predikat,
                    'persen' => $fetch[$iFetch]->persen,
                    'soft_delete' => 0,
                    'create_date' => date('Y-m-d H:i:s'),
                    'last_update' => date('Y-m-d H:i:s'),
                    'updater_id' => null,
                    'satuan' => $fetch[$iFetch]->satuan
                ]);

                $label = "[INSERTED]";

            }

            echo "[INF] [".($iFetch+1)."/".sizeof($fetch)."] [".$fetch[$iFetch]->kode_instrumen_spm."] ".$kode_wilayah.' - '.$fetch[$iFetch]->sekolah_id.' '.$label.PHP_EOL;

        }
    }
}