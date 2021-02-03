<?php

namespace App\Console\Commands\SPM;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class spm0203
{
    static function spm_0203($kode_wilayah, $semester_id){
        $sql_0203 = "
        SELECT
            sekolah.sekolah_id,
            '".$semester_id."' AS semester_id,
            '0203' AS kode_instrumen_spm,
            ISNULL( rombels.rombel_total, 0 ) AS target,
            ISNULL( rombels.rombel_kurang_36, 0 ) AS capaian,
            ISNULL( rombels.rombel_total, 0 ) - ISNULL( rombels.rombel_kurang_36, 0 ) AS gap,
            (
            CASE	
                WHEN ( ROUND( ( ISNULL( rombels.rombel_kurang_36, 0 ) / CAST ( ISNULL( rombels.rombel_total, 1 ) AS FLOAT ) * 100 ), 2 ) ) = 100 THEN
                'Tercapai' ELSE 'Belum Tercapai' 
            END 
            ) AS predikat,
            ROUND( ( ISNULL( rombels.rombel_kurang_36, 0 ) / CAST ( ISNULL( rombels.rombel_total, 1 ) AS FLOAT ) * 100 ), 2 ) AS persen,
            'Rombongan Belajar' as satuan 
        FROM
            sekolah
            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
            JOIN ref.bentuk_pendidikan bp WITH ( nolock ) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
            LEFT JOIN (
            SELECT
                sekolah.sekolah_id,
                SUM ( 1 ) AS rombel_total,
                SUM ( CASE WHEN anggota.jumlah_anggota_rombel <= 36 THEN 1 ELSE 0 END ) AS rombel_kurang_36 
            FROM
                rombongan_belajar WITH ( nolock )
                JOIN ref.kurikulum kurikulum WITH ( nolock ) ON kurikulum.kurikulum_id = rombongan_belajar.kurikulum_id
                JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id
                JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                LEFT JOIN (
                SELECT
                    rombongan_belajar.rombongan_belajar_id,
                    SUM ( 1 ) AS jumlah_anggota_rombel 
                FROM
                    anggota_rombel
                    JOIN rombongan_belajar ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
                    JOIN sekolah ON sekolah.sekolah_id = rombongan_belajar.sekolah_id
                    JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                    JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                    JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah 
                WHERE
                    anggota_rombel.Soft_delete = 0 
                    AND rombongan_belajar.Soft_delete = 0 
                    AND rombongan_belajar.semester_id = ".$semester_id." 
                    AND sekolah.Soft_delete = 0 
                    AND sekolah.bentuk_pendidikan_id IN ( 6, 10, 54 ) 
                    AND kab.kode_wilayah = '".$kode_wilayah."' 
                GROUP BY
                    rombongan_belajar.rombongan_belajar_id 
                ) anggota ON anggota.rombongan_belajar_id = rombongan_belajar.rombongan_belajar_id 
            WHERE
                rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                AND rombongan_belajar.Soft_delete = 0 
                AND rombongan_belajar.semester_id = ".$semester_id." 
                AND sekolah.bentuk_pendidikan_id IN ( 6, 10, 54 ) 
            GROUP BY
                rombongan_belajar.sekolah_id,
                sekolah.sekolah_id,
                sekolah.nama,
                sekolah.npsn,
                prop.nama,
                kab.nama,
                kec.nama 
            ) rombels ON rombels.sekolah_id = sekolah.sekolah_id
        WHERE
            soft_delete = 0 
        AND sekolah.bentuk_pendidikan_id IN ( 6, 10, 54 ) 
        AND kab.kode_wilayah = '".$kode_wilayah."'
        ";

        $fetch = DB::connection('sqlsrv')->select($sql_0203);

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