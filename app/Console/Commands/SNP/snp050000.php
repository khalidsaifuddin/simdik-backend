<?php

namespace App\Console\Commands\SNP;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class snp050000
{
    static function index($sekolah_id, $nuptk_kepsek, $npsn){
        // $sql_sekolah = '';

        $sql = 'select top 1 
                    (avg_peda/cast(100 as float)*7) as avg_peda,
                    (avg_prof/cast(100 as float)*7) as avg_prof
                from 
                    ukg_sekolah 
                where 
                    npsn = \''.$npsn.'\'';
        
        $fetch = DB::connection('sqlsrv_27')->select($sql);

        
        if(sizeof($fetch) > 0){
            // echo json_encode($fetch[0]);

        //     foreach ($fetch[0] as $key => $value) {
        //         if($key != 'sekolah_id'){

                    $sql_update = "update 
                                        master_pmp 
                                    set 
                                        r19 = '".$fetch[0]->avg_peda."', 
                                        last_update = DATEADD(mi, 30, getdate())
                                    where 
                                        sekolah_id = '".$sekolah_id."' 
                                    and urut = '05.01.05' 
                                    and r19 < ".$fetch[0]->avg_peda."";
        
                    try {
                        DB::connection('sqlsrv_pmp')->statement($sql_update);
        
                        echo "[INF] [".$sekolah_id."] MASTER_PMP 05.01.05 [BERHASIL]".PHP_EOL;
                    } catch (\Throwable $th) {
                        echo "[INF] [".$sekolah_id."] MASTER_PMP 05.01.05 [GAGAL]".PHP_EOL;
                    }


                    $sql_update = "update 
                                        master_pmp 
                                    set 
                                        r19 = '".$fetch[0]->avg_prof."', 
                                        last_update = DATEADD(mi, 30, getdate())
                                    where 
                                        sekolah_id = '".$sekolah_id."' 
                                    and urut = '05.01.07' 
                                    and r19 < ".$fetch[0]->avg_prof."";
        
                    try {
                        DB::connection('sqlsrv_pmp')->statement($sql_update);
        
                        echo "[INF] [".$sekolah_id."] MASTER_PMP 05.01.07 [BERHASIL]".PHP_EOL;
                    } catch (\Throwable $th) {
                        echo "[INF] [".$sekolah_id."] MASTER_PMP 05.01.07 [GAGAL]".PHP_EOL;
                    }

        //         }   
        //     }

        }
            

    }
}