<?php

namespace App\Console\Commands\SNP;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class snp060101
{
    static function index($sekolah_id){
        $sql = "SELECT
                    sekolah.sekolah_id,
                    20191 AS semester_id,
                    '0201' AS kode_instrumen_spm,
                    ISNULL( rombels.rombel_total, 0 ) AS target,
                    ISNULL( rombels.rombel_kurang_32, 0 ) AS capaian,
                    ISNULL( rombels.rombel_total, 0 ) - ISNULL( rombels.rombel_kurang_32, 0 ) AS gap,
                    (
                    CASE	
                        WHEN ( ROUND( ( ISNULL( rombels.rombel_kurang_32, 0 ) / CAST ( ISNULL( rombels.rombel_total, 1 ) AS FLOAT ) * 100 ), 2 ) ) = 100 THEN
                        'Tercapai' ELSE 'Belum Tercapai' 
                    END 
                    ) AS predikat,
                    ROUND( ( ISNULL( rombels.rombel_kurang_32, 0 ) / CAST ( ISNULL( rombels.rombel_total, 1 ) AS FLOAT ) * 100 ), 2 ) AS persen,
                    ( case when ROUND( ( ROUND( ( ISNULL( rombels.rombel_kurang_32, 0 ) / CAST ( ISNULL( rombels.rombel_total, 1 ) AS FLOAT ) * 100 ), 2 ) / 100 * 7 ), 2 ) > 7 then 7 else ROUND( ( ROUND( ( ISNULL( rombels.rombel_kurang_32, 0 ) / CAST ( ISNULL( rombels.rombel_total, 1 ) AS FLOAT ) * 100 ), 2 ) / 100 * 7 ), 2 ) end ) AS snp,
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
                        SUM ( CASE WHEN anggota.jumlah_anggota_rombel <= (case when sekolah.bentuk_pendidikan_id IN (5,6) then 32 when sekolah.bentuk_pendidikan_id IN (13,15) then 36 else 32 end) THEN 1 ELSE 0 END ) AS rombel_kurang_32 
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
                            AND rombongan_belajar.semester_id = 20191 
                            AND sekolah.Soft_delete = 0 
                            AND sekolah.sekolah_id = '".$sekolah_id."'
                             
                        GROUP BY
                            rombongan_belajar.rombongan_belajar_id 
                        ) anggota ON anggota.rombongan_belajar_id = rombongan_belajar.rombongan_belajar_id 
                    WHERE
                        rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                        AND rombongan_belajar.Soft_delete = 0 
                        AND rombongan_belajar.semester_id = 20191 
                        AND sekolah.sekolah_id = '".$sekolah_id."'
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
                AND sekolah.sekolah_id = '".$sekolah_id."'
                ";
        
        $fetch = DB::connection('sqlsrv')->select($sql);

        if(sizeof($fetch) > 0){
            $sql_update = "update master_pmp set r19 = '".$fetch[0]->snp."', last_update = getdate() where sekolah_id = '".$sekolah_id."' and urut = '06.01.01' and r19 < ".$fetch[0]->snp."";

            try {
                DB::connection('sqlsrv_pmp')->statement($sql_update);

                echo "[INF] [".$sekolah_id."] MASTER_PMP 06.01.01 [BERHASIL]".PHP_EOL;
            } catch (\Throwable $th) {
                echo "[INF] [".$sekolah_id."] MASTER_PMP 06.01.01 [GAGAL]".PHP_EOL;
            }
        }
            

    }
}