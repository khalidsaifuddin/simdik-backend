<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class rekapRaporSekolahSumWilayah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:rekap_rapor_sekolah_wilayah';

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
        $semester_id = 20191;
        $provinsi = Redis::lrange('wilayah:kode_wilayah:1',0,-1);

        for ($iProvinsi=0; $iProvinsi < sizeof($provinsi); $iProvinsi++) {
            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] ".$provinsi[$iProvinsi].PHP_EOL;

            $recordRaporProvinsi = Redis::lrange('rekap_rapor_dapodik_sekolah:'.$provinsi[$iProvinsi].':'.$semester_id, 0, -1);

            $rapor_pd = 0;
            $rapor_ptk = 0;
            $rapor_sarpras = 0;
            $rapor_rombel = 0;
            $rapor_sekolah = 0;
            $rapor_akhir = 0;

            $jumlah_record_total = 0;

            for ($iRecordRaporProvinsi=0; $iRecordRaporProvinsi < sizeof($recordRaporProvinsi); $iRecordRaporProvinsi++) { 
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iRecordRaporProvinsi+1)."/".sizeof($recordRaporProvinsi)."] ".$provinsi[$iProvinsi]." - ".$recordRaporProvinsi[$iRecordRaporProvinsi].PHP_EOL;

                $recordRaporSekolah = json_decode( Redis::get('rekap_rapor_dapodik_sekolah:'.$recordRaporProvinsi[$iRecordRaporProvinsi]) );
                // echo "[INF] ".$recordRaporSekolah.PHP_EOL;
                if((int)$recordRaporSekolah->{'soft_delete'} == 0){
                    $rapor_pd = ( $rapor_pd+round((float)$recordRaporSekolah->{'rapor_pd'},2) );
                    $rapor_ptk = ( $rapor_ptk+round((float)$recordRaporSekolah->{'rapor_ptk'},2) );
                    $rapor_sarpras = ( $rapor_sarpras+round((float)$recordRaporSekolah->{'rapor_sarpras'},2) );
                    $rapor_rombel = ( $rapor_rombel+round((float)$recordRaporSekolah->{'rapor_rombel'},2) );
                    $rapor_sekolah = ( $rapor_sekolah+round((float)$recordRaporSekolah->{'rapor_sekolah'},2) );
                    $rapor_akhir = ( $rapor_akhir+round((float)$recordRaporSekolah->{'rapor_akhir'},2) );

                    $jumlah_record_total++;
                }
            }

            $rapor_pd = $rapor_pd/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
            $rapor_ptk = $rapor_ptk/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
            $rapor_sarpras = $rapor_sarpras/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
            $rapor_rombel = $rapor_rombel/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
            $rapor_sekolah = $rapor_sekolah/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
            $rapor_akhir = $rapor_akhir/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);

            $arrWilayah = json_decode( Redis::get('wilayah:'.$provinsi[$iProvinsi]) );

            $arrWilayah->{'rapor_pd'} = $rapor_pd;
            $arrWilayah->{'rapor_ptk'} = $rapor_ptk;
            $arrWilayah->{'rapor_sekolah'} = $rapor_sekolah;
            $arrWilayah->{'rapor_sarpras'} = $rapor_sarpras;
            $arrWilayah->{'rapor_rombel'} = $rapor_rombel;
            $arrWilayah->{'rapor_akhir'} = $rapor_akhir;
            $arrWilayah->{'last_update'} = date('Y-m-d H:i:s');

            // echo "[INF] ". json_encode(str_split(strtolower(str_replace(' ','',$arrWilayah->nama)))) .PHP_EOL;die;
            $arrString = str_split(strtolower(str_replace(' ','',$arrWilayah->nama)));

            for ($xString=0; $xString < sizeOf($arrString); $xString++) { 
                
                for ($iString=$xString; $iString < sizeOf($arrString); $iString++) { 

                    $strKeyword = '';
                    // echo $arrString[$iString].PHP_EOL;
                    for ($jString=$xString; $jString <= $iString; $jString++) { 
                        $strKeyword.= $arrString[$jString];
                    }
    
                    // for ($jString=1; $jString <= $iString; $jString++) { 
                    //     echo $arrString[$jString];
                    // }
                    echo $strKeyword;
                    echo PHP_EOL;
                }
            
                // echo PHP_EOL;
            }
            echo $provinsi[$iProvinsi];
            die;
            // echo "[INF] ".json_encode(explode('', $arrWilayah->{'nama'})).PHP_EOL;
            Redis::set( 'rekap_rapor_dapodik_wilayah:'.$provinsi[$iProvinsi].':'.$semester_id, json_encode($arrWilayah) );

            $kabupaten = Redis::lrange('wilayah:mst_kode_wilayah:'.$provinsi[$iProvinsi],0,-1);

            for ($iKabupaten=0; $iKabupaten < sizeof($kabupaten); $iKabupaten++) {
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] ".$provinsi[$iProvinsi].' - '.$kabupaten[$iKabupaten].PHP_EOL;
            
                $recordRaporKabupaten = Redis::lrange('rekap_rapor_dapodik_sekolah:'.$kabupaten[$iKabupaten].':'.$semester_id, 0, -1);

                $rapor_pd = 0;
                $rapor_ptk = 0;
                $rapor_sarpras = 0;
                $rapor_rombel = 0;
                $rapor_sekolah = 0;
                $rapor_akhir = 0;

                $jumlah_record_total = 0;

                for ($iRecordRaporKabupaten=0; $iRecordRaporKabupaten < sizeof($recordRaporKabupaten); $iRecordRaporKabupaten++) { 
                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iRecordRaporKabupaten+1)."/".sizeof($recordRaporKabupaten)."] ".$kabupaten[$iKabupaten]." - ".$recordRaporKabupaten[$iRecordRaporKabupaten].PHP_EOL;

                    $recordRaporSekolah = json_decode( Redis::get('rekap_rapor_dapodik_sekolah:'.$recordRaporKabupaten[$iRecordRaporKabupaten]) );
                    // echo "[INF] ".$recordRaporSekolah.PHP_EOL;
                    if((int)$recordRaporSekolah->{'soft_delete'} == 0){
                        $rapor_pd = ( $rapor_pd+round((float)$recordRaporSekolah->{'rapor_pd'},2) );
                        $rapor_ptk = ( $rapor_ptk+round((float)$recordRaporSekolah->{'rapor_ptk'},2) );
                        $rapor_sarpras = ( $rapor_sarpras+round((float)$recordRaporSekolah->{'rapor_sarpras'},2) );
                        $rapor_rombel = ( $rapor_rombel+round((float)$recordRaporSekolah->{'rapor_rombel'},2) );
                        $rapor_sekolah = ( $rapor_sekolah+round((float)$recordRaporSekolah->{'rapor_sekolah'},2) );
                        $rapor_akhir = ( $rapor_akhir+round((float)$recordRaporSekolah->{'rapor_akhir'},2) );
    
                        $jumlah_record_total++;
                    }
                }
    
                $rapor_pd = $rapor_pd/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                $rapor_ptk = $rapor_ptk/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                $rapor_sarpras = $rapor_sarpras/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                $rapor_rombel = $rapor_rombel/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                $rapor_sekolah = $rapor_sekolah/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                $rapor_akhir = $rapor_akhir/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);

                $arrWilayah = json_decode( Redis::get('wilayah:'.$kabupaten[$iKabupaten]) );

                $arrWilayah->{'rapor_pd'} = $rapor_pd;
                $arrWilayah->{'rapor_ptk'} = $rapor_ptk;
                $arrWilayah->{'rapor_sekolah'} = $rapor_sekolah;
                $arrWilayah->{'rapor_sarpras'} = $rapor_sarpras;
                $arrWilayah->{'rapor_rombel'} = $rapor_rombel;
                $arrWilayah->{'rapor_akhir'} = $rapor_akhir;
                $arrWilayah->{'last_update'} = date('Y-m-d H:i:s');

                Redis::set( 'rekap_rapor_dapodik_wilayah:'.$kabupaten[$iKabupaten].':'.$semester_id, json_encode($arrWilayah) );

                $kecamatan = Redis::lrange('wilayah:mst_kode_wilayah:'.$kabupaten[$iKabupaten],0,-1);

                for ($iKecamatan=0; $iKecamatan < sizeof($kecamatan); $iKecamatan++) {
                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi].' - '.$kabupaten[$iKabupaten].' - '.$kecamatan[$iKecamatan].PHP_EOL;
                    
                    $recordRaporKecamatan = Redis::lrange('rekap_rapor_dapodik_sekolah:'.$kecamatan[$iKecamatan].':'.$semester_id, 0, -1);

                    $rapor_pd = 0;
                    $rapor_ptk = 0;
                    $rapor_sarpras = 0;
                    $rapor_rombel = 0;
                    $rapor_sekolah = 0;
                    $rapor_akhir = 0;

                    $jumlah_record_total = 0;

                    for ($iRecordRaporKecamatan=0; $iRecordRaporKecamatan < sizeof($recordRaporKecamatan); $iRecordRaporKecamatan++) { 
                        echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iRecordRaporKabupaten+1)."/".sizeof($recordRaporKabupaten)."] [".($iRecordRaporKecamatan+1)."/".sizeof($recordRaporKecamatan)."] ".$kecamatan[$iKecamatan]." - ".$recordRaporKecamatan[$iRecordRaporKecamatan].PHP_EOL;
                    
                        $recordRaporSekolah = json_decode( Redis::get('rekap_rapor_dapodik_sekolah:'.$recordRaporKecamatan[$iRecordRaporKecamatan]) );
                        
                        if((int)$recordRaporSekolah->{'soft_delete'} == 0){
                            $rapor_pd = ( $rapor_pd+round((float)$recordRaporSekolah->{'rapor_pd'},2) );
                            $rapor_ptk = ( $rapor_ptk+round((float)$recordRaporSekolah->{'rapor_ptk'},2) );
                            $rapor_sarpras = ( $rapor_sarpras+round((float)$recordRaporSekolah->{'rapor_sarpras'},2) );
                            $rapor_rombel = ( $rapor_rombel+round((float)$recordRaporSekolah->{'rapor_rombel'},2) );
                            $rapor_sekolah = ( $rapor_sekolah+round((float)$recordRaporSekolah->{'rapor_sekolah'},2) );
                            $rapor_akhir = ( $rapor_akhir+round((float)$recordRaporSekolah->{'rapor_akhir'},2) );
        
                            $jumlah_record_total++;
                        }
                    }
                    
                    $rapor_pd = $rapor_pd/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                    $rapor_ptk = $rapor_ptk/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                    $rapor_sarpras = $rapor_sarpras/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                    $rapor_rombel = $rapor_rombel/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                    $rapor_sekolah = $rapor_sekolah/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);
                    $rapor_akhir = $rapor_akhir/((int)$jumlah_record_total > 0 ? (int)$jumlah_record_total : 1);

                    $arrWilayah = json_decode( Redis::get('wilayah:'.$kecamatan[$iKecamatan]) );

                    $arrWilayah->{'rapor_pd'} = $rapor_pd;
                    $arrWilayah->{'rapor_ptk'} = $rapor_ptk;
                    $arrWilayah->{'rapor_sekolah'} = $rapor_sekolah;
                    $arrWilayah->{'rapor_sarpras'} = $rapor_sarpras;
                    $arrWilayah->{'rapor_rombel'} = $rapor_rombel;
                    $arrWilayah->{'rapor_akhir'} = $rapor_akhir;
                    $arrWilayah->{'last_update'} = date('Y-m-d H:i:s');

                    Redis::set( 'rekap_rapor_dapodik_wilayah:'.$kecamatan[$iKecamatan].':'.$semester_id, json_encode($arrWilayah) );
                }
            }
        } 
    }
}
