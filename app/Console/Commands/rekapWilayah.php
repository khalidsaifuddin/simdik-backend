<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class rekapWilayah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:wilayah';

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
        $semester_id = '20191';

        //ambil data wilayah
        $provinsi = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
        ->where('wilayah.id_level_wilayah','=',1)
        ->whereNull('wilayah.expired_date')
        ->get();

        for ($iProvinsi=0; $iProvinsi < sizeof($provinsi); $iProvinsi++) { 
            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] ".$provinsi[$iProvinsi]->{'nama'}.PHP_EOL;

            Redis::set( 'wilayah:'.trim($provinsi[$iProvinsi]->{'kode_wilayah'}), json_encode($provinsi[$iProvinsi]) );
            Redis::lrem( 'wilayah:kode_wilayah:1', 0, trim($provinsi[$iProvinsi]->{'kode_wilayah'}) );
            Redis::lpush( 'wilayah:kode_wilayah:1', trim($provinsi[$iProvinsi]->{'kode_wilayah'}) );
            Redis::lrem( 'wilayah:mst_kode_wilayah:000000', 0, trim($provinsi[$iProvinsi]->{'kode_wilayah'}) );
            Redis::lpush( 'wilayah:mst_kode_wilayah:000000', trim($provinsi[$iProvinsi]->{'kode_wilayah'}) );

            $kabupaten = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
            ->where('wilayah.mst_kode_wilayah','=',$provinsi[$iProvinsi]->{'kode_wilayah'})
            ->whereNull('wilayah.expired_date')
            ->get();

            for ($iKabupaten=0; $iKabupaten < sizeof($kabupaten); $iKabupaten++) { 
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}.PHP_EOL;
            
                Redis::set( 'wilayah:'.trim($kabupaten[$iKabupaten]->{'kode_wilayah'}), json_encode($kabupaten[$iKabupaten]) );
                Redis::lrem( 'wilayah:kode_wilayah:2', 0, trim($kabupaten[$iKabupaten]->{'kode_wilayah'}) );
                Redis::lpush( 'wilayah:kode_wilayah:2', trim($kabupaten[$iKabupaten]->{'kode_wilayah'}) );
                Redis::lrem( 'wilayah:mst_kode_wilayah:'.trim($provinsi[$iProvinsi]->{'kode_wilayah'}), 0, trim($kabupaten[$iKabupaten]->{'kode_wilayah'}) );
                Redis::lpush( 'wilayah:mst_kode_wilayah:'.trim($provinsi[$iProvinsi]->{'kode_wilayah'}), trim($kabupaten[$iKabupaten]->{'kode_wilayah'}) );
                
                $kecamatan = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
                ->where('wilayah.mst_kode_wilayah','=',$kabupaten[$iKabupaten]->{'kode_wilayah'})
                ->whereNull('wilayah.expired_date')
                ->get();

                for ($iKecamatan=0; $iKecamatan < sizeof($kecamatan); $iKecamatan++) { 
                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}.PHP_EOL;

                    Redis::set( 'wilayah:'.trim($kecamatan[$iKecamatan]->{'kode_wilayah'}), json_encode($kecamatan[$iKecamatan]) );
                    Redis::lrem( 'wilayah:kode_wilayah:3', 0, trim($kecamatan[$iKecamatan]->{'kode_wilayah'}) );
                    Redis::lpush( 'wilayah:kode_wilayah:3', trim($kecamatan[$iKecamatan]->{'kode_wilayah'}) );
                    Redis::lrem( 'wilayah:mst_kode_wilayah:'.trim($kabupaten[$iKabupaten]->{'kode_wilayah'}), 0, trim($kecamatan[$iKecamatan]->{'kode_wilayah'}) );
                    Redis::lpush( 'wilayah:mst_kode_wilayah:'.trim($kabupaten[$iKabupaten]->{'kode_wilayah'}), trim($kecamatan[$iKecamatan]->{'kode_wilayah'}) );
                
                }
            }
        }
    }
}
