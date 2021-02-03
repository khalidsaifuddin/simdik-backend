<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class rekapRaporTerolah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:rapor_terolah';

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
        //ambil dulu datanya dr mysql
        $sql = "SELECT
            sekolah_id,
            avg( r19 ) AS r19 
        FROM
            master_pmp 
        WHERE
            soft_delete = 0 
            AND LEVEL = 'parent' 
            AND sekolah_id IS NOT NULL 
        GROUP BY
            sekolah_id";

        $fetch = DB::connection('mysql_rt')->select(DB::raw($sql));

        for ($i=0; $i < sizeof($fetch); $i++) { 
            // echo ($i+1)." - ".$fetch[$i]->sekolah_id.PHP_EOL;

            if($fetch[$i]->sekolah_id){

                $sql_sekolah = "SELECT
                    * 
                FROM
                    sekolah 
                WHERE
                    sekolah_id = '".$fetch[$i]->sekolah_id."'";
                
                $fetch_sekolah = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_sekolah));
    
                if($fetch_sekolah){
                    $fetch_sekolah[0]->r19 = $fetch[$i]->r19;
                    // echo json_encode($fetch_sekolah[0]).PHP_EOL;
                    $arrKolom = array();

                    foreach ($fetch_sekolah[0] as $key => $value) {
                        $arrKolom[$key] = $value;
                    }


                    try {
                        $fetch_cek = DB::connection('sqlsrv_pmp')->table('rekap.rekap_rapor_19')
                        ->where('sekolah_id','=',$fetch[$i]->sekolah_id)
                        ->get();
    
                        if(sizeof($fetch_cek) > 0){
                            //sudah ada
                            // echo ($i+1)." - sudah ada".PHP_EOL;
                            $exe = DB::connection('sqlsrv_pmp')->table('rekap.rekap_rapor_19')
                            ->where('sekolah_id','=',$fetch[$i]->sekolah_id)
                            ->update($arrKolom);
                        }else{
                            //belum ada
                            // echo ($i+1)." - belum ada".PHP_EOL;
                            $exe = DB::connection('sqlsrv_pmp')->table('rekap.rekap_rapor_19')
                            ->insert($arrKolom);
                        }

                        echo ($i+1)." - ".$fetch[$i]->sekolah_id." (".$fetch[$i]->r19.") - ".($exe ? "Berhasil" : "Gagal").PHP_EOL;

                    } catch (\Throwable $th) {
                        //throw $th;
                        echo ($i+1)." - ".$fetch[$i]->sekolah_id." (".$fetch[$i]->r19.") - gagal exception".PHP_EOL;
                    }

                }

            }


        }
    }
}
