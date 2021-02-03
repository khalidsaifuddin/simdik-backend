<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use App\Console\Commands\SPM\spm0201;
use App\Console\Commands\SPM\spm0202;
use App\Console\Commands\SPM\spm0203;
use App\Console\Commands\SPM\spm0204;
use App\Console\Commands\SPM\spm0301;
use App\Console\Commands\SPM\spm0302;
use App\Console\Commands\SPM\spm0401;
use App\Console\Commands\SPM\spm0402;
use App\Console\Commands\SPM\spm0403;
use App\Console\Commands\SPM\spm0701;
use App\Console\Commands\SPM\spm0801;
use App\Console\Commands\SPM\spm1000;
use App\Console\Commands\SPM\spm1100;
use App\Console\Commands\SPM\spm1500;
use App\Console\Commands\SPM\spm1600;
use App\Console\Commands\SPM\spm1700;
use App\Console\Commands\SPM\spm1801;
use App\Console\Commands\SPM\spm1802;
use App\Console\Commands\SPM\spm1900;
use App\Console\Commands\SPM\spm2001;
use App\Console\Commands\SPM\spm2002;
use App\Console\Commands\SPM\spm2100;
use App\Console\Commands\SPM\spm2200;
use App\Console\Commands\SPM\spm2300;
use App\Console\Commands\SPM\spm2400;
use App\Console\Commands\SPM\spm2500;
use App\Console\Commands\SPM\spm2600;
use App\Console\Commands\SPM\spm2701;
use App\Console\Commands\SPM\spm2702;
use App\Console\Commands\SPM\spm2703;

class rekapSPMSekolah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spm:rekap_spm_sekolah {--semester_id=20191}';

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
        // $semester_id = 20191;
        $semester_id = $this->option('semester_id');
        $provinsi = Redis::lrange('wilayah:kode_wilayah:1',0,-1);

        for ($iProvinsi=0; $iProvinsi < sizeof($provinsi); $iProvinsi++) {
            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] ".$provinsi[$iProvinsi].PHP_EOL;

            $kabupaten = Redis::lrange('wilayah:mst_kode_wilayah:'.$provinsi[$iProvinsi],0,-1);

            for ($iKabupaten=0; $iKabupaten < sizeof($kabupaten); $iKabupaten++) {
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] ".$provinsi[$iProvinsi].' - '.$kabupaten[$iKabupaten].PHP_EOL;

                if(trim($kabupaten[$iKabupaten]) == '116000'){

                spm2703::index($kabupaten[$iKabupaten], $semester_id);
                spm2702::index($kabupaten[$iKabupaten], $semester_id);
                spm2701::index($kabupaten[$iKabupaten], $semester_id);
                spm2600::index($kabupaten[$iKabupaten], $semester_id);
                spm2500::index($kabupaten[$iKabupaten], $semester_id);
                spm2400::index($kabupaten[$iKabupaten], $semester_id);
                spm2300::index($kabupaten[$iKabupaten], $semester_id);
                spm2200::index($kabupaten[$iKabupaten], $semester_id);
                spm2100::index($kabupaten[$iKabupaten], $semester_id);
                spm2002::index($kabupaten[$iKabupaten], $semester_id);
                spm2001::index($kabupaten[$iKabupaten], $semester_id);
                spm1900::index($kabupaten[$iKabupaten], $semester_id);
                spm1802::index($kabupaten[$iKabupaten], $semester_id);
                spm1801::index($kabupaten[$iKabupaten], $semester_id);
                spm1700::index($kabupaten[$iKabupaten], $semester_id);
                spm1600::index($kabupaten[$iKabupaten], $semester_id);
                spm1500::index($kabupaten[$iKabupaten], $semester_id);
                spm1100::index($kabupaten[$iKabupaten], $semester_id);
                spm1000::index($kabupaten[$iKabupaten], $semester_id);
                spm0801::spm_0801($kabupaten[$iKabupaten], $semester_id);
                spm0701::spm_0701($kabupaten[$iKabupaten], $semester_id);
                spm0403::spm_0403($kabupaten[$iKabupaten], $semester_id);
                spm0402::spm_0402($kabupaten[$iKabupaten], $semester_id);
                spm0401::spm_0401($kabupaten[$iKabupaten], $semester_id);
                spm0302::spm_0302($kabupaten[$iKabupaten], $semester_id);
                spm0301::spm_0301($kabupaten[$iKabupaten], $semester_id);
                spm0204::spm_0204($kabupaten[$iKabupaten], $semester_id);
                spm0203::spm_0203($kabupaten[$iKabupaten], $semester_id);
                spm0202::spm_0202($kabupaten[$iKabupaten], $semester_id);
                spm0201::spm_0201($kabupaten[$iKabupaten], $semester_id);
                
                }
            }
        }
    }
}
