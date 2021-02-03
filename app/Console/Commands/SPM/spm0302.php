<?php

namespace App\Console\Commands\SPM;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class spm0302
{
    static function spm_0302($kode_wilayah, $semester_id){
        $sql_0302 = "
        SELECT
            sekolah.sekolah_id,
            '".$semester_id."' AS semester_id,
            '0302' AS kode_instrumen_spm,
            1 AS target,
            ISNULL( sarpras.jumlah_alat_peraga, 0 ) AS capaian,
            ( CASE WHEN 1 - ISNULL( sarpras.jumlah_alat_peraga, 0 ) < 0 then 0 else 1 - ISNULL( sarpras.jumlah_alat_peraga, 0 ) END )  AS gap,
            (
            CASE	
                WHEN ( ROUND( ( ISNULL( sarpras.jumlah_alat_peraga, 0 ) / CAST ( 1 AS FLOAT ) * 100 ), 2 ) ) >= 100 THEN
                'Tercapai' ELSE 'Belum Tercapai' 
            END 
            ) AS predikat,
            ( CASE WHEN ( ROUND( ( ISNULL( sarpras.jumlah_alat_peraga, 0 ) / CAST ( 1 AS FLOAT ) * 100 ), 2 ) ) > 100 then 100 else ( ROUND( ( ISNULL( sarpras.jumlah_alat_peraga, 0 ) / CAST ( 1 AS FLOAT ) * 100 ), 2 ) ) END ) AS persen,
            'Set Alat Peraga' as satuan
        FROM
            sekolah
            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
            JOIN ref.bentuk_pendidikan bp WITH ( nolock ) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
            LEFT JOIN (
            SELECT
                sekolah.sekolah_id,
                SUM ( 1 ) AS rombel_total 
            FROM
                rombongan_belajar WITH ( nolock )
                JOIN ref.kurikulum kurikulum WITH ( nolock ) ON kurikulum.kurikulum_id = rombongan_belajar.kurikulum_id
                JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id
                JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah 
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
            LEFT JOIN (
            SELECT
                prasarana.sekolah_id,
                SUM( alats.jumlah_alat_peraga) as jumlah_alat_peraga
            FROM
                ruang prasarana WITH ( nolock )
                JOIN ruang_longitudinal prl WITH ( nolock ) ON prl.id_ruang = prasarana.id_ruang
                JOIN bangunan WITH ( nolock ) ON prasarana.id_bangunan = bangunan.id_bangunan
                JOIN bangunan_longitudinal WITH ( nolock ) ON bangunan.id_bangunan = bangunan_longitudinal.id_bangunan 
                LEFT JOIN (
                SELECT
            ruang.id_ruang,
            ruang.nm_ruang,
            SUM(along.jumlah) as jumlah_alat_peraga
        FROM
            alat
            JOIN ruang ON ruang.id_ruang = alat.id_ruang
            JOIN alat_longitudinal along ON along.id_alat = alat.id_alat 
            AND along.semester_id = ".$semester_id."
            JOIN ref.jenis_sarana jenis ON jenis.jenis_sarana_id = alat.jenis_sarana_id AND kelompok = '5 - Alat Peraga' 
        WHERE
            alat.Soft_delete = 0 
            AND ruang.Soft_delete = 0 
        -- 	AND ruang.jenis_prasarana_id = 2
        GROUP BY
            ruang.id_ruang,
            ruang.nm_ruang
        ) alats on alats.id_ruang = prasarana.id_ruang
            WHERE
                prasarana.soft_delete = 0 
                AND prl.Soft_delete = 0 
                AND prl.semester_id = ".$semester_id." 
                AND bangunan.Soft_delete = 0 
                AND bangunan_longitudinal.Soft_delete = 0 
                AND bangunan_longitudinal.semester_id = ".$semester_id." 
            GROUP BY
                prasarana.sekolah_id 
            ) sarpras ON sarpras.sekolah_id = sekolah.sekolah_id 
        WHERE
            soft_delete = 0 
            AND sekolah.bentuk_pendidikan_id IN ( 6, 10, 54 ) 
            AND kab.kode_wilayah = '".$kode_wilayah."'
        ";

        $fetch = DB::connection('sqlsrv')->select($sql_0302);

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