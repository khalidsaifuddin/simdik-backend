<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class rekapRaporMutakhir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:rekap_rapor_mutakhir {--semester_id=20191} {--kode_wilayah=000000} {--sekolah_id=0} {--kode_wilayah_kabupaten=000000}';

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
        $semester_id = $this->option('semester_id');
        $kode_wilayah = $this->option('kode_wilayah');
        $kode_wilayah_kabupaten = $this->option('kode_wilayah_kabupaten');
        $op_sekolah_id = $this->option('sekolah_id');

        //ambil data wilayah
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
            // ->get();
            if($kode_wilayah_kabupaten != '000000'){
                $kabupaten = $kabupaten->where('kode_wilayah','=',$kode_wilayah_kabupaten);
            }

            $kabupaten = $kabupaten->get();

            for ($iKabupaten=0; $iKabupaten < sizeof($kabupaten); $iKabupaten++) { 
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}.PHP_EOL;
            
                $kecamatan = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
                ->where('wilayah.mst_kode_wilayah','=',$kabupaten[$iKabupaten]->{'kode_wilayah'})
                ->whereNull('wilayah.expired_date')
                ->get();

                // $gage = substr($semester_id,4,1);

                for ($iKecamatan=0; $iKecamatan < sizeof($kecamatan); $iKecamatan++) { 
                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}.PHP_EOL;
                
                    $fetch = DB::select(DB::raw("SELECT
                        sekolah.sekolah_id,
                        '".$semester_id."' as semester_id,
                        sekolah.bentuk_pendidikan_id,
                        sekolah.status_sekolah,
                        sekolah.nama,
                        sekolah.npsn,
                        kec.kode_wilayah as kode_wilayah_kecamatan,
                        kab.kode_wilayah as kode_wilayah_kabupaten,
                        prop.kode_wilayah as kode_wilayah_provinsi,
                        negara.kode_wilayah as kode_wilayah_negara,
                        kec.nama as kecamatan,
                        kab.nama as kabupaten,
                        prop.nama as provinsi,
                        negara.nama as negara,
                        kec.id_level_wilayah as id_level_wilayah_kecamatan,
                        kab.id_level_wilayah as id_level_wilayah_kabupaten,
                        prop.id_level_wilayah as id_level_wilayah_provinsi,
                        negara.id_level_wilayah as id_level_wilayah_negara,
                        getdate() as create_date,
                        getdate() as last_update,
                        sekolah.soft_delete as soft_delete,
                        '2BF85354-F67D-4D24-B284-7DB9C8836933' as updater_id
                    FROM 
                        sekolah with(nolock)
                    JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                    JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                    JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                    JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah
                    WHERE kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'"));

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 

                        try {
                            //code...
                            $sql_mutakhir_pd = "SELECT
                            rpd.sekolah_id,
                                (
                                    ( case when ISNULL( AVG ( CASE WHEN semester_id = ".$semester_id." THEN tinggi_badan ELSE NULL END ), 0 ) != ISNULL( AVG ( CASE WHEN semester_id = ".(substr($semester_id,4,1)=="1" ? "20".((int)substr($semester_id,3,1)-1)."2" : substr($semester_id,0,4)."1")." THEN tinggi_badan ELSE NULL END ), 0 ) then 1 else 0 end ) +
                                    ( case when ISNULL( AVG ( CASE WHEN semester_id = ".$semester_id." THEN berat_badan ELSE NULL END ), 0 ) != ISNULL( AVG ( CASE WHEN semester_id = ".(substr($semester_id,4,1)=="1" ? "20".((int)substr($semester_id,3,1)-1)."2" : substr($semester_id,0,4)."1")." THEN berat_badan ELSE NULL END ), 0 ) then 1 else 0 end ) +
                                    ( case when ISNULL( AVG ( CASE WHEN semester_id = ".$semester_id." THEN lingkar_kepala ELSE NULL END ), 0 ) != ISNULL( AVG ( CASE WHEN semester_id = ".(substr($semester_id,4,1)=="1" ? "20".((int)substr($semester_id,3,1)-1)."2" : substr($semester_id,0,4)."1")." THEN lingkar_kepala ELSE NULL END ), 0 ) then 1 else 0 end ) 
                                ) / cast(3 as float) * 100 as rapor_mutakhir_pd
                                
                            FROM
                                peserta_didik_longitudinal WITH ( nolock )
                                JOIN peserta_didik WITH ( nolock ) ON peserta_didik.peserta_didik_id = peserta_didik_longitudinal.peserta_didik_id 
                                AND peserta_didik.Soft_delete = 0
                                JOIN registrasi_peserta_didik rpd WITH ( nolock ) ON peserta_didik.peserta_didik_id = rpd.peserta_didik_id 
                                AND rpd.Soft_delete = 0 
                                AND rpd.jenis_keluar_id IS NULL 
                            WHERE
                                rpd.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' 
                            GROUP BY
                                rpd.sekolah_id";

                            // echo $sql_mutakhir_pd;die;
                            $fetch_mutakhir_pd = DB::select(DB::raw($sql_mutakhir_pd));

                            try {
                                $fetch[$iSekolah]->rapor_mutakhir_pd = $fetch_mutakhir_pd[0]->rapor_mutakhir_pd;
                            } catch (\Throwable $th) {
                                $fetch[$iSekolah]->rapor_mutakhir_pd = 0;
                            }

                            $sql_mutakhir_ptk = "SELECT
                                ptkd.sekolah_id,
                                ( SUM ( CASE WHEN pembelajaran.pembelajaran > 0 THEN 1 ELSE 0 END ) / cast(SUM(1) as float) * 100 ) AS rapor_mutakhir_ptk 
                            FROM
                                ptk WITH ( nolock )
                                LEFT JOIN (
                                SELECT
                                    ptk_terdaftar.ptk_id,
                                    SUM ( CASE WHEN pembelajaran.semester_id = ".$semester_id." THEN 1 ELSE 0 END ) AS pembelajaran 
                                FROM
                                    pembelajaran WITH ( nolock )
                                    JOIN ptk_terdaftar WITH ( nolock ) ON ptk_terdaftar.ptk_terdaftar_id = pembelajaran.ptk_terdaftar_id 
                                    AND ptk_terdaftar.Soft_delete = 0 
                                    AND ptk_terdaftar.tahun_ajaran_id = LEFT ( pembelajaran.semester_id, 4 ) 
                                    AND ptk_terdaftar.jenis_keluar_id IS NULL 
                                WHERE
                                    ptk_terdaftar.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."'
                                    AND pembelajaran.semester_id IN ( ".$semester_id." ) 
                                GROUP BY
                                    ptk_terdaftar.ptk_id 
                                ) pembelajaran ON pembelajaran.ptk_id = ptk.ptk_id
                                JOIN ptk_terdaftar ptkd WITH ( nolock ) ON ptkd.ptk_id = ptk.ptk_id 
                            WHERE
                                ptkd.jenis_keluar_id IS NULL 
                                AND ptkd.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."'
                                AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) 
                                AND ptk.soft_delete = 0 
                                AND ptkd.Soft_delete = 0 
                            GROUP BY
                                ptkd.sekolah_id";
                            
                            // echo $sql_mutakhir_ptk;die;
                            $fetch_mutakhir_ptk = DB::select(DB::raw($sql_mutakhir_ptk));

                            try {
                                $fetch[$iSekolah]->rapor_mutakhir_ptk = $fetch_mutakhir_ptk[0]->rapor_mutakhir_ptk;
                            } catch (\Throwable $th) {
                                $fetch[$iSekolah]->rapor_mutakhir_ptk = 0;
                            }

                            $sql_mutakhir_rombel = "SELECT
                                rombongan_belajar.sekolah_id,
                                ( CASE WHEN SUM ( 1 ) > 0 THEN 100 ELSE 0 END ) AS rapor_mutakhir_rombel 
                            FROM
                                pembelajaran
                                JOIN rombongan_belajar ON rombongan_belajar.rombongan_belajar_id = pembelajaran.rombongan_belajar_id 
                            WHERE
                                pembelajaran.semester_id = ".$semester_id." 
                                AND rombongan_belajar.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."'
                            GROUP BY
                                rombongan_belajar.sekolah_id";

                            $fetch_mutakhir_rombel = DB::select(DB::raw($sql_mutakhir_rombel));

                            try {
                                $fetch[$iSekolah]->rapor_mutakhir_rombel = $fetch_mutakhir_rombel[0]->rapor_mutakhir_rombel;
                            } catch (\Throwable $th) {
                                $fetch[$iSekolah]->rapor_mutakhir_rombel = 0;
                            }

                            $sql_mutakhir_sarpras = "SELECT
                                ruang.sekolah_id,
                                ( CASE WHEN SUM ( 1 ) > 0 THEN 100 ELSE 0 END ) AS rapor_mutakhir_sarpras
                            FROM
                                ruang
                                JOIN ruang_longitudinal ON ruang_longitudinal.id_ruang= ruang.id_ruang
                            WHERE
                                ruang_longitudinal.semester_id = ".$semester_id."  
                                AND ruang.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."'
                            GROUP BY
                                ruang.sekolah_id";
                            
                            $fetch_mutakhir_sarpras = DB::select(DB::raw($sql_mutakhir_sarpras));

                            try {
                                $fetch[$iSekolah]->rapor_mutakhir_sarpras = $fetch_mutakhir_sarpras[0]->rapor_mutakhir_sarpras;
                            } catch (\Throwable $th) {
                                $fetch[$iSekolah]->rapor_mutakhir_sarpras = 0;
                            }

                            $rapor_mutakhir = 
                            ( 
                                (
                                    (int)$fetch[$iSekolah]->rapor_mutakhir_pd+
                                    (int)$fetch[$iSekolah]->rapor_mutakhir_ptk+
                                    (int)$fetch[$iSekolah]->rapor_mutakhir_rombel+
                                    (int)$fetch[$iSekolah]->rapor_mutakhir_sarpras
                                ) 
                                / (int)4 
                            );

                            // echo json_encode($fetch[$iSekolah]);die;
                            $sql_update = "update rekap.rekap_rapor_dapodik_sekolah set
                                rapor_mutakhir_pd = '".$fetch[$iSekolah]->rapor_mutakhir_pd."',
                                rapor_mutakhir_ptk = '".$fetch[$iSekolah]->rapor_mutakhir_ptk."',
                                rapor_mutakhir_rombel = '".$fetch[$iSekolah]->rapor_mutakhir_rombel."',
                                rapor_mutakhir_sarpras = '".$fetch[$iSekolah]->rapor_mutakhir_sarpras."',
                                rapor_mutakhir = '".$rapor_mutakhir."'
                            where sekolah_id = '".$fetch[$iSekolah]->sekolah_id."'
                            AND semester_id = '".$fetch[$iSekolah]->semester_id."'
                            ";

                            // echo $sql_update;die;
                            try {
                                DB::connection('sqlsrv_3')->statement($sql_update);

                                $bulan = substr($fetch[$iSekolah]->{'last_update'},5,2);

                            // echo $bulan.PHP_EOL;die;
                                $sql_periodik = "IF NOT EXISTS ( SELECT * FROM rekap.rekap_rapor_dapodik_periodik rekap_rapor WITH ( nolock ) WHERE sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."' ) 
                                INSERT INTO rekap.rekap_rapor_dapodik_periodik (
                                    sekolah_id,
                                    semester_id,
                                    mutakhir_pd_bulan_".$bulan.",
                                    mutakhir_ptk_bulan_".$bulan.",
                                    mutakhir_sarpras_bulan_".$bulan.",
                                    mutakhir_rombel_bulan_".$bulan.",
                                    mutakhir_bulan_".$bulan."
                                )
                                VALUES
                                (
                                    '".$fetch[$iSekolah]->{'sekolah_id'}."',
                                    '".$fetch[$iSekolah]->{'semester_id'}."',
                                    '".$fetch[$iSekolah]->rapor_mutakhir_pd."',
                                    '".$fetch[$iSekolah]->rapor_mutakhir_ptk."',
                                    '".$fetch[$iSekolah]->rapor_mutakhir_sarpras."',
                                    '".$fetch[$iSekolah]->rapor_mutakhir_rombel."',
                                    '".$rapor_mutakhir."'
                                ) ELSE 
                                UPDATE rekap.rekap_rapor_dapodik_periodik 
                                SET 
                                    mutakhir_pd_bulan_".$bulan." = '".$fetch[$iSekolah]->rapor_mutakhir_pd."',
                                    mutakhir_ptk_bulan_".$bulan." = '".$fetch[$iSekolah]->rapor_mutakhir_ptk."',
                                    mutakhir_sarpras_bulan_".$bulan." = '".$fetch[$iSekolah]->rapor_mutakhir_sarpras."',
                                    mutakhir_rombel_bulan_".$bulan." = '".$fetch[$iSekolah]->rapor_mutakhir_rombel."',
                                    mutakhir_bulan_".$bulan." = '".$rapor_mutakhir."'
                                WHERE
                                    sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' 
                                    AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'";

                                DB::connection('sqlsrv_3')->statement($sql_periodik);

                                // echo $sql_periodik;die;
                                
                                $sql_final = "UPDATE rekap.rekap_rapor_dapodik_periodik 
                                SET 
                                    final_bulan_".$bulan." = ((akurat_bulan_01+berkelanjutan_bulan_01+mutakhir_bulan_01)/3)
                                WHERE
                                    sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' 
                                    AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'";
                                
                                DB::connection('sqlsrv_3')->statement($sql_final);
                                
                                    
                                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL]".PHP_EOL;
                            } catch (\Throwable $th) {
                                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL-EXCEPTION]".PHP_EOL;
                            }
                            
                        } catch (\Throwable $th) {
                            //throw $th;
                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL]".PHP_EOL;
                        }


                    }
                }
            }
        }
    }
}
