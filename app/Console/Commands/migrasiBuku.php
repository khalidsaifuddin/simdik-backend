<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class migrasiBuku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buku:migrasi';

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

        $list = file_get_contents('http://simdikapi:8888/list.txt');

        $arrList = explode("\n",$list);

        for ($i=0; $i < sizeof($arrList); $i++) { 
            $record = $arrList[$i];

            $guid = DB::connection('sqlsrv_2')->table('rekap_sekolah')->select(DB::raw('newid() as guid'))->first();

            $arrRecord = explode("/", $record);

            $id = $guid->guid;
            $judul = substr($arrRecord[(sizeof($arrRecord)-1)], 0, (strlen($arrRecord[(sizeof($arrRecord)-1)])-5));
            $keterangan = '';
            $tingkat = 10;
            $mapel = '';
            $tahun = 2019;
            $filename = $arrRecord[(sizeof($arrRecord)-1)];

            // echo $id.",".$judul."".PHP_EOL;
            $execute = DB::connection('sqlsrv_2')->table('buku.buku')->insert([
                'buku_id' => $id,
                'judul' => $judul,
                'keterangan' => $keterangan,
                'tingkat' => $tingkat,
                'mapel' => $mapel,
                'tahun_terbit' => $tahun,
                'filename' => $record
            ]);

            if($execute){
                echo 'BERHASIL'.PHP_EOL;
            }else{
                echo 'GAGAL'.PHP_EOL;
            }
        }

        // echo var_dump($arrList);

        // echo $list;
        die;
    }
}
