<?php

namespace App\Console\Commands\SPM;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class spm2200
{
    static function index($kode_wilayah, $semester_id){
        $sql_2200 = "
        SELECT
            sekolah.sekolah_id,
            '".$semester_id."' AS semester_id,
            '2200' AS kode_instrumen_spm,
            ISNULL( guru.guru_total, 0 ) AS target,
            ISNULL ( guru.guru_total, 0 ) as capaian,
            ( CASE WHEN guru.guru_total - ISNULL ( guru.guru_total, 0 ) < 0 then 0 else guru.guru_total - ISNULL ( guru.guru_total, 0 ) END )  AS gap,
            (
            CASE	
                WHEN ( ROUND( ( ISNULL ( guru.guru_total, 0 ) / CAST ( ISNULL( guru.guru_total, 1 ) AS FLOAT ) * 100 ), 2 ) ) >= 100 THEN
                'Tercapai' ELSE 'Belum Tercapai' 
            END 
            ) AS predikat,
            ( CASE WHEN ( ROUND( ( ISNULL ( guru.guru_total, 0 ) / CAST ( ISNULL( guru.guru_total, 1 ) AS FLOAT ) * 100 ), 2 ) ) > 100 then 100 else ( ROUND( ( ISNULL ( guru.guru_total, 0 ) / CAST ( ISNULL( guru.guru_total, 1 ) AS FLOAT ) * 100 ), 2 ) ) END ) AS persen,
            'Guru' as satuan
        FROM
            sekolah
            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
            JOIN ref.bentuk_pendidikan bp WITH ( nolock ) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id 
            LEFT JOIN (
            SELECT
                ptkd.sekolah_id,
                SUM ( 1 ) AS guru_total 
            FROM
                ptk ptk WITH ( nolock )
                JOIN ptk_terdaftar ptkd WITH ( nolock ) ON ptk.ptk_id = ptkd.ptk_id
                JOIN ref.tahun_ajaran ta WITH ( nolock ) ON ta.tahun_ajaran_id = ptkd.tahun_ajaran_id 
            WHERE
                ptk.Soft_delete = 0 
                AND ptkd.Soft_delete = 0 
                AND ptkd.ptk_induk = 1 
                AND ptkd.tahun_ajaran_id = '".substr($semester_id,0,4)."' 
                AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) 
                AND ( ptkd.tgl_ptk_keluar > ta.tanggal_selesai OR ptkd.jenis_keluar_id IS NULL ) 
            GROUP BY
                ptkd.sekolah_id
            ) guru on guru.sekolah_id = sekolah.sekolah_id
        WHERE
            soft_delete = 0 
            AND sekolah.bentuk_pendidikan_id IN ( 5, 6, 9, 10, 53, 54 ) 
            AND kab.kode_wilayah = '".$kode_wilayah."'
        ";

        $fetch = DB::connection('sqlsrv')->select($sql_2200);

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