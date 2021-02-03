<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Console\Commands\SNP\snp060101;
use App\Console\Commands\SNP\snp060200;

class hitungRaporTigaStandarInv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rapor:tiga_standar_inv {--semester_id=20191} {--kode_wilayah=000000} {--sekolah_id=0} {--npsn=0}';

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
        $op_sekolah_id = $this->option('sekolah_id');
        $op_npsn = $this->option('npsn');

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
                
                    $sql = "select * FROM
                        sekolah WITH ( nolock )
                    JOIN ref.bentuk_pendidikan bp WITH ( nolock ) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
                    LEFT JOIN yayasan WITH ( nolock ) ON yayasan.yayasan_id = sekolah.yayasan_id
                    JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                    JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                    JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                    WHERE
                        kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'";

                    if($op_sekolah_id != '0'){
                        $sql .= " AND sekolah.sekolah_id = '".$op_sekolah_id."'";
                    }
                    
                    if($op_npsn != '0'){
                        $sql .= " AND sekolah.npsn = '".$op_npsn."'";
                    }

                    $fetch = DB::select(DB::raw($sql));

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 

                        echo "-----------------------------------".PHP_EOL;

                        $record = $fetch[$iSekolah];

                        $cek_timeline = "SELECT
                                            sekolah_id,
                                            MAX ( jenis_timeline_id ) AS jenis_timeline_id 
                                        FROM
                                            timeline 
                                        WHERE
                                            soft_delete = 0 
                                        AND sekolah_id = '".$record->sekolah_id."'
                                        GROUP BY
                                            sekolah_id";

                        $fetch_timeline = DB::connection('sqlsrv_pmp')->select(DB::raw($cek_timeline));

                        if(sizeof($fetch_timeline) > 0){

                            if((int)$fetch_timeline[0]->jenis_timeline_id < 2){
                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$record->sekolah_id." ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}." [SKIPPED]".PHP_EOL;
                                continue;
                            }

                        }else{
                            echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$record->sekolah_id." ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}." [SKIPPED]".PHP_EOL;
                            continue;
                        }

                        // start of proses hitung rapor mutu dapodik
                        snp060101::index($record->sekolah_id);
                        snp060200::index($record->sekolah_id);
                        // end of proses hitung rapor mutu dapodik

                        $sql_child = "SELECT
                                        sekolah_id,
                                        concat ( LEFT ( urut, 6 ), '00' ) AS urut,
                                        AVG (
                                        isnull( r19, 0 )) AS r19
                                    FROM
                                        master_pmp 
                                    WHERE
                                        sekolah_id = '".$record->sekolah_id."' 
                                        AND LEVEL = 'grandchild' 
                                        AND (
                                            LEFT ( urut, 2 ) = '05'
                                            OR LEFT ( urut, 2 ) = '06'
                                        )
                                    GROUP BY
                                        concat ( LEFT ( urut, 6 ), '00' ),
                                        sekolah_id";

                        $fetch_child = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_child));

                        for ($iChild=0; $iChild < sizeof($fetch_child); $iChild++) { 
                            $sql_update = "update master_pmp set r19 = '".$fetch_child[$iChild]->r19."', last_update = getdate() where sekolah_id = '".$fetch_child[$iChild]->sekolah_id."' and urut = '".$fetch_child[$iChild]->urut."'";

                            try {
                                DB::connection('sqlsrv_pmp')->statement($sql_update);

                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$record->sekolah_id." MASTER_PMP INDIKATOR [BERHASIL]".PHP_EOL;
                            } catch (\Throwable $th) {
                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$record->sekolah_id." MASTER_PMP INDIKATOR [GAGAL]".PHP_EOL;
                            }
                        }
                        
                        $sql_parent = "SELECT
                                            sekolah_id,
                                            concat ( LEFT ( urut, 3 ), '00.00' ) AS urut,
                                            AVG (
                                            isnull( r19, 0 )) AS r19
                                        FROM
                                            master_pmp 
                                        WHERE
                                            sekolah_id = '".$record->sekolah_id."' 
                                            AND LEVEL = 'child' 
                                            AND (
                                                LEFT ( urut, 2 ) = '05'
                                                OR LEFT ( urut, 2 ) = '06'
                                            )
                                        GROUP BY
                                            concat ( LEFT ( urut, 3 ), '00.00' ),
                                            sekolah_id";

                        $fetch_parent = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_parent));

                        for ($iParent=0; $iParent < sizeof($fetch_parent); $iParent++) { 
                            $sql_update = "update master_pmp set r19 = '".$fetch_parent[$iParent]->r19."', last_update = getdate() where sekolah_id = '".$fetch_parent[$iParent]->sekolah_id."' and urut = '".$fetch_parent[$iParent]->urut."'";

                            try {
                                DB::connection('sqlsrv_pmp')->statement($sql_update);

                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$record->sekolah_id." ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}." MASTER_PMP STANDAR [BERHASIL]".PHP_EOL;
                            } catch (\Throwable $th) {
                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$record->sekolah_id." ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}." MASTER_PMP STANDAR [GAGAL]".PHP_EOL;
                            }
                        }

                        echo "-----------------------------------".PHP_EOL;
                    }
                }
            }
        }
    }
}
