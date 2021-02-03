<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class rekapRaporSNP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:rapor_snp {--kode_wilayah=000000} {--semester_id=20191}';

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
        $semester_id = $this->option('semester_id');
        $kode_wilayah = $this->option('kode_wilayah');

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
            ->whereNull('wilayah.expired_date')
            ->get();

            for ($iKabupaten=0; $iKabupaten < sizeof($kabupaten); $iKabupaten++) { 
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}.PHP_EOL;
            
                $kecamatan = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
                ->where('wilayah.mst_kode_wilayah','=',$kabupaten[$iKabupaten]->{'kode_wilayah'})
                ->whereNull('wilayah.expired_date')
                ->get();

                // $gage = substr($semester_id,4,1);

                for ($iKecamatan=0; $iKecamatan < sizeof($kecamatan); $iKecamatan++) { 
                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}.PHP_EOL;
                    
                    $sql = "SELECT
                            sekolah.sekolah_id,
                            sekolah.nama,
                            '".$semester_id."' as semester_id,
                            getdate() as tanggal,
                            SUM ( CASE WHEN nomor = '1.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) AS standar_1,
                            SUM ( CASE WHEN nomor = '2.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) AS standar_2,
                            SUM ( CASE WHEN nomor = '3.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) AS standar_3,
                            SUM ( CASE WHEN nomor = '4.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) AS standar_4,
                            SUM ( CASE WHEN nomor = '5.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) AS standar_5,
                            SUM ( CASE WHEN nomor = '6.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) AS standar_6,
                            SUM ( CASE WHEN nomor = '7.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) AS standar_7,
                            SUM ( CASE WHEN nomor = '8.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) AS standar_8,
                            (
                                (
                                SUM ( CASE WHEN nomor = '1.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) + 
                                SUM ( CASE WHEN nomor = '2.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) + 
                                SUM ( CASE WHEN nomor = '3.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) + 
                                SUM ( CASE WHEN nomor = '4.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) + 
                                SUM ( CASE WHEN nomor = '5.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) + 
                                SUM ( CASE WHEN nomor = '6.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) + 
                                SUM ( CASE WHEN nomor = '7.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) + 
                                SUM ( CASE WHEN nomor = '8.' THEN ".($semester_id == '20191' ? 'r18' : ($semester_id == '20192' ? 'r19' : 'r17'))." ELSE 0 END ) 
                            ) / 8 
                            ) AS rapor_snp 
                        FROM
                            master_pmp with(nolock)
                            JOIN sekolah with(nolock) ON sekolah.sekolah_id = master_pmp.sekolah_id 
                            JOIN ref.wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                            JOIN ref.wilayah kab with(nolock) on kab.kode_wilayah = kec.induk_kode_wilayah
                            JOIN ref.wilayah prop with(nolock) on prop.kode_wilayah = kab.induk_kode_wilayah
                        WHERE
                            LEVEL = 'parent' 
                            AND master_pmp.soft_delete = 0 
                            AND sekolah.soft_delete = 0 
                            AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                        GROUP BY
                            sekolah.sekolah_id,
                            sekolah.nama";
                    
                    $fetch = DB::connection('sqlsrv_pmp')->select(DB::raw($sql));

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 
                        try {

                            $sql = "IF NOT EXISTS ( 
                                    SELECT * FROM rekap_rapor_snp WITH ( nolock ) 
                                    WHERE 
                                        sekolah_id = '".$fetch[$iSekolah]->sekolah_id."' 
                                    AND semester_id = '".$fetch[$iSekolah]->semester_id."'
                                    ) 
                                    INSERT INTO rekap_rapor_snp (
                                        sekolah_id,
                                        semester_id,
                                        rapor_snp,
                                        standar_1,
                                        standar_2,
                                        standar_3,
                                        standar_4,
                                        standar_5,
                                        standar_6,
                                        standar_7,
                                        standar_8,
                                        tanggal
                                    ) VALUES (
                                        '".$fetch[$iSekolah]->sekolah_id."',
                                        '".$fetch[$iSekolah]->semester_id."',
                                        '".$fetch[$iSekolah]->rapor_snp."',
                                        '".$fetch[$iSekolah]->standar_1."',
                                        '".$fetch[$iSekolah]->standar_2."',
                                        '".$fetch[$iSekolah]->standar_3."',
                                        '".$fetch[$iSekolah]->standar_4."',
                                        '".$fetch[$iSekolah]->standar_5."',
                                        '".$fetch[$iSekolah]->standar_6."',
                                        '".$fetch[$iSekolah]->standar_7."',
                                        '".$fetch[$iSekolah]->standar_8."',
                                        '".$fetch[$iSekolah]->tanggal."'
                                    ) ELSE 
                                    UPDATE rekap_rapor_snp set 
                                        rapor_snp = '".$fetch[$iSekolah]->rapor_snp."',
                                        standar_1 = '".$fetch[$iSekolah]->standar_1."',
                                        standar_2 = '".$fetch[$iSekolah]->standar_2."',
                                        standar_3 = '".$fetch[$iSekolah]->standar_3."',
                                        standar_4 = '".$fetch[$iSekolah]->standar_4."',
                                        standar_5 = '".$fetch[$iSekolah]->standar_5."',
                                        standar_6 = '".$fetch[$iSekolah]->standar_6."',
                                        standar_7 = '".$fetch[$iSekolah]->standar_7."',
                                        standar_8 = '".$fetch[$iSekolah]->standar_8."',
                                        tanggal = '".$fetch[$iSekolah]->tanggal."'
                                    where 
                                        sekolah_id = '".$fetch[$iSekolah]->sekolah_id."' 
                                    and semester_id = '".$fetch[$iSekolah]->semester_id."'";
                            
                            DB::connection('sqlsrv_2')->statement($sql);

                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL]".PHP_EOL;
                        } catch (\Throwable $th) {
                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL]".PHP_EOL;
                        }
                    }
                }
            }
        }
    }
}
