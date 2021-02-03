<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class rekapRaporBerkelanjutan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:rekap_rapor_berkelanjutan {--semester_id=20191}';

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
        // $semester_id = '20191';
        $semester_id = $this->option('semester_id');

        //ambil data wilayah
        $provinsi = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
        ->where('wilayah.id_level_wilayah','=',1)
        ->whereNull('wilayah.expired_date')
        ->get();

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
                    
                    $fetch_semester = DB::select(DB::raw("
                        select top 4 * from ref.semester semester order by semester_id desc
                    "));

                    $arrSemester = [];
                    $strQuery = "";
                    $strQueryPersen = "";
                    $strInsert = "";

                    for ($iSem=0; $iSem < sizeof($fetch_semester); $iSem++) { 

                        array_push($arrSemester,$fetch_semester[$iSem]->semester_id);

                        $strQuery .= " SUM ( CASE WHEN semester_id = ".$fetch_semester[$iSem]->semester_id." THEN 1 ELSE 0 END ) AS sync_".$fetch_semester[$iSem]->semester_id.",";
                        $strQueryPersen .= " SUM ( CASE WHEN semester_id = ".$fetch_semester[$iSem]->semester_id." THEN 1 ELSE 0 END )+";
                        $strInsert .= " sync_".$fetch_semester[$iSem]->semester_id.",";
                        // echo "[INF] ".$fetch_semester[$iSem]->semester_id.PHP_EOL;
                    }

                    $strQueryPersen = substr($strQueryPersen,0,strlen($strQueryPersen)-1);
                    $strInsert = substr($strInsert,0,strlen($strInsert)-1);

                    $sql = "SELECT
                        sekolah.sekolah_id,
                        -- sekolah.nama,
                        '".$semester_id."' as semester_id,
                        {$strQuery}
                        sekolah.soft_delete,
                        (
                            (
                                {$strQueryPersen}
                            ) / cast(4 as float) * 100 
                        ) as persen,
                        sekolah.nama,
                        getdate() as tanggal_rekap_terakhir
                    FROM
                        sekolah
                        LEFT JOIN sekolah_longitudinal ON sekolah_longitudinal.sekolah_id = sekolah.sekolah_id 
                        JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                        JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                        JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                        JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah
                    WHERE
                        sekolah_longitudinal.Soft_delete = 0 			
                        -- AND sekolah.Soft_delete = 0 
                        AND kec.kode_wilayah = '".trim($kecamatan[$iKecamatan]->{'kode_wilayah'})."'
                    GROUP BY
                        sekolah.sekolah_id
                        ,sekolah.nama
                        ,sekolah.soft_delete
                        ";

                    // echo "[INF] ".$sql.PHP_EOL;

                    // for ($iSem=0; $iSem < 4; $iSem++) { 
                    //     echo "[INF] ".(substr($semester_id,3,1)-$iSem).$gage.PHP_EOL;
                        
                    //     $gage = ($gage == 1 ? 2 : 1);
                    // }
                    
                    $fetch = DB::select(DB::raw($sql));

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 

                        $strValue = "";
                        $strValueUpdate = "";
                        $countValue = 0;
                        $idxSemester = 0;

                        foreach ($fetch[$iSekolah] as $key => $value) {

                            if($countValue < 7){
                                $strValue .= "'".$value."',";
                                // $countValue++;
                            }

                            if($countValue > 1 && $countValue < 6){
                                $strValueUpdate .= "sync_".$arrSemester[$idxSemester]." = '".$value."',";

                                $idxSemester++;
                            }

                            $countValue++;
                        }

                        $strValue = substr($strValue,0,strlen($strValue)-1);

                        $sql_insert = "IF NOT EXISTS ( SELECT * FROM dbo.rapor_berkelanjutan lanjut WITH ( nolock ) WHERE lanjut.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' AND lanjut.semester_id = '".$fetch[$iSekolah]->{'semester_id'}."' ) 
                            INSERT INTO dbo.rapor_berkelanjutan (
                                sekolah_id, semester_id,{$strInsert}, soft_delete
                            ) values (
                                {$strValue}
                            ) ELSE UPDATE dbo.rapor_berkelanjutan SET 
                                {$strValueUpdate}
                                soft_delete = '".$fetch[$iSekolah]->{'soft_delete'}."' 
                            WHERE
                                sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."'
                                AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'";
                        
                        // echo $sql_insert.PHP_EOL;

                        try {

                            DB::connection('sqlsrv_3')->statement($sql_insert);

                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL-1]".PHP_EOL;

                            $sql_insert_rapor = "IF NOT EXISTS ( SELECT * FROM rekap.rekap_rapor_dapodik_sekolah rapor WITH ( nolock ) WHERE rapor.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' AND rapor.semester_id = '".$fetch[$iSekolah]->{'semester_id'}."' ) 
                            INSERT INTO rekap.rekap_rapor_dapodik_sekolah (
                                sekolah_id, semester_id, rapor_berkelanjutan, soft_delete
                            ) values (
                                '".$fetch[$iSekolah]->{'sekolah_id'}."', 
                                '".$fetch[$iSekolah]->{'semester_id'}."', 
                                '".$fetch[$iSekolah]->{'persen'}."', 
                                '".$fetch[$iSekolah]->{'soft_delete'}."'
                            ) ELSE UPDATE rekap.rekap_rapor_dapodik_sekolah SET 
                                rapor_berkelanjutan = '".$fetch[$iSekolah]->{'persen'}."'
                            WHERE
                                sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."'
                                AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'";

                            try {
                                DB::connection('sqlsrv_3')->statement($sql_insert_rapor);

                                $bulan = substr($fetch[$iSekolah]->{'tanggal_rekap_terakhir'},5,2);

                                // echo $bulan.PHP_EOL;die;

                                DB::connection('sqlsrv_3')->statement("IF NOT EXISTS ( SELECT * FROM rekap.rekap_rapor_dapodik_periodik rekap_rapor WITH ( nolock ) WHERE sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."' ) 
                                INSERT INTO rekap.rekap_rapor_dapodik_periodik (
                                    sekolah_id,
                                    semester_id,
                                    berkelanjutan_bulan_".$bulan."
                                )
                                VALUES
                                (
                                    '".$fetch[$iSekolah]->{'sekolah_id'}."',
                                    '".$fetch[$iSekolah]->{'semester_id'}."',
                                    '".$fetch[$iSekolah]->{'persen'}."'
                                ) ELSE 
                                UPDATE rekap.rekap_rapor_dapodik_periodik 
                                SET 
                                    berkelanjutan_bulan_".$bulan." = '".$fetch[$iSekolah]->{'persen'}."'
                                WHERE
                                    sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' 
                                    AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'");

                                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL-2]".PHP_EOL;
                            } catch (\Throwable $th) {
                                //throw $th;
                                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL-2]".PHP_EOL;
                            }

                            // echo $sql_insert_rapor.PHP_EOL;die;

                            // echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL]".PHP_EOL;
                        } catch (\Throwable $th) {
                            //throw $th;
                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL-1]".PHP_EOL;
                        }

                    }
                }
            }
        }
    }
}
