<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class normalisasiRaporMutu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rapor:normalisasi_rapor_mutu {--semester_id=20191} {--kode_wilayah=000000}  {--kode_wilayah_kabupaten=000000} {--sekolah_id=0}';

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
        $semester_id = $this->option('semester_id');
        $kode_wilayah = $this->option('kode_wilayah');
        $kode_wilayah_kabupaten = $this->option('kode_wilayah_kabupaten');
        $sekolah_id = $this->option('sekolah_id');

        // echo $semester_id;

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
            // ->get()
            ;

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
                
                    $sql = "select 
                                sekolah.* 
                            from 
                                sekolah 
                            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                            where 
                                kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                            -- AND sekolah.sekolah_id = '20DD2106-2DF5-E011-8BC3-7F093DBE7A1A'
                    ";

                    if($sekolah_id != '0'){
                        $sql .= " AND sekolah.sekolah_id = '".$sekolah_id."'";
                    }

                    $fetch = DB::select(DB::raw($sql));

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 

                        $record = $fetch[$iSekolah];

                        // try {
                            
                            $sql_master_pmp = "select * from master_pmp where sekolah_id = '".$record->sekolah_id."'";
    
                            $fetch_master_pmp = DB::connection('sqlsrv_pmp_dev')->select(DB::raw($sql_master_pmp));

                            for ($iMaster=0; $iMaster < sizeof($fetch_master_pmp); $iMaster++) { 

                                if((int)$fetch_master_pmp[$iMaster]->r18 == 0){

                                    $sql_update = "update 
                                                        master_pmp 
                                                    set 
                                                        r16 = '".$fetch_master_pmp[$iMaster]->r16."',
                                                        r17 = '".$fetch_master_pmp[$iMaster]->r17."',
                                                        r18 = '".$fetch_master_pmp[$iMaster]->r18."',
                                                        last_update = getdate()
                                                    where sekolah_id = '".$fetch_master_pmp[$iMaster]->sekolah_id."'
                                                    and urut = '".$fetch_master_pmp[$iMaster]->urut."'";
                                    
                                    try {
                                        
                                        DB::connection('sqlsrv_pmp')->statement($sql_update);
                                        echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." - ".$fetch_master_pmp[$iMaster]->urut." [BERHASIL]".PHP_EOL;
                                    } catch (\Throwable $th) {
                                        echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." - ".$fetch_master_pmp[$iMaster]->urut." [GAGAL]".PHP_EOL;
                                    }

                                }

                            }
                            
                            // echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL]".PHP_EOL;
                        // } catch (\Throwable $th) {
                        //     echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL]".PHP_EOL;
                        // }

                    }
                }
            }
        }   
    }
}
