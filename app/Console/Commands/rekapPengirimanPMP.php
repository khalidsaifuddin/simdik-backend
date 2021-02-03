<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class rekapPengirimanPMP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:pengiriman_pmp {--kode_wilayah=000000} {--kode_wilayah_kabupaten=0} {--kode_wilayah_kecamatan=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $semester_id = '20191';
        $kode_wilayah = $this->option('kode_wilayah');
        $kode_wilayah_kabupaten = $this->option('kode_wilayah_kabupaten');
        $kode_wilayah_kecamatan = $this->option('kode_wilayah_kecamatan');

        $provinsi = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
        ->where('wilayah.id_level_wilayah','=',1)
        ->whereNull('wilayah.expired_date');

        if($kode_wilayah != '000000'){
            $provinsi = $provinsi->where('kode_wilayah','=',$kode_wilayah);
        }

        $provinsi = $provinsi->get();

        for ($iProvinsi=0; $iProvinsi < sizeof($provinsi); $iProvinsi++) { 
            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] ".$provinsi[$iProvinsi]->{'nama'}.PHP_EOL;

            $kabupaten = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
            ->where('wilayah.mst_kode_wilayah','=',$provinsi[$iProvinsi]->{'kode_wilayah'})
            ->whereNull('wilayah.expired_date');
            
            if($kode_wilayah_kabupaten != '0'){
                $kabupaten = $kabupaten->where('kode_wilayah','=',$kode_wilayah_kabupaten);
            }

            $kabupaten = $kabupaten->get();

            for ($iKabupaten=0; $iKabupaten < sizeof($kabupaten); $iKabupaten++) { 
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}.PHP_EOL;
            
                $kecamatan = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
                ->where('wilayah.mst_kode_wilayah','=',$kabupaten[$iKabupaten]->{'kode_wilayah'})
                ->whereNull('wilayah.expired_date');
                
                if($kode_wilayah_kecamatan != '0'){
                    $kecamatan = $kecamatan->where('kode_wilayah','=',$kode_wilayah_kecamatan);
                }

                $kecamatan = $kecamatan->get();

                for ($iKecamatan=0; $iKecamatan < sizeof($kecamatan); $iKecamatan++) { 

                    $sql_delete = "delete from rekap_pengiriman_pmp where kode_wilayah_kecamatan = '".trim($kecamatan[$iKecamatan]->{'kode_wilayah'})."'";

                    try {
                        DB::connection('sqlsrv_pmp')->statement($sql_delete);
                        DB::connection('sqlsrv_2')->statement($sql_delete);

                        echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'} ." - DELETE BERHASIL".PHP_EOL;
                    } catch (\Throwable $th) {

                        echo $sql_delete.PHP_EOL;
                        echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'} ." - DELETE GAGAL".PHP_EOL;
                    }

                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}.PHP_EOL;
                    
                    $sql = "
                    SELECT
                        sekolah.sekolah_id, 
                        sekolah.nama,
                        pengiriman_pmp.jumlah_kirim,
                        (jumlah_pengguna.jumlah_pengguna+jumlah_pengguna_pd.jumlah_pengguna) as jumlah_pengguna,
                        (pengguna_mengerjakan.jumlah_pengguna+pengguna_mengerjakan_pd.jumlah_pengguna) as jumlah_pengguna_mengerjakan,
                        (case when rapor.jenis_timeline_id = 2 then 1 else 0 end) as hitung_rapor_mutu,
                        pengiriman_pmp.sync_terakhir,
                        verval.jumlah as verifikasi_pengawas,
                        pengguna_mengerjakan.kepsek_mengerjakan,
                        pengguna_mengerjakan_pd.pd_mengerjakan,
                        pengguna_mengerjakan.ptk_mengerjakan,
                        pengguna_mengerjakan.komite_mengerjakan,
                        jumlah_pengguna.kepsek_total,
                        jumlah_pengguna_pd.pd_total,
                        jumlah_pengguna.ptk_total,
                        jumlah_pengguna.komite_total,
                        ((pengguna_mengerjakan.kepsek_mengerjakan/cast(1 as float))*100) as kepsek_persen,
                        (case when ((pengguna_mengerjakan_pd.pd_mengerjakan/cast((case when jumlah_pengguna_pd.pd_total > 15 then 15 else (case when jumlah_pengguna_pd.pd_total > 0 then jumlah_pengguna_pd.pd_total else 1 end) end) as float))*100) > 100 then 100 else ((pengguna_mengerjakan_pd.pd_mengerjakan/cast((case when jumlah_pengguna_pd.pd_total > 15 then 15 else (case when jumlah_pengguna_pd.pd_total > 0 then jumlah_pengguna_pd.pd_total else 1 end) end) as float))*100) end) as pd_persen,
                        (case when ((pengguna_mengerjakan.ptk_mengerjakan/cast((case when jumlah_pengguna.ptk_total > 10 then 10 else (case when jumlah_pengguna.ptk_total > 0 then jumlah_pengguna.ptk_total else 1 end) end) as float))*100) > 100 then 100 else ((pengguna_mengerjakan.ptk_mengerjakan/cast((case when jumlah_pengguna.ptk_total > 10 then 10 else (case when jumlah_pengguna.ptk_total > 0 then jumlah_pengguna.ptk_total else 1 end) end) as float))*100) end ) as ptk_persen,
                        ((pengguna_mengerjakan.komite_mengerjakan/cast(3 as float))*100) as komite_persen,
                        '2019' as tahun_ajaran_id,
                        sekolah.soft_delete as soft_delete_pmp
                    FROM SEKOLAH with(nolock) 
                    LEFT JOIN (
                    SELECT
                        sekolah.sekolah_id,
                        COUNT ( distinct(jawaban_pengguna.pengguna_id) ) AS jumlah_pengguna,
                        COUNT ( distinct(CASE WHEN jawaban_pengguna.peran_id = 10 then jawaban_pengguna.pengguna_id ELSE NULL END)) as kepsek_total,
                        COUNT ( distinct(CASE WHEN jawaban_pengguna.peran_id = 90 then jawaban_pengguna.pengguna_id ELSE NULL END)) as pd_total,
                        COUNT ( distinct(CASE WHEN jawaban_pengguna.peran_id = 53 then jawaban_pengguna.pengguna_id ELSE NULL END)) as ptk_total,
                        COUNT ( distinct(CASE WHEN jawaban_pengguna.peran_id = 14 then jawaban_pengguna.pengguna_id ELSE NULL END)) as komite_total 
                    FROM
                        sekolah with(nolock)
                        JOIN (
                        SELECT
                            pengguna.pengguna_id,
                            sekolah.sekolah_id,
                            max(pengguna.peran_id) as peran_id 
                        FROM
                            pengguna with(nolock)
                            JOIN sekolah with(nolock) ON sekolah.sekolah_id = pengguna.sekolah_id 
                        WHERE
                            pengguna.soft_delete = 0 
                            AND sekolah.soft_delete = 0 
                        GROUP BY
                            pengguna.pengguna_id,
                            sekolah.sekolah_id 
                        ) jawaban_pengguna ON jawaban_pengguna.sekolah_id = sekolah.sekolah_id 
                    GROUP BY
                        sekolah.sekolah_id 
                    ) jumlah_pengguna ON jumlah_pengguna.sekolah_id = sekolah.sekolah_id 
                    LEFT JOIN (
                        SELECT
                            sekolah.sekolah_id,
                            COUNT ( distinct(jawaban_pengguna.pengguna_id) ) AS jumlah_pengguna,
                            COUNT ( distinct(CASE WHEN jawaban_pengguna.peran_id = 10 then jawaban_pengguna.pengguna_id ELSE NULL END)) as kepsek_total,
                            COUNT ( distinct(CASE WHEN jawaban_pengguna.peran_id = 90 then jawaban_pengguna.pengguna_id ELSE NULL END)) as pd_total,
                            COUNT ( distinct(CASE WHEN jawaban_pengguna.peran_id = 53 then jawaban_pengguna.pengguna_id ELSE NULL END)) as ptk_total,
                            COUNT ( distinct(CASE WHEN jawaban_pengguna.peran_id = 14 then jawaban_pengguna.pengguna_id ELSE NULL END)) as komite_total 
                        FROM
                            sekolah with(nolock)
                            JOIN (
                            SELECT
                                pengguna.pengguna_id,
                                sekolah.sekolah_id,
                                max(pengguna.peran_id) as peran_id 
                            FROM
                                pengguna_pd pengguna with(nolock)
                                JOIN sekolah with(nolock) ON sekolah.sekolah_id = pengguna.sekolah_id 
                            WHERE
                                pengguna.soft_delete = 0 
                                AND sekolah.soft_delete = 0 
                            GROUP BY
                                pengguna.pengguna_id,
                                sekolah.sekolah_id 
                            ) jawaban_pengguna ON jawaban_pengguna.sekolah_id = sekolah.sekolah_id 
                        GROUP BY
                            sekolah.sekolah_id 
                    ) jumlah_pengguna_pd ON jumlah_pengguna_pd.sekolah_id = sekolah.sekolah_id 
                    LEFT JOIN (
                        SELECT
                            pengguna.sekolah_id,
                            COUNT(distinct(jawaban_utama.pengguna_id)) as jumlah_pengguna,
                            COUNT(distinct(CASE WHEN pengguna.peran_id = 10 then jawaban_utama.pengguna_id ELSE NULL END)) as kepsek_mengerjakan,
                            COUNT(distinct(CASE WHEN pengguna.peran_id = 90 then jawaban_utama.pengguna_id ELSE NULL END)) as pd_mengerjakan,
                            COUNT(distinct(CASE WHEN pengguna.peran_id = 53 then jawaban_utama.pengguna_id ELSE NULL END)) as ptk_mengerjakan,
                            COUNT(distinct(CASE WHEN pengguna.peran_id = 14 then jawaban_utama.pengguna_id ELSE NULL END)) as komite_mengerjakan
                        FROM
                            jawaban.[".trim($kabupaten[$iKabupaten]->{'kode_wilayah'})."] jawaban_utama with(nolock)
                            JOIN pengguna with(nolock) ON pengguna.pengguna_id = jawaban_utama.pengguna_id 
                        WHERE
                            pengguna.soft_delete = 0 
                            AND jawaban_utama.soft_delete = 0 
                        GROUP BY
                            pengguna.sekolah_id
                    ) pengguna_mengerjakan ON pengguna_mengerjakan.sekolah_id = sekolah.sekolah_id
                    LEFT JOIN (
                        SELECT
                            pengguna.sekolah_id,
                            COUNT(distinct(jawaban_utama.pengguna_id)) as jumlah_pengguna,
                            COUNT(distinct(CASE WHEN pengguna.peran_id = 10 then jawaban_utama.pengguna_id ELSE NULL END)) as kepsek_mengerjakan,
                            COUNT(distinct(CASE WHEN pengguna.peran_id = 90 then jawaban_utama.pengguna_id ELSE NULL END)) as pd_mengerjakan,
                            COUNT(distinct(CASE WHEN pengguna.peran_id = 53 then jawaban_utama.pengguna_id ELSE NULL END)) as ptk_mengerjakan,
                            COUNT(distinct(CASE WHEN pengguna.peran_id = 14 then jawaban_utama.pengguna_id ELSE NULL END)) as komite_mengerjakan
                        FROM
                            jawaban.[".trim($kabupaten[$iKabupaten]->{'kode_wilayah'})."] jawaban_utama with(nolock)
                            JOIN pengguna_pd pengguna with(nolock) ON pengguna.pengguna_id = jawaban_utama.pengguna_id 
                        WHERE
                            pengguna.soft_delete = 0 
                            AND jawaban_utama.soft_delete = 0 
                        GROUP BY
                            pengguna.sekolah_id
                    ) pengguna_mengerjakan_pd ON pengguna_mengerjakan_pd.sekolah_id = sekolah.sekolah_id 
                    LEFT JOIN (
                    SELECT
                        sekolah.sekolah_id,
                        SUM ( 1 ) AS jumlah_kirim,
                        -- ISNULL( SUM ( jumlah_pengguna.jumlah_pengguna ), 0 ) AS jumlah_pengguna,
                        -- ISNULL( SUM ( pengguna_mengerjakan.jumlah_pengguna ), 0 ) AS jumlah_pengguna_mengerjakan,
                        MAX ( begin_sync ) AS sync_terakhir 
                    FROM
                        sync_log with(nolock)
                        JOIN pengguna with(nolock) ON pengguna.pengguna_id = sync_log.pengguna_id
                        JOIN sekolah with(nolock) ON sekolah.sekolah_id = pengguna.sekolah_id
                        JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                        JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                        JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                    WHERE
                        end_sync IS NOT NULL 
                        AND alamat_ip NOT LIKE '%prefil%' 
                        AND sekolah.soft_delete = 0 
                        AND pengguna.soft_delete = 0 
                        AND pengguna.peran_id = 10 
                        AND kec.kode_wilayah = '".trim($kecamatan[$iKecamatan]->{'kode_wilayah'})."'
                    GROUP BY
                        sekolah.sekolah_id
                    ) pengiriman_pmp on pengiriman_pmp.sekolah_id = sekolah.sekolah_id
                    JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                    JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                    JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                    LEFT JOIN (
                        select sekolah_id, max(jenis_timeline_id) as jenis_timeline_id from timeline with(nolock) where jenis_timeline_id = 2 and soft_delete = 0 group by sekolah_id
                    ) rapor on rapor.sekolah_id = sekolah.sekolah_id
                    LEFT JOIN (
                        SELECT
                            sekolah_id,
                            SUM ( 1 ) AS jumlah 
                        FROM
                            verifikasi_pengawas 
                        WHERE
                            soft_delete = 0 
                        GROUP BY
                            sekolah_id
                    ) verval on verval.sekolah_id = sekolah.sekolah_id
                    WHERE 
                        sekolah.soft_delete = 0
                    AND kec.kode_wilayah = '".trim($kecamatan[$iKecamatan]->{'kode_wilayah'})."'";
                    
                    // echo $sql;die;

                    $fetch = DB::connection('sqlsrv_pmp')->select(DB::raw($sql));


                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 
                        $sql_insert = "insert into rekap_pengiriman_pmp values(
                            '".$fetch[$iSekolah]->{'sekolah_id'}."',
                            '".$fetch[$iSekolah]->{'tahun_ajaran_id'}."',
                            '".$fetch[$iSekolah]->{'jumlah_kirim'}."',
                            '".$fetch[$iSekolah]->{'jumlah_pengguna'}."',
                            '".substr($fetch[$iSekolah]->{'sync_terakhir'},0,19)."',
                            '".trim($kecamatan[$iKecamatan]->{'kode_wilayah'})."',
                            '".trim($kabupaten[$iKabupaten]->{'kode_wilayah'})."',
                            '".trim($provinsi[$iProvinsi]->{'kode_wilayah'})."',
                            '".$fetch[$iSekolah]->{'jumlah_pengguna_mengerjakan'}."',
                            '".$fetch[$iSekolah]->{'hitung_rapor_mutu'}."',
                            '".$fetch[$iSekolah]->{'verifikasi_pengawas'}."',
                            '".$fetch[$iSekolah]->{'kepsek_total'}."',
                            '".$fetch[$iSekolah]->{'kepsek_mengerjakan'}."',
                            '".$fetch[$iSekolah]->{'kepsek_persen'}."',
                            '".$fetch[$iSekolah]->{'pd_total'}."',
                            '".$fetch[$iSekolah]->{'pd_mengerjakan'}."',
                            '".$fetch[$iSekolah]->{'pd_persen'}."',
                            '".$fetch[$iSekolah]->{'ptk_total'}."',
                            '".$fetch[$iSekolah]->{'ptk_mengerjakan'}."',
                            '".$fetch[$iSekolah]->{'ptk_persen'}."',
                            '".$fetch[$iSekolah]->{'komite_total'}."',
                            '".$fetch[$iSekolah]->{'komite_mengerjakan'}."',
                            '".$fetch[$iSekolah]->{'komite_persen'}."',
                            '".$fetch[$iSekolah]->{'soft_delete_pmp'}."'    
                        )";

                        try {
                            // echo "[INF] ".json_encode($fetch[$iSekolah]).PHP_EOL;

                            DB::connection('sqlsrv_pmp')->statement($sql_insert);
                            DB::connection('sqlsrv_2')->statement($sql_insert);
                            
                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL]".PHP_EOL;

                        } catch (\Throwable $th) {

                            echo $sql_insert.PHP_EOL;
                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL]".PHP_EOL;
                        }
                    }
                }
            }
        }
    }
}
