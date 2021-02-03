<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Console\Commands\SNP\snp060101;

class hitungRaporTigaStandar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rapor:tiga_standar {--semester_id=20191} {--kode_wilayah=000000} {--sekolah_id=0}';

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

    public function ceknull($data)
    {
        $hasil='';
        if (is_null($data))
        {
            $hasil='n.a.';
        }
        else
        {
            $hasil=$data;
        }

        return $hasil;
    }

    public function maksi($data)
    {
        if ($data>=1){return 1;}
        else
            {return $data;}
    }

    public function maksi_s($data)
    {
    switch ($data) {
        case '0':
            # BAIK
            return 1;
            break;
        case 'BAIK':
            # BAIK
            return 1;
            break;
        case '1':
            # RUSAK RINGAN
            return (3/4);
            break;
        case 'RUSAK RINGAN':
            # RUSAK RINGAN
            return (3/4);
            break;
        case '2':
            # RUSAK SEDANG
            return (2/4);
            break;
        case 'RUSAK SEDANG':
            # RUSAK SEDANG
            return (2/4);
            break;
        case '3':
            # RUSAK BERAT
            return (1/4);
            break;
        case 'RUSAK BERAT':
            # RUSAK BERAT
            return (1/4);
            break;
        case '4':
            # LULUH
            return 0;
            break;
        default:
            # code...
            return 0;
            break;
        }
    }


    public function stdrombel($data, $jenjang)
    {
        switch ($jenjang) {
            case 5:
                $hasil=1;
                if ($data<6)
                    {$hasil=$data/6;}
                if ($data>24)
                    {$hasil=24/$data;}				
                break;
            case 6:
                $hasil=1;
                if ($data<3)
                    {$hasil=$data/3;}
                if ($data>24)
                    {$hasil=24/$data;}				
                break;
            case 13:
                $hasil=1;
                if ($data<3)
                    {$hasil=$data/3;}
                if ($data>27)
                    {$hasil=27/$data;}				
                break;
            case 15:
                $hasil=1;
                if ($data<3)
                    {$hasil=$data/3;}
                break;

            default:
                $hasil=0;
                break;
        }
        return $hasil;
    }

    public function stdrasiolahan($data,$rombel,$jenjang)
    {
        switch ($jenjang) {
            case 5:
                if (($data/$rombel)<4.1)
                    {$hasil=($data/$rombel)/4.1;}
                else
                    {$hasil=1;}				
                break;
            case 6:
                if (($data/$rombel)<4.3)
                    {$hasil=($data/$rombel)/4.3;}
                else
                    {$hasil=1;}				
                break;
            case 13:
                if (($data/$rombel)<4.7)
                    {$hasil=($data/$rombel)/4.7;}
                else
                    {$hasil=1;}				
                break;
            case 15:
                if (($data/$rombel)<4.7)
                    {$hasil=($data/$rombel)/4.7;}
                else
                    {$hasil=1;}				
                break;

            default:
                $hasil=0;
                break;
        }
        return $hasil;
    }

    public function statuslahan($data)
    {
        if ($data)
            {return 7;}
        else
            {return 0;}
    }

    public function milik($data)
    {
        if ($data=='Milik')
            {return 7;}
        else
        if ($data=='Bukan Milik')
            {return 0;}
        else
            {return 'n.a';}
    }

    public function stdrasiogd($data,$rombel,$jenjang)
    {
        switch ($jenjang) {
            case 5:
                if (($data/$rombel)<3.4)
                    {$hasil=($data/$rombel)/3.4;}
                else
                    {$hasil=1;}				
                break;
            case 6:
                if (($data/$rombel)<3.7)
                    {$hasil=($data/$rombel)/3.7;}
                else
                    {$hasil=1;}				
                break;
            case 13:
                if (($data/$rombel)<4.1)
                    {$hasil=($data/$rombel)/4.1;}
                else
                    {$hasil=1;}				
                break;
            case 15:
                if (($data/$rombel)<4.1)
                    {$hasil=($data/$rombel)/4.1;}
                else
                    {$hasil=1;}				
                break;

            default:
                $hasil=0;
                break;
        }
        return $hasil;
    }

    public function stdpln($data, $jenjang)
    {
        switch ($jenjang) {
            case 5:
                $hasil=1;
                if ($data<900)
                    {$hasil=$data/900;}
                break;
            default:
                $hasil=1;
                if ($data<1300)
                    {$hasil=$data/1300;}
                break;
        }
        return $hasil;
    }

    public function luasgudang($data, $jenjang)
    {
        switch ($jenjang) {
            case 5:
                $hasil=1;
                if ($data<18)
                    {$hasil=$data/18;}
                break;
            default:
                $hasil=1;
                if ($data<21)
                    {$hasil=$data/21;}
                break;
        }
        return $hasil;
    }

    public function stdruangguru($data, $jenjang)
    {
        switch ($jenjang) {
            case 5:
                $hasil=1;
                if ($data<32)
                    {$hasil=$data/32;}
                break;
            case 6:
                $hasil=1;
                if ($data<48)
                    {$hasil=$data/48;}
                break;
            case 13:
                $hasil=1;
                if ($data<72)
                    {$hasil=$data/72;}
                break;
            case 15:
                $hasil=1;
                if ($data<72)
                    {$hasil=$data/72;}
                break;

            default:
                $hasil=0;
                break;
        }
        return $hasil;
    }

    public function stdjamban($data, $siswa, $jenjang, $jenis)
    {
        if($siswa==0){$siswa=100000;} 	
        switch ($jenis) {
            case 1:
                if ($jenjang==5)
                {return $data/($siswa/60);}
            else
                {return $data/($siswa/40);}
                break;
            
            default:
                if ($jenjang==5)
                {return $data/($siswa/50);}
            else
                {return $data/($siswa/30);}
                break;
        }
    }

    public function stdkloset($data)
    {
        switch ($data) {
            case 'Leher Angsa':
                return 1;
                break;
            case 'Cubluk dengan Tutup':
                return (4/5);
                break;
            case 'Cubluk Tanpa Tutup':
                return (3/5);
                break;
            case 'Jamban menggantung di atas sungai':
                return (2/5);
                break;
            case 'Menggantung di Atas Sungai':
                return (1/5);
                break;		
            default:
                # code...
                return 0;
                break;
        }
    }

    public function cekbaginol($data)
    {
        if($data==0||is_null($data)||$data=='')
            {return 10000000;}
        else
            {return $data;}
    }

    
    public function ke_tigastandar($sekolah_id, $no_instrumen, $data_instrumen, $urutan=0, $total=0)
    {
        // mysqli_query($db_link,"INSERT INTO tigastandar (ids, id_quest, nilai) VALUES ('".$sekolah_id."', '".$no_instrumen."', '".$data_instrumen."')");
        $sql = "IF NOT EXISTS ( SELECT * FROM dm.tigastandar WITH ( nolock ) WHERE ids = '".$sekolah_id."' and id_quest = '".$no_instrumen."') INSERT INTO dm.tigastandar(ids, id_quest, nilai, create_date, last_update, soft_delete, updater_id, last_sync) VALUES ('".$sekolah_id."', '".$no_instrumen."', '".$data_instrumen."', getdate(), getdate(), 0, null, '1990-01-01 00:00:00') else UPDATE dm.tigastandar set nilai = '".$data_instrumen."', last_update = getdate() where ids = '".$sekolah_id."' and id_quest = '".$no_instrumen."'";
    
        try {
            DB::connection('sqlsrv_pmp')->statement($sql);

            echo "[INF] [".$urutan."/".$total."] ".$sekolah_id."-".$no_instrumen." TIGASTANDAR [BERHASIL]".PHP_EOL;
        } catch (\Throwable $th) {
            echo $sql.PHP_EOL;
            // die;
            // echo "[INF] ".$sekolah_id."-".$no_instrumen." [GAGAL]";
        }
    }

    
    function ke_ptksp($sekolah_id, $no_instrumen, $data_instrumen, $urutan=0, $total=0)
    {
        // mysqli_query($db_link,"INSERT INTO ptksp (ids, kdsi, nilai) VALUES ('".$sekolah_id."', '".$no_instrumen."', '".$data_instrumen."')");
        //echo "INSERT INTO tigastandar (ids, id_quest, nilai) VALUES ('".$sekolah_id."', '".$no_instrumen."', '".$data_instrumen."')<br>";
        $sql = "IF NOT EXISTS ( SELECT * FROM dm.ptksp WITH ( nolock ) WHERE ids = '".$sekolah_id."' and kdsi = '".$no_instrumen."') INSERT INTO dm.ptksp (ids, kdsi, nilai, create_date, last_update, soft_delete, updater_id, last_sync) VALUES ('".$sekolah_id."', '".$no_instrumen."', '".$data_instrumen."', getdate(), getdate(), 0, null, '1990-01-01 00:00:00') ELSE UPDATE dm.ptksp set nilai = '".$data_instrumen."', last_update = getdate() where ids = '".$sekolah_id."' and kdsi = '".$no_instrumen."'";
    
        try {
            DB::connection('sqlsrv_pmp')->statement($sql);

            echo "[INF] [".$urutan."/".$total."] ".$sekolah_id."-".$no_instrumen." PTKSP [BERHASIL]".PHP_EOL;
        } catch (\Throwable $th) {
            echo $sql.PHP_EOL;
            // die;
            // echo "[INF] ".$sekolah_id."-".$no_instrumen." [GAGAL]";
        }

        $sql2 = "update master_pmp set r19 = '".$data_instrumen."', last_update = getdate() where sekolah_id = '".$sekolah_id."' and nomor = '".$no_instrumen."'";

        try {
            DB::connection('sqlsrv_pmp')->statement($sql2);

            echo "[INF] [".$urutan."/".$total."] ".$sekolah_id."-".$no_instrumen." MASTER_PMP [BERHASIL]".PHP_EOL;
        } catch (\Throwable $th) {
            echo $sql2.PHP_EOL;
            // die;
            // echo "[INF] ".$sekolah_id."-".$no_instrumen." [GAGAL]";
        }
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

                    // echo $sql." ".PHP_EOL;
                    
                    $fetch = DB::select(DB::raw($sql));

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 
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
                                continue;
                            }

                        }else{
                            continue;
                        }


                        $sekolah_id = $record->sekolah_id;
                        $bentuk_pendidikan_id = $record->bentuk_pendidikan_id;

                        $sql_pd = "SELECT TOP 1 * FROM dm.pd WHERE sekolah_id='".$record->sekolah_id."'";
                        $fetch_pd = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_pd));

                        // echo json_encode($fetch_pd);die;

                        if(sizeof($fetch_pd) > 0){

                            //Jumlah siswa menerima PIP
                            $col_64=$fetch_pd[0]->col_64;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 10
                            $col_65=$fetch_pd[0]->col_65;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 10
                            $col_66=$fetch_pd[0]->col_66;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 10
                            $col_67=$fetch_pd[0]->col_67;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 10
                            $col_68=$fetch_pd[0]->col_68;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 10
                            $col_69=$fetch_pd[0]->col_69;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Inggris Kelas 10
                            $col_70=$fetch_pd[0]->col_70;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya Kelas 10
                            $col_71=$fetch_pd[0]->col_71;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya Kelas 10
                            $col_72=$fetch_pd[0]->col_72;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sejarah Indonesia Kelas 10
                            $col_73=$fetch_pd[0]->col_73;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya dan Kewirausahaan Kelas 10
                            $col_74=$fetch_pd[0]->col_74;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika (Peminatan) Kelas 10
                            $col_75=$fetch_pd[0]->col_75;
                            //Rata-rata Nilai Rapor Mata Pelajaran Biologi Kelas 10
                            $col_76=$fetch_pd[0]->col_76;
                            //Rata-rata Nilai Rapor Mata Pelajaran Fisika Kelas 10
                            $col_77=$fetch_pd[0]->col_77;
                            //Rata-rata Nilai Rapor Mata Pelajaran Kimia Kelas 10
                            $col_78=$fetch_pd[0]->col_78;
                            //Rata-rata Nilai Rapor Mata Pelajaran Geografi Kelas 10
                            $col_79=$fetch_pd[0]->col_79;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sejarah Kelas 10
                            $col_80=$fetch_pd[0]->col_80;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sosiologi dan Antropologi Kelas 10
                            $col_81=$fetch_pd[0]->col_81;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ekonomi Kelas 10
                            $col_82=$fetch_pd[0]->col_82;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 10
                            $col_83=$fetch_pd[0]->col_83;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Inggris Kelas 10
                            $col_84=$fetch_pd[0]->col_84;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 10
                            $col_85=$fetch_pd[0]->col_85;
                            //Rata-rata Nilai Rapor Mata Pelajaran Antropologi Kelas 10
                            $col_86=$fetch_pd[0]->col_86;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendalaman Kelas 10
                            $col_87=$fetch_pd[0]->col_87;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 11
                            $col_88=$fetch_pd[0]->col_88;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 11
                            $col_89=$fetch_pd[0]->col_89;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 11
                            $col_90=$fetch_pd[0]->col_90;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 11
                            $col_91=$fetch_pd[0]->col_91;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 11
                            $col_92=$fetch_pd[0]->col_92;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Inggris Kelas 11
                            $col_93=$fetch_pd[0]->col_93;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya Kelas 11
                            $col_94=$fetch_pd[0]->col_94;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya Kelas 11
                            $col_95=$fetch_pd[0]->col_95;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sejarah Indonesia Kelas 11
                            $col_96=$fetch_pd[0]->col_96;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya dan Kewirausahaan Kelas 11
                            $col_97=$fetch_pd[0]->col_97;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika (Peminatan) Kelas 11
                            $col_98=$fetch_pd[0]->col_98;
                            //Rata-rata Nilai Rapor Mata Pelajaran Biologi Kelas 11
                            $col_99=$fetch_pd[0]->col_99;
                            //Rata-rata Nilai Rapor Mata Pelajaran Fisika Kelas 11
                            $col_100=$fetch_pd[0]->col_100;
                            //Rata-rata Nilai Rapor Mata Pelajaran Kimia Kelas 11
                            $col_101=$fetch_pd[0]->col_101;
                            //Rata-rata Nilai Rapor Mata Pelajaran Geografi Kelas 11
                            $col_102=$fetch_pd[0]->col_102;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sejarah Kelas 11
                            $col_103=$fetch_pd[0]->col_103;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sosiologi dan Antropologi Kelas 11
                            $col_104=$fetch_pd[0]->col_104;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ekonomi Kelas 11
                            $col_105=$fetch_pd[0]->col_105;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 11
                            $col_106=$fetch_pd[0]->col_106;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Inggris Kelas 11
                            $col_107=$fetch_pd[0]->col_107;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 11
                            $col_108=$fetch_pd[0]->col_108;
                            //Rata-rata Nilai Rapor Mata Pelajaran Antropologi Kelas 11
                            $col_109=$fetch_pd[0]->col_109;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendalaman Kelas 11
                            $col_110=$fetch_pd[0]->col_110;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 12
                            $col_111=$fetch_pd[0]->col_111;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 12
                            $col_112=$fetch_pd[0]->col_112;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 12
                            $col_113=$fetch_pd[0]->col_113;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 12
                            $col_114=$fetch_pd[0]->col_114;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 12
                            $col_115=$fetch_pd[0]->col_115;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Inggris Kelas 12
                            $col_116=$fetch_pd[0]->col_116;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya Kelas 12
                            $col_117=$fetch_pd[0]->col_117;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya Kelas 12
                            $col_118=$fetch_pd[0]->col_118;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sejarah Indonesia Kelas 12
                            $col_119=$fetch_pd[0]->col_119;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya dan Kewirausahaan Kelas 12
                            $col_120=$fetch_pd[0]->col_120;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika (Peminatan) Kelas 12
                            $col_121=$fetch_pd[0]->col_121;
                            //Rata-rata Nilai Rapor Mata Pelajaran Biologi Kelas 12
                            $col_122=$fetch_pd[0]->col_122;
                            //Rata-rata Nilai Rapor Mata Pelajaran Fisika Kelas 12
                            $col_123=$fetch_pd[0]->col_123;
                            //Rata-rata Nilai Rapor Mata Pelajaran Kimia Kelas 12
                            $col_124=$fetch_pd[0]->col_124;
                            //Rata-rata Nilai Rapor Mata Pelajaran Geografi Kelas 12
                            $col_125=$fetch_pd[0]->col_125;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sejarah Kelas 12
                            $col_126=$fetch_pd[0]->col_126;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sosiologi dan Antropologi Kelas 12
                            $col_127=$fetch_pd[0]->col_127;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ekonomi Kelas 12
                            $col_128=$fetch_pd[0]->col_128;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 12
                            $col_129=$fetch_pd[0]->col_129;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Inggris Kelas 12
                            $col_130=$fetch_pd[0]->col_130;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 12
                            $col_131=$fetch_pd[0]->col_131;
                            //Rata-rata Nilai Rapor Mata Pelajaran Antropologi Kelas 12
                            $col_132=$fetch_pd[0]->col_132;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendalaman Kelas 12
                            $col_133=$fetch_pd[0]->col_133;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 13
                            $col_134=$fetch_pd[0]->col_134;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 13
                            $col_135=$fetch_pd[0]->col_135;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 13
                            $col_136=$fetch_pd[0]->col_136;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 13
                            $col_137=$fetch_pd[0]->col_137;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 13
                            $col_138=$fetch_pd[0]->col_138;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Inggris Kelas 13
                            $col_139=$fetch_pd[0]->col_139;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya Kelas 13
                            $col_140=$fetch_pd[0]->col_140;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya Kelas 13
                            $col_141=$fetch_pd[0]->col_141;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sejarah Indonesia Kelas 13
                            $col_142=$fetch_pd[0]->col_142;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya dan Kewirausahaan Kelas 13
                            $col_143=$fetch_pd[0]->col_143;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika (Peminatan) Kelas 13
                            $col_144=$fetch_pd[0]->col_144;
                            //Rata-rata Nilai Rapor Mata Pelajaran Biologi Kelas 13
                            $col_145=$fetch_pd[0]->col_145;
                            //Rata-rata Nilai Rapor Mata Pelajaran Fisika Kelas 13
                            $col_146=$fetch_pd[0]->col_146;
                            //Rata-rata Nilai Rapor Mata Pelajaran Kimia Kelas 13
                            $col_147=$fetch_pd[0]->col_147;
                            //Rata-rata Nilai Rapor Mata Pelajaran Geografi Kelas 13
                            $col_148=$fetch_pd[0]->col_148;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sejarah Kelas 13
                            $col_149=$fetch_pd[0]->col_149;
                            //Rata-rata Nilai Rapor Mata Pelajaran Sosiologi dan Antropologi Kelas 13
                            $col_150=$fetch_pd[0]->col_150;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ekonomi Kelas 13
                            $col_151=$fetch_pd[0]->col_151;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 13
                            $col_152=$fetch_pd[0]->col_152;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Inggris Kelas 13
                            $col_153=$fetch_pd[0]->col_153;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 13
                            $col_154=$fetch_pd[0]->col_154;
                            //Rata-rata Nilai Rapor Mata Pelajaran Antropologi Kelas 13
                            $col_155=$fetch_pd[0]->col_155;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendalaman Kelas 13
                            $col_156=$fetch_pd[0]->col_156;
                            //Jumlah siswa kelas 10
                            $col_7=$fetch_pd[0]->col_7;
                            //Jumlah siswa kelas 11
                            $col_8=$fetch_pd[0]->col_8;
                            //Jumlah siswa kelas 12
                            $col_9=$fetch_pd[0]->col_9;
                            //Jumlah siswa kelas 13
                            $col_10=$fetch_pd[0]->col_10;
                            //Jumlah siswa laki-laki
                            $col_14=$fetch_pd[0]->col_14;
                            //Jumlah siswa perempuan
                            $col_15=$fetch_pd[0]->col_15;
                            //Rata-rata Nilai UN/S Mata Pelajaran Teori Kejuruan
                            $col_640=$fetch_pd[0]->col_640;
                            //Jumlah siswa kelas 1
                            $col_650=$fetch_pd[0]->col_650;
                            //Jumlah siswa kelas 2
                            $col_651=$fetch_pd[0]->col_651;
                            //Jumlah siswa kelas 3
                            $col_652=$fetch_pd[0]->col_652;
                            //Jumlah siswa kelas 4
                            $col_653=$fetch_pd[0]->col_653;
                            //Jumlah siswa kelas 5
                            $col_654=$fetch_pd[0]->col_654;
                            //Jumlah siswa kelas 6
                            $col_655=$fetch_pd[0]->col_655;
                            //Jumlah siswa kelas 7
                            $col_656=$fetch_pd[0]->col_656;
                            //Jumlah siswa kelas 8
                            $col_657=$fetch_pd[0]->col_657;
                            //Jumlah siswa kelas 9
                            $col_658=$fetch_pd[0]->col_658;
                            //Jumlah peserta lulus UN/S
                            $col_1=$fetch_pd[0]->col_1;
                            //Rata-rata Nilai UN/S Mata Pelajaran Bahasa Indonesia
                            $col_20=$fetch_pd[0]->col_20;
                            //Rata-rata Nilai UN/S Mata Pelajaran Matematika
                            $col_21=$fetch_pd[0]->col_21;
                            //Rata-rata Nilai UN/S Mata Pelajaran Ilmu Pengetahuan Alam
                            $col_22=$fetch_pd[0]->col_22;
                            //Rata-rata Nilai UN/S Mata Pelajaran Bahasa Inggris
                            $col_23=$fetch_pd[0]->col_23;
                            //Rata-rata Nilai UN/S Mata Pelajaran Kimia
                            $col_24=$fetch_pd[0]->col_24;
                            //Rata-rata Nilai UN/S Mata Pelajaran Biologi
                            $col_25=$fetch_pd[0]->col_25;
                            //Rata-rata Nilai UN/S Mata Pelajaran Fisika
                            $col_26=$fetch_pd[0]->col_26;
                            //Rata-rata Nilai UN/S Mata Pelajaran Geografi
                            $col_27=$fetch_pd[0]->col_27;
                            //Rata-rata Nilai UN/S Mata Pelajaran Sosiologi
                            $col_28=$fetch_pd[0]->col_28;
                            //Rata-rata Nilai UN/S Mata Pelajaran Ekonomi
                            $col_29=$fetch_pd[0]->col_29;
                            //Rata-rata Nilai UN/S Mata Pelajaran Sasta Bahasa Indonesia
                            $col_30=$fetch_pd[0]->col_30;
                            //Rata-rata Nilai UN/S Mata Pelajaran Antropologi
                            $col_31=$fetch_pd[0]->col_31;
                            //Rata-rata Nilai UN/S Mata Pelajaran Bahasa Asing
                            $col_32=$fetch_pd[0]->col_32;
                            //Jumlah siswa kelas 1 laki-laki
                            $col_762=$fetch_pd[0]->col_762;
                            //Jumlah siswa kelas 1 perempuan
                            $col_763=$fetch_pd[0]->col_763;
                            //Jumlah siswa kelas 2 laki-laki
                            $col_764=$fetch_pd[0]->col_764;
                            //Jumlah siswa kelas 2 perempuan
                            $col_765=$fetch_pd[0]->col_765;
                            //Jumlah siswa kelas 3 laki-laki
                            $col_766=$fetch_pd[0]->col_766;
                            //Jumlah siswa kelas 3 perempuan
                            $col_767=$fetch_pd[0]->col_767;
                            //Jumlah siswa kelas 4 laki-laki
                            $col_768=$fetch_pd[0]->col_768;
                            //Jumlah siswa kelas 4 perempuan
                            $col_769=$fetch_pd[0]->col_769;
                            //Jumlah siswa kelas 5 laki-laki
                            $col_770=$fetch_pd[0]->col_770;
                            //Jumlah siswa kelas 5 perempuan
                            $col_771=$fetch_pd[0]->col_771;
                            //Jumlah siswa kelas 6 laki-laki
                            $col_772=$fetch_pd[0]->col_772;
                            //Jumlah siswa kelas 6 perempuan
                            $col_773=$fetch_pd[0]->col_773;
                            //Jumlah siswa kelas 7 laki-laki
                            $col_774=$fetch_pd[0]->col_774;
                            //Jumlah siswa kelas 7 perempuan
                            $col_775=$fetch_pd[0]->col_775;
                            //Jumlah siswa kelas 8 laki-laki
                            $col_776=$fetch_pd[0]->col_776;
                            //Jumlah siswa kelas 8 perempuan
                            $col_777=$fetch_pd[0]->col_777;
                            //Jumlah siswa kelas 9 laki-laki
                            $col_778=$fetch_pd[0]->col_778;
                            //Jumlah siswa kelas 9 perempuan
                            $col_779=$fetch_pd[0]->col_779;
                            //Rata-rata Nilai Rapor Kelas 1
                            $col_886=$fetch_pd[0]->col_886;
                            //Rata-rata Nilai Rapor Kelas 2
                            $col_887=$fetch_pd[0]->col_887;
                            //Rata-rata Nilai Rapor Kelas 3
                            $col_888=$fetch_pd[0]->col_888;
                            //Rata-rata Nilai Rapor Kelas 4
                            $col_889=$fetch_pd[0]->col_889;
                            //Rata-rata Nilai Rapor Kelas 5
                            $col_890=$fetch_pd[0]->col_890;
                            //Rata-rata Nilai Rapor Kelas 6
                            $col_891=$fetch_pd[0]->col_891;
                            //Rata-rata Nilai Rapor Kelas 7
                            $col_892=$fetch_pd[0]->col_892;
                            //Rata-rata Nilai Rapor Kelas 8
                            $col_893=$fetch_pd[0]->col_893;
                            //Rata-rata Nilai Rapor Kelas 9
                            $col_894=$fetch_pd[0]->col_894;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 1
                            $col_895=$fetch_pd[0]->col_895;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 2
                            $col_896=$fetch_pd[0]->col_896;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 3
                            $col_897=$fetch_pd[0]->col_897;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 4
                            $col_898=$fetch_pd[0]->col_898;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 5
                            $col_899=$fetch_pd[0]->col_899;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 6
                            $col_900=$fetch_pd[0]->col_900;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 7
                            $col_901=$fetch_pd[0]->col_901;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 8
                            $col_902=$fetch_pd[0]->col_902;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Indonesia Kelas 9
                            $col_903=$fetch_pd[0]->col_903;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Inggris Kelas 7
                            $col_904=$fetch_pd[0]->col_904;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Inggris Kelas 8
                            $col_905=$fetch_pd[0]->col_905;
                            //Rata-rata Nilai Rapor Mata Pelajaran Bahasa Inggris Kelas 9
                            $col_906=$fetch_pd[0]->col_906;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 1
                            $col_907=$fetch_pd[0]->col_907;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 2
                            $col_908=$fetch_pd[0]->col_908;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 3
                            $col_909=$fetch_pd[0]->col_909;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 4
                            $col_910=$fetch_pd[0]->col_910;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 5
                            $col_911=$fetch_pd[0]->col_911;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 6
                            $col_912=$fetch_pd[0]->col_912;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 7
                            $col_913=$fetch_pd[0]->col_913;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 8
                            $col_914=$fetch_pd[0]->col_914;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Alam Kelas 9
                            $col_915=$fetch_pd[0]->col_915;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 1
                            $col_916=$fetch_pd[0]->col_916;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 2
                            $col_917=$fetch_pd[0]->col_917;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 3
                            $col_918=$fetch_pd[0]->col_918;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 4
                            $col_919=$fetch_pd[0]->col_919;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 5
                            $col_920=$fetch_pd[0]->col_920;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 6
                            $col_921=$fetch_pd[0]->col_921;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 7
                            $col_922=$fetch_pd[0]->col_922;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 8
                            $col_923=$fetch_pd[0]->col_923;
                            //Rata-rata Nilai Rapor Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 9
                            $col_924=$fetch_pd[0]->col_924;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 1
                            $col_925=$fetch_pd[0]->col_925;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 2
                            $col_926=$fetch_pd[0]->col_926;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 3
                            $col_927=$fetch_pd[0]->col_927;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 4
                            $col_928=$fetch_pd[0]->col_928;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 5
                            $col_929=$fetch_pd[0]->col_929;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 6
                            $col_930=$fetch_pd[0]->col_930;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 7
                            $col_931=$fetch_pd[0]->col_931;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 8
                            $col_932=$fetch_pd[0]->col_932;
                            //Rata-rata Nilai Rapor Mata Pelajaran Matematika Kelas 9
                            $col_933=$fetch_pd[0]->col_933;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 1
                            $col_934=$fetch_pd[0]->col_934;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 2
                            $col_935=$fetch_pd[0]->col_935;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 3
                            $col_936=$fetch_pd[0]->col_936;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 4
                            $col_937=$fetch_pd[0]->col_937;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 5
                            $col_938=$fetch_pd[0]->col_938;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 6
                            $col_939=$fetch_pd[0]->col_939;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 7
                            $col_940=$fetch_pd[0]->col_940;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 8
                            $col_941=$fetch_pd[0]->col_941;
                            //Rata-rata Nilai Rapor Mata Pelajaran Muatan Lokal Kelas 9
                            $col_942=$fetch_pd[0]->col_942;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 1
                            $col_943=$fetch_pd[0]->col_943;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 2
                            $col_944=$fetch_pd[0]->col_944;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 3
                            $col_945=$fetch_pd[0]->col_945;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 4
                            $col_946=$fetch_pd[0]->col_946;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 5
                            $col_947=$fetch_pd[0]->col_947;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 6
                            $col_948=$fetch_pd[0]->col_948;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 7
                            $col_949=$fetch_pd[0]->col_949;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 8
                            $col_950=$fetch_pd[0]->col_950;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 9
                            $col_951=$fetch_pd[0]->col_951;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 1
                            $col_952=$fetch_pd[0]->col_952;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 2
                            $col_953=$fetch_pd[0]->col_953;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 3
                            $col_954=$fetch_pd[0]->col_954;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 4
                            $col_955=$fetch_pd[0]->col_955;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 5
                            $col_956=$fetch_pd[0]->col_956;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 6
                            $col_957=$fetch_pd[0]->col_957;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 7
                            $col_958=$fetch_pd[0]->col_958;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 8
                            $col_959=$fetch_pd[0]->col_959;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 9
                            $col_960=$fetch_pd[0]->col_960;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 1
                            $col_961=$fetch_pd[0]->col_961;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 2
                            $col_962=$fetch_pd[0]->col_962;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 3
                            $col_963=$fetch_pd[0]->col_963;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 4
                            $col_964=$fetch_pd[0]->col_964;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 5
                            $col_965=$fetch_pd[0]->col_965;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 6
                            $col_966=$fetch_pd[0]->col_966;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 7
                            $col_967=$fetch_pd[0]->col_967;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 8
                            $col_968=$fetch_pd[0]->col_968;
                            //Rata-rata Nilai Rapor Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 9
                            $col_969=$fetch_pd[0]->col_969;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya Kelas 7
                            $col_970=$fetch_pd[0]->col_970;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya Kelas 8
                            $col_971=$fetch_pd[0]->col_971;
                            //Rata-rata Nilai Rapor Mata Pelajaran Prakarya Kelas 9
                            $col_972=$fetch_pd[0]->col_972;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya dan Prakarya Kelas 1
                            $col_973=$fetch_pd[0]->col_973;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya dan Prakarya Kelas 2
                            $col_974=$fetch_pd[0]->col_974;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya dan Prakarya Kelas 3
                            $col_975=$fetch_pd[0]->col_975;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya dan Prakarya Kelas 4
                            $col_976=$fetch_pd[0]->col_976;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya dan Prakarya Kelas 5
                            $col_977=$fetch_pd[0]->col_977;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya dan Prakarya Kelas 6
                            $col_978=$fetch_pd[0]->col_978;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya Kelas 7
                            $col_979=$fetch_pd[0]->col_979;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya Kelas 8
                            $col_980=$fetch_pd[0]->col_980;
                            //Rata-rata Nilai Rapor Mata Pelajaran Seni Budaya Kelas 9
                            $col_981=$fetch_pd[0]->col_981;
                            //Rata-rata Nilai Rapor Mata Pelajaran Dasar Bidang Keahlian Kelas 10
                            $col_983=$fetch_pd[0]->col_983;
                            //Rata-rata Nilai Rapor Mata Pelajaran Dasar Program Keahlian Kelas 10
                            $col_984=$fetch_pd[0]->col_984;
                            //Rata-rata Nilai Rapor Mata Pelajaran Paket Keahlian Kelas 10
                            $col_985=$fetch_pd[0]->col_985;
                            //Rata-rata Nilai Rapor Mata Pelajaran Dasar Bidang Keahlian Kelas 11
                            $col_986=$fetch_pd[0]->col_986;
                            //Rata-rata Nilai Rapor Mata Pelajaran Dasar Program Keahlian Kelas 11
                            $col_987=$fetch_pd[0]->col_987;
                            //Rata-rata Nilai Rapor Mata Pelajaran Paket Keahlian Kelas 11
                            $col_988=$fetch_pd[0]->col_988;
                            //Rata-rata Nilai Rapor Mata Pelajaran Dasar Bidang Keahlian Kelas 12
                            $col_989=$fetch_pd[0]->col_989;
                            //Rata-rata Nilai Rapor Mata Pelajaran Dasar Program Keahlian Kelas 12
                            $col_990=$fetch_pd[0]->col_990;
                            //Rata-rata Nilai Rapor Mata Pelajaran Paket Keahlian Kelas 12
                            $col_991=$fetch_pd[0]->col_991;
                            //Rata-rata Nilai Rapor Mata Pelajaran Dasar Bidang Keahlian Kelas 13
                            $col_992=$fetch_pd[0]->col_992;
                            //Rata-rata Nilai Rapor Mata Pelajaran Dasar Program Keahlian Kelas 13
                            $col_993=$fetch_pd[0]->col_993;
                            //Rata-rata Nilai Rapor Mata Pelajaran Paket Keahlian Kelas 13
                            $col_994=$fetch_pd[0]->col_994;
                            //Rata-rata Nilai Rapor Kelas 10
                            $col_1063=$fetch_pd[0]->col_1063;
                            //Rata-rata Nilai Rapor Kelas 11
                            $col_1064=$fetch_pd[0]->col_1064;
                            //Rata-rata Nilai Rapor Kelas 12
                            $col_1065=$fetch_pd[0]->col_1065;
                            //Rata-rata Nilai Rapor Kelas 13
                            $col_1066=$fetch_pd[0]->col_1066;
                            //Jumlah siswa kelas 10 laki-laki
                            $col_1042=$fetch_pd[0]->col_1042;
                            //Jumlah siswa kelas 10 perempuan
                            $col_1043=$fetch_pd[0]->col_1043;
                            //Jumlah siswa kelas 11 laki-laki
                            $col_1044=$fetch_pd[0]->col_1044;
                            //Jumlah siswa kelas 11 perempuan
                            $col_1045=$fetch_pd[0]->col_1045;
                            //Jumlah siswa kelas 12 laki-laki
                            $col_1046=$fetch_pd[0]->col_1046;
                            //Jumlah siswa kelas 12 perempuan
                            $col_1047=$fetch_pd[0]->col_1047;
                            //Jumlah siswa kelas 13 laki-laki
                            $col_1048=$fetch_pd[0]->col_1048;
                            //Jumlah siswa kelas 13 perempuan
                            $col_1049=$fetch_pd[0]->col_1049;
                            //Jumlah peserta UN/S
                            $col_1050=$fetch_pd[0]->col_1050;
                            //Indeks Integritas Ujian Nasional
                            $col_186=$fetch_pd[0]->col_186;

                        }else{
                            $col_64=0;
                            //Rata00
                            $col_65=0;
                            //Rata00
                            $col_66=0;
                            //Rata00
                            $col_67=0;
                            //Rata00
                            $col_68=0;
                            //Rata00
                            $col_69=0;
                            //Rata00
                            $col_70=0;
                            //Rata00
                            $col_71=0;
                            //Rata00
                            $col_72=0;
                            //Rata00
                            $col_73=0;
                            //Rata00
                            $col_74=0;
                            //Rata00
                            $col_75=0;
                            //Rata00
                            $col_76=0;
                            //Rata00
                            $col_77=0;
                            //Rata00
                            $col_78=0;
                            //Rata00
                            $col_79=0;
                            //Rata00
                            $col_80=0;
                            //Rata00
                            $col_81=0;
                            //Rata00
                            $col_82=0;
                            //Rata00
                            $col_83=0;
                            //Rata00
                            $col_84=0;
                            //Rata00
                            $col_85=0;
                            //Rata00
                            $col_86=0;
                            //Rata00
                            $col_87=0;
                            //Rata01
                            $col_88=0;
                            //Rata01
                            $col_89=0;
                            //Rata01
                            $col_90=0;
                            //Rata01
                            $col_91=0;
                            //Rata01
                            $col_92=0;
                            //Rata01
                            $col_93=0;
                            //Rata01
                            $col_94=0;
                            //Rata01
                            $col_95=0;
                            //Rata01
                            $col_96=0;
                            //Rata01
                            $col_97=0;
                            //Rata01
                            $col_98=0;
                            //Rata01
                            $col_99=0;
                            //Rata01
                            $col_100=0;
                            //Rata01
                            $col_101=0;
                            //Rata01
                            $col_102=0;
                            //Rata01
                            $col_103=0;
                            //Rata01
                            $col_104=0;
                            //Rata01
                            $col_105=0;
                            //Rata01
                            $col_106=0;
                            //Rata01
                            $col_107=0;
                            //Rata01
                            $col_108=0;
                            //Rata01
                            $col_109=0;
                            //Rata01
                            $col_110=0;
                            //Rata02
                            $col_111=0;
                            //Rata02
                            $col_112=0;
                            //Rata02
                            $col_113=0;
                            //Rata02
                            $col_114=0;
                            //Rata02
                            $col_115=0;
                            //Rata02
                            $col_116=0;
                            //Rata02
                            $col_117=0;
                            //Rata02
                            $col_118=0;
                            //Rata02
                            $col_119=0;
                            //Rata02
                            $col_120=0;
                            //Rata02
                            $col_121=0;
                            //Rata02
                            $col_122=0;
                            //Rata02
                            $col_123=0;
                            //Rata02
                            $col_124=0;
                            //Rata02
                            $col_125=0;
                            //Rata02
                            $col_126=0;
                            //Rata02
                            $col_127=0;
                            //Rata02
                            $col_128=0;
                            //Rata02
                            $col_129=0;
                            //Rata02
                            $col_130=0;
                            //Rata02
                            $col_131=0;
                            //Rata02
                            $col_132=0;
                            //Rata02
                            $col_133=0;
                            //Rata03
                            $col_134=0;
                            //Rata03
                            $col_135=0;
                            //Rata03
                            $col_136=0;
                            //Rata03
                            $col_137=0;
                            //Rata03
                            $col_138=0;
                            //Rata03
                            $col_139=0;
                            //Rata03
                            $col_140=0;
                            //Rata03
                            $col_141=0;
                            //Rata03
                            $col_142=0;
                            //Rata03
                            $col_143=0;
                            //Rata03
                            $col_144=0;
                            //Rata03
                            $col_145=0;
                            //Rata03
                            $col_146=0;
                            //Rata03
                            $col_147=0;
                            //Rata03
                            $col_148=0;
                            //Rata03
                            $col_149=0;
                            //Rata03
                            $col_150=0;
                            //Rata03
                            $col_151=0;
                            //Rata03
                            $col_152=0;
                            //Rata03
                            $col_153=0;
                            //Rata03
                            $col_154=0;
                            //Rata03
                            $col_155=0;
                            //Rata03
                            $col_156=0;
                            //Jumlah00
                            $col_7=0;
                            //Jumlah01
                            $col_8=0;
                            //Jumlah02
                            $col_9=0;
                            //Jumlah03
                            $col_10=0;
                            //Jumlah0i
                            $col_14=0;
                            //Jumlah0n
                            $col_15=0;
                            //Rata0n
                            $col_640=0;
                            //Jumlah01
                            $col_650=0;
                            //Jumlah02
                            $col_651=0;
                            //Jumlah03
                            $col_652=0;
                            //Jumlah04
                            $col_653=0;
                            //Jumlah05
                            $col_654=0;
                            //Jumlah06
                            $col_655=0;
                            //Jumlah07
                            $col_656=0;
                            //Jumlah08
                            $col_657=0;
                            //Jumlah09
                            $col_658=0;
                            //Jumlah0S
                            $col_1=0;
                            //Rata0a
                            $col_20=0;
                            //Rata0a
                            $col_21=0;
                            //Rata0m
                            $col_22=0;
                            //Rata0s
                            $col_23=0;
                            //Rata0a
                            $col_24=0;
                            //Rata0i
                            $col_25=0;
                            //Rata0a
                            $col_26=0;
                            //Rata0i
                            $col_27=0;
                            //Rata0i
                            $col_28=0;
                            //Rata0i
                            $col_29=0;
                            //Rata0a
                            $col_30=0;
                            //Rata0i
                            $col_31=0;
                            //Rata0g
                            $col_32=0;
                            //Jumlah0i
                            $col_762=0;
                            //Jumlah0n
                            $col_763=0;
                            //Jumlah0i
                            $col_764=0;
                            //Jumlah0n
                            $col_765=0;
                            //Jumlah0i
                            $col_766=0;
                            //Jumlah0n
                            $col_767=0;
                            //Jumlah0i
                            $col_768=0;
                            //Jumlah0n
                            $col_769=0;
                            //Jumlah0i
                            $col_770=0;
                            //Jumlah0n
                            $col_771=0;
                            //Jumlah0i
                            $col_772=0;
                            //Jumlah0n
                            $col_773=0;
                            //Jumlah0i
                            $col_774=0;
                            //Jumlah0n
                            $col_775=0;
                            //Jumlah0i
                            $col_776=0;
                            //Jumlah0n
                            $col_777=0;
                            //Jumlah0i
                            $col_778=0;
                            //Jumlah0n
                            $col_779=0;
                            //Rata01
                            $col_886=0;
                            //Rata02
                            $col_887=0;
                            //Rata03
                            $col_888=0;
                            //Rata04
                            $col_889=0;
                            //Rata05
                            $col_890=0;
                            //Rata06
                            $col_891=0;
                            //Rata07
                            $col_892=0;
                            //Rata08
                            $col_893=0;
                            //Rata09
                            $col_894=0;
                            //Rata01
                            $col_895=0;
                            //Rata02
                            $col_896=0;
                            //Rata03
                            $col_897=0;
                            //Rata04
                            $col_898=0;
                            //Rata05
                            $col_899=0;
                            //Rata06
                            $col_900=0;
                            //Rata07
                            $col_901=0;
                            //Rata08
                            $col_902=0;
                            //Rata09
                            $col_903=0;
                            //Rata07
                            $col_904=0;
                            //Rata08
                            $col_905=0;
                            //Rata09
                            $col_906=0;
                            //Rata01
                            $col_907=0;
                            //Rata02
                            $col_908=0;
                            //Rata03
                            $col_909=0;
                            //Rata04
                            $col_910=0;
                            //Rata05
                            $col_911=0;
                            //Rata06
                            $col_912=0;
                            //Rata07
                            $col_913=0;
                            //Rata08
                            $col_914=0;
                            //Rata09
                            $col_915=0;
                            //Rata01
                            $col_916=0;
                            //Rata02
                            $col_917=0;
                            //Rata03
                            $col_918=0;
                            //Rata04
                            $col_919=0;
                            //Rata05
                            $col_920=0;
                            //Rata06
                            $col_921=0;
                            //Rata07
                            $col_922=0;
                            //Rata08
                            $col_923=0;
                            //Rata09
                            $col_924=0;
                            //Rata01
                            $col_925=0;
                            //Rata02
                            $col_926=0;
                            //Rata03
                            $col_927=0;
                            //Rata04
                            $col_928=0;
                            //Rata05
                            $col_929=0;
                            //Rata06
                            $col_930=0;
                            //Rata07
                            $col_931=0;
                            //Rata08
                            $col_932=0;
                            //Rata09
                            $col_933=0;
                            //Rata01
                            $col_934=0;
                            //Rata02
                            $col_935=0;
                            //Rata03
                            $col_936=0;
                            //Rata04
                            $col_937=0;
                            //Rata05
                            $col_938=0;
                            //Rata06
                            $col_939=0;
                            //Rata07
                            $col_940=0;
                            //Rata08
                            $col_941=0;
                            //Rata09
                            $col_942=0;
                            //Rata01
                            $col_943=0;
                            //Rata02
                            $col_944=0;
                            //Rata03
                            $col_945=0;
                            //Rata04
                            $col_946=0;
                            //Rata05
                            $col_947=0;
                            //Rata06
                            $col_948=0;
                            //Rata07
                            $col_949=0;
                            //Rata08
                            $col_950=0;
                            //Rata09
                            $col_951=0;
                            //Rata01
                            $col_952=0;
                            //Rata02
                            $col_953=0;
                            //Rata03
                            $col_954=0;
                            //Rata04
                            $col_955=0;
                            //Rata05
                            $col_956=0;
                            //Rata06
                            $col_957=0;
                            //Rata07
                            $col_958=0;
                            //Rata08
                            $col_959=0;
                            //Rata09
                            $col_960=0;
                            //Rata01
                            $col_961=0;
                            //Rata02
                            $col_962=0;
                            //Rata03
                            $col_963=0;
                            //Rata04
                            $col_964=0;
                            //Rata05
                            $col_965=0;
                            //Rata06
                            $col_966=0;
                            //Rata07
                            $col_967=0;
                            //Rata08
                            $col_968=0;
                            //Rata09
                            $col_969=0;
                            //Rata07
                            $col_970=0;
                            //Rata08
                            $col_971=0;
                            //Rata09
                            $col_972=0;
                            //Rata01
                            $col_973=0;
                            //Rata02
                            $col_974=0;
                            //Rata03
                            $col_975=0;
                            //Rata04
                            $col_976=0;
                            //Rata05
                            $col_977=0;
                            //Rata06
                            $col_978=0;
                            //Rata07
                            $col_979=0;
                            //Rata08
                            $col_980=0;
                            //Rata09
                            $col_981=0;
                            //Rata00
                            $col_983=0;
                            //Rata00
                            $col_984=0;
                            //Rata00
                            $col_985=0;
                            //Rata01
                            $col_986=0;
                            //Rata01
                            $col_987=0;
                            //Rata01
                            $col_988=0;
                            //Rata02
                            $col_989=0;
                            //Rata02
                            $col_990=0;
                            //Rata02
                            $col_991=0;
                            //Rata03
                            $col_992=0;
                            //Rata03
                            $col_993=0;
                            //Rata03
                            $col_994=0;
                            //Rata00
                            $col_1063=0;
                            //Rata01
                            $col_1064=0;
                            //Rata02
                            $col_1065=0;
                            //Rata03
                            $col_1066=0;
                            //Jumlah0i
                            $col_1042=0;
                            //Jumlah0n
                            $col_1043=0;
                            //Jumlah0i
                            $col_1044=0;
                            //Jumlah0n
                            $col_1045=0;
                            //Jumlah0i
                            $col_1046=0;
                            //Jumlah0n
                            $col_1047=0;
                            //Jumlah0i
                            $col_1048=0;
                            //Jumlah0n
                            $col_1049=0;
                            //Jumlah0S
                            $col_1050=0;
                            //Indeks0l
                            $col_186=0;
                        }

                        $sql_prasarana = "SELECT TOP 1 * FROM dm.prasarana WHERE sekolah_id='".$record->sekolah_id."'";
                        $fetch_prasarana = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_prasarana));

                        // echo json_encode($fetch_prasarana);die;

                        if(sizeof($fetch_prasarana) > 0){
                            //Jumlah kelas yang dilengkapi meja siswa [ruang kelas] [perabot]
                            $col_160=$fetch_prasarana[0]->col_160;
                            //Luas ruang guru [ruang guru] [ruangan]
                            $col_1097=$fetch_prasarana[0]->col_1097;
                            //Luas koridor sekolah [koridor sekolah] [ruangan]
                            $col_1099=$fetch_prasarana[0]->col_1099;
                            //Jumlah ruang kelas dalam kondisi rusak ringan [ruang kelas] [ruangan]
                            $col_1051=$fetch_prasarana[0]->col_1051;
                            //Jumlah ruang kelas dalam kondisi rusak sedang [ruang kelas] [ruangan]
                            $col_1052=$fetch_prasarana[0]->col_1052;
                            //Jumlah ruang kelas dalam kondisi rusak berat [ruang kelas] [ruangan]
                            $col_1053=$fetch_prasarana[0]->col_1053;
                            //Jumlah ruang perpustakaan rusak ringan [ruang perpustakaan] [ruangan]
                            $col_1054=$fetch_prasarana[0]->col_1054;
                            //Jumlah ruang perpustakaan rusak sedang [ruang perpustakaan] [ruangan]
                            $col_1055=$fetch_prasarana[0]->col_1055;
                            //Luas ruang pimpinan [ruang pimpinan] [ruangan]
                            $col_1056=$fetch_prasarana[0]->col_1056;
                            //Jumlah ruang pimpinan berkondisi rusak ringan [ruang pimpinan] [ruangan]
                            $col_1057=$fetch_prasarana[0]->col_1057;
                            //Jumlah ruang pimpinan berkondisi rusak sedang [ruang pimpinan] [ruangan]
                            $col_1058=$fetch_prasarana[0]->col_1058;
                            //Jumlah ruang pimpinan berkondisi rusak berat [ruang pimpinan] [ruangan]
                            $col_1059=$fetch_prasarana[0]->col_1059;
                            //Jumlah jamban untuk siswa laki-laki [Jamban] [ruangan]
                            $col_1060=$fetch_prasarana[0]->col_1060;
                            //Jumlah jamban untuk siswa perempuan [Jamban] [ruangan]
                            $col_1061=$fetch_prasarana[0]->col_1061;
                            //Kondisi ruang/kelas laboratorium [ruang praktik gambar teknik] [ruangan]
                            $col_1019=$fetch_prasarana[0]->col_1019;
                            //Jumlah ruang praktik yang tersedia [ruang pembelajaran khusus] [ruangan]
                            $col_1028=$fetch_prasarana[0]->col_1028;
                            //Jumlah lapangan praktik yang tersedia [ruang pembelajaran khusus] [ruangan]
                            $col_1029=$fetch_prasarana[0]->col_1029;
                            //Rata-rata luas ruang praktik [ruang pembelajaran khusus] [ruangan]
                            $col_1030=$fetch_prasarana[0]->col_1030;
                            //Ketersediaan air [ruang laboratorium IPA] [ruangan]
                            $col_780=$fetch_prasarana[0]->col_780;
                            //Jumlah tempat ibadah yang disediakan [tempat ibadah] [ruangan]
                            $col_362=$fetch_prasarana[0]->col_362;
                            //Luas tempat ibadah [tempat ibadah] [ruangan]
                            $col_363=$fetch_prasarana[0]->col_363;
                            //Luas ruang organisasi kesiswaan [ruang organisasi kesiswaan] [ruangan]
                            $col_426=$fetch_prasarana[0]->col_426;
                            //Kondisi ruang/kelas laboratorium [ruang laboratorium bahasa] [ruangan]
                            $col_510=$fetch_prasarana[0]->col_510;
                            //Kondisi ruang/kelas laboratorium [ruang laboratorium biologi] [ruangan]
                            $col_432=$fetch_prasarana[0]->col_432;
                            //Kondisi ruang/kelas laboratorium [ruang laboratorium fisika] [ruangan]
                            $col_452=$fetch_prasarana[0]->col_452;
                            //Kondisi ruang/kelas laboratorium [ruang laboratorium kimia] [ruangan]
                            $col_471=$fetch_prasarana[0]->col_471;
                            //Kondisi ruang/kelas laboratorium [ruang laboratorium komputer] [ruangan]
                            $col_491=$fetch_prasarana[0]->col_491;
                            //Total jumlah jamban yang berfungsi
                            $col_16=$fetch_prasarana[0]->col_16;
                            //Jumlah ruang kelas dalam kondisi baik [ruang kelas] [ruangan]
                            $col_17=$fetch_prasarana[0]->col_17;
                            //Jumlah ruang pimpinan berkondisi baik [ruang pimpinan] [ruangan]
                            $col_18=$fetch_prasarana[0]->col_18;
                            //Jumlah ruang perpustakaan baik [ruang perpustakaan] [ruangan]
                            $col_19=$fetch_prasarana[0]->col_19;
                            //Jumlah ruang kelas [ruang kelas] [ruangan]
                            $col_11=$fetch_prasarana[0]->col_11;
                            //Rata-rata luas ruang kelas [ruang kelas] [ruangan]
                            $col_12=$fetch_prasarana[0]->col_12;
                            //Luas ruang perpustakaan [ruang perpustakaan] [ruangan]
                            $col_13=$fetch_prasarana[0]->col_13;
                            //Kondisi ruang perpustakaan [ruang perpustakaan] [ruangan]
                            $col_54=$fetch_prasarana[0]->col_54;
                            //Jumlah kelas yang dilengkapi kursi guru [ruang kelas] [perabot]
                            $col_162=$fetch_prasarana[0]->col_162;
                            //Jumlah kelas yang dilengkapi meja guru [ruang kelas] [perabot]
                            $col_163=$fetch_prasarana[0]->col_163;
                            //Jumlah kelas yang dilengkapi lemari [ruang kelas] [perabot]
                            $col_164=$fetch_prasarana[0]->col_164;
                            //Jumlah kelas yang dilengkapi papan panjang [ruang kelas] [perabot]
                            $col_166=$fetch_prasarana[0]->col_166;
                            //Jumlah kelas yang dilengkapi alat peraga [ruang kelas] [peralatan pendidikan]
                            $col_167=$fetch_prasarana[0]->col_167;
                            //Jumlah kelas yang dilengkapi papan tulis [ruang kelas] [media pendidikan]
                            $col_168=$fetch_prasarana[0]->col_168;
                            //Jumlah kelas yang dilengkapi tempat sampah [ruang kelas] [perlengkapan lain]
                            $col_169=$fetch_prasarana[0]->col_169;
                            //Jumlah kelas yang dilengkapi tempat cuci tangan [ruang kelas] [perlengkapan lain]
                            $col_170=$fetch_prasarana[0]->col_170;
                            //Jumlah kelas yang dilengkapi jam dinding [ruang kelas] [perlengkapan lain]
                            $col_171=$fetch_prasarana[0]->col_171;
                            //Jumlah kelas yang dilengkapi soket listrik [ruang kelas] [perlengkapan lain]
                            $col_172=$fetch_prasarana[0]->col_172;
                            //Luas ruang/kelas laboratorium [ruang praktik gambar teknik] [ruangan]
                            $col_173=$fetch_prasarana[0]->col_173;
                            //Luas ruang/kelas laboratorium [ruang laboratorium IPA] [ruangan]
                            $col_174=$fetch_prasarana[0]->col_174;
                            //Luas ruang/kelas laboratorium [ruang laboratorium biologi] [ruangan]
                            $col_177=$fetch_prasarana[0]->col_177;
                            //Luas ruang/kelas laboratorium [ruang laboratorium fisika] [ruangan]
                            $col_178=$fetch_prasarana[0]->col_178;
                            //Luas ruang/kelas laboratorium [ruang laboratorium kimia] [ruangan]
                            $col_179=$fetch_prasarana[0]->col_179;
                            //Luas ruang/kelas laboratorium [ruang laboratorium komputer] [ruangan]
                            $col_180=$fetch_prasarana[0]->col_180;
                            //Luas ruang/kelas laboratorium [ruang laboratorium bahasa] [ruangan]
                            $col_181=$fetch_prasarana[0]->col_181;
                            //Luas ruang tata usaha [ruang tata usaha] [ruangan]
                            $col_182=$fetch_prasarana[0]->col_182;
                            //Kondisi ruang/kelas laboratorium [ruang laboratorium IPA] [ruangan]
                            $col_329=$fetch_prasarana[0]->col_329;
                            //Lebar ruang pimpinan [ruang pimpinan] [ruangan]
                            $col_330=$fetch_prasarana[0]->col_330;
                            //Luas UKS [UKS] [ruangan]
                            $col_367=$fetch_prasarana[0]->col_367;
                            //Luas gudang [gudang] [ruangan]
                            $col_395=$fetch_prasarana[0]->col_395;
                            //Luas ruang konseling [konseling] [ruangan]
                            $col_416=$fetch_prasarana[0]->col_416;
                            //Jumlah kelas yang dilengkapi kursi siswa [ruang kelas] [perabot]
                            $col_158=$fetch_prasarana[0]->col_158;
                        }else{
                            //Jumla0]
                            $col_160=0;
                            //Lua0]
                            $col_1097=0;
                            //Lua0]
                            $col_1099=0;
                            //Jumla0]
                            $col_1051=0;
                            //Jumla0]
                            $col_1052=0;
                            //Jumla0]
                            $col_1053=0;
                            //Jumla0]
                            $col_1054=0;
                            //Jumla0]
                            $col_1055=0;
                            //Lua0]
                            $col_1056=0;
                            //Jumla0]
                            $col_1057=0;
                            //Jumla0]
                            $col_1058=0;
                            //Jumla0]
                            $col_1059=0;
                            //Jumla0]
                            $col_1060=0;
                            //Jumla0]
                            $col_1061=0;
                            //Kondis0]
                            $col_1019=0;
                            //Jumla0]
                            $col_1028=0;
                            //Jumla0]
                            $col_1029=0;
                            //Rat0]
                            $col_1030=0;
                            //Ketersediaa0]
                            $col_780=0;
                            //Jumla0]
                            $col_362=0;
                            //Lua0]
                            $col_363=0;
                            //Lua0]
                            $col_426=0;
                            //Kondis0]
                            $col_510=0;
                            //Kondis0]
                            $col_432=0;
                            //Kondis0]
                            $col_452=0;
                            //Kondis0]
                            $col_471=0;
                            //Kondis0]
                            $col_491=0;
                            //Tota0i
                            $col_16=0;
                            //Jumla0]
                            $col_17=0;
                            //Jumla0]
                            $col_18=0;
                            //Jumla0]
                            $col_19=0;
                            //Jumla0]
                            $col_11=0;
                            //Rat0]
                            $col_12=0;
                            //Lua0]
                            $col_13=0;
                            //Kondis0]
                            $col_54=0;
                            //Jumla0]
                            $col_162=0;
                            //Jumla0]
                            $col_163=0;
                            //Jumla0]
                            $col_164=0;
                            //Jumla0]
                            $col_166=0;
                            //Jumla0]
                            $col_167=0;
                            //Jumla0]
                            $col_168=0;
                            //Jumla0]
                            $col_169=0;
                            //Jumla0]
                            $col_170=0;
                            //Jumla0]
                            $col_171=0;
                            //Jumla0]
                            $col_172=0;
                            //Lua0]
                            $col_173=0;
                            //Lua0]
                            $col_174=0;
                            //Lua0]
                            $col_177=0;
                            //Lua0]
                            $col_178=0;
                            //Lua0]
                            $col_179=0;
                            //Lua0]
                            $col_180=0;
                            //Lua0]
                            $col_181=0;
                            //Lua0]
                            $col_182=0;
                            //Kondis0]
                            $col_329=0;
                            //Leba0]
                            $col_330=0;
                            //Lua0]
                            $col_367=0;
                            //Lua0]
                            $col_395=0;
                            //Lua0]
                            $col_416=0;
                            //Jumla0]
                            $col_158=0;
                        }

                        $sql_ptk = "SELECT TOP 1 * FROM dm.ptk WHERE sekolah_id='".$record->sekolah_id."'";
                        $fetch_ptk = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_ptk));

                        // echo json_encode($fetch_ptk);die;

                        if(sizeof($fetch_ptk) > 0){
                            //Memiliki pangkat serendah-rendahnya III/c atau setara [Kepala Sekolah]
                            $col_37=$fetch_ptk[0]->col_37;
                            //Memiliki Kepala Tenaga Administrasi [Kepala Tenaga Administrasi ]
                            $col_38=$fetch_ptk[0]->col_38;
                            //Kepala Tenaga Administrasi berpendidikan minimal lulusan SMK atau yang sederajat, [Kepala Tenaga Administrasi ]
                            $col_39=$fetch_ptk[0]->col_39;
                            //Jumlah tenaga pelaksana urusan administrasi [Kepala Tenaga Administrasi ]
                            $col_40=$fetch_ptk[0]->col_40;
                            //Memiliki Kepala Tenaga Pustakawan [Kepala Tenaga Pustakawan]
                            $col_41=$fetch_ptk[0]->col_41;
                            //Jumlah guru kelas [Guru]
                            $col_42=$fetch_ptk[0]->col_42;
                            //Jumlah guru [Guru]
                            $col_43=$fetch_ptk[0]->col_43;
                            //Kepala Tenaga Pustakawan Berpendidikan minimal lulusan S1/D4 (untuk pendidik) atau D2 (untuk non pendidik) [Kepala Tenaga Pustakawan]
                            $col_44=$fetch_ptk[0]->col_44;
                            //Jumlah tenaga pustakawan yang dimiliki [Tenaga Pustakawan]
                            $col_45=$fetch_ptk[0]->col_45;
                            //Memiliki Kepala Tenaga Laboratorium [Kepala Laboratorium]
                            $col_46=$fetch_ptk[0]->col_46;
                            //Berpendidikan minimal lulusan S1/D4 (untuk pendidik) atau D3 (untuk non pendidik) [Kepala Laboratorium]
                            $col_47=$fetch_ptk[0]->col_47;
                            //Jumlah tenaga teknisi laboran yang dimiliki [Teknisi Laboran]
                            $col_48=$fetch_ptk[0]->col_48;
                            //Jumlah tenaga laboran yang dimiliki [Tenaga Laboran]
                            $col_49=$fetch_ptk[0]->col_49;
                            //Jumlah guru dengan kualifikasi pendidikan min. S1/D4 [Guru]
                            $col_2=$fetch_ptk[0]->col_2;
                            //Jumlah tenaga pelaksana urusan administrasi dengan kualifikasi min. SMA/MA/SMK/MAK [Kepala Tenaga Administrasi ]
                            $col_3=$fetch_ptk[0]->col_3;
                            //Jumlah tenaga pustakawan lulusan SMA/MA/SMK/MAK [Tenaga Pustakawan]
                            $col_4=$fetch_ptk[0]->col_4;
                            //Jumlah tenaga teknisi laboran dengan min. lulusan D2 terkait laboratorium [Teknisi Laboran]
                            $col_5=$fetch_ptk[0]->col_5;
                            //Jumlah tenaga laboran dengan min. lulusan D1 terkait laboratorium [Tenaga Laboran]
                            $col_6=$fetch_ptk[0]->col_6;
                            //Ketersediaan guru penjaskes untuk SD [Guru]
                            $col_525=$fetch_ptk[0]->col_525;
                            //Lama pengalaman mengajar kepala sekolah [Kepala Sekolah]
                            $col_526=$fetch_ptk[0]->col_526;
                            //Masa Kerja sebagai pendidik atau tenaga kependidikan [Kepala Tenaga Pustakawan]
                            $col_529=$fetch_ptk[0]->col_529;
                            //Masa Kerja sebagai pendidik atau tenaga kependidikan [Kepala Laboratorium]
                            $col_531=$fetch_ptk[0]->col_531;
                            //Ketersediaan Kepsek dengan kualifikasi minimal S1/D4 [Kepala Sekolah]
                            $col_659=$fetch_ptk[0]->col_659;
                            //Usia Kepsek waktu diangkat [Kepala Sekolah]
                            $col_660=$fetch_ptk[0]->col_660;
                            //Rata-rata skor kompetensi pedagogik [Guru]
                            $col_55=$fetch_ptk[0]->col_55;
                            //Rata-rata skor kompetensi Profesional [Guru]
                            $col_56=$fetch_ptk[0]->col_56;
                            //Rata-rata skor Kompetensi Manajerial [Kepala Sekolah]
                            $col_58=$fetch_ptk[0]->col_58;
                            //Rata-rata skor Kompetensi Kewirausahaan [Kepala Sekolah]
                            $col_59=$fetch_ptk[0]->col_59;
                            //Rata-rata skor kompetensi Supervisi [Kepala Sekolah]
                            $col_60=$fetch_ptk[0]->col_60;
                            //Jumlah guru yang Memiliki Sertifikat pendidik
                            $col_157=$fetch_ptk[0]->col_157;
                            //Ketersediaan guru agama untuk SD [Guru]
                            $col_783=$fetch_ptk[0]->col_783;
                            //Jumlah mapel yang memiliki minimal satu guru yang sesuai dengan bidang [Guru]
                            $col_1100=$fetch_ptk[0]->col_1100;
                            //Rata-rata skor kompetensi Sosial [Kepala Laboratorium]
                            $col_1101=$fetch_ptk[0]->col_1101;
                            //Rata-rata skor kompetensi Sosial [Teknisi Laboran]
                            $col_1102=$fetch_ptk[0]->col_1102;
                            //Rata-rata skor kompetensi Sosial [Laboran]
                            $col_1103=$fetch_ptk[0]->col_1103;
                            //Rasio guru terhadap kelas [Guru]
                            $col_883=$fetch_ptk[0]->col_883;
                            //Rasio guru terhadap rombel [Guru]
                            $col_884=$fetch_ptk[0]->col_884;
                            //Kepsek Memiliki Sertifikat pendidik [Kepala Sekolah]
                            $col_534=$fetch_ptk[0]->col_534;
                            //Kepsek Memiliki Sertifikat kepala sekolah [Kepala Sekolah]
                            $col_535=$fetch_ptk[0]->col_535;
                        }else{
                            //Memilik0]
                            $col_37=0;
                            //Memilik0]
                            $col_38=0;
                            //Kepal0]
                            $col_39=0;
                            //Jumla0]
                            $col_40=0;
                            //Memilik0]
                            $col_41=0;
                            //Jumla0]
                            $col_42=0;
                            //Jumla0]
                            $col_43=0;
                            //Kepal0]
                            $col_44=0;
                            //Jumla0]
                            $col_45=0;
                            //Memilik0]
                            $col_46=0;
                            //Berpendidika0]
                            $col_47=0;
                            //Jumla0]
                            $col_48=0;
                            //Jumla0]
                            $col_49=0;
                            //Jumla0]
                            $col_2=0;
                            //Jumla0]
                            $col_3=0;
                            //Jumla0]
                            $col_4=0;
                            //Jumla0]
                            $col_5=0;
                            //Jumla0]
                            $col_6=0;
                            //Ketersediaa0]
                            $col_525=0;
                            //Lam0]
                            $col_526=0;
                            //Mas0]
                            $col_529=0;
                            //Mas0]
                            $col_531=0;
                            //Ketersediaa0]
                            $col_659=0;
                            //Usi0]
                            $col_660=0;
                            //Rat0]
                            $col_55=0;
                            //Rat0]
                            $col_56=0;
                            //Rat0]
                            $col_58=0;
                            //Rat0]
                            $col_59=0;
                            //Rat0]
                            $col_60=0;
                            //Jumla0k
                            $col_157=0;
                            //Ketersediaa0]
                            $col_783=0;
                            //Jumla0]
                            $col_1100=0;
                            //Rat0]
                            $col_1101=0;
                            //Rat0]
                            $col_1102=0;
                            //Rat0]
                            $col_1103=0;
                            //Rasi0]
                            $col_883=0;
                            //Rasi0]
                            $col_884=0;
                            //Kepse0]
                            $col_534=0;
                            //Kepse0]
                            $col_535=0;
                        }

                        $sql_rombel = "SELECT TOP 1 * FROM dm.rombel WHERE sekolah_id='".$record->sekolah_id."'";
                        $fetch_rombel = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_rombel));

                        // echo json_encode($fetch_rombel);die;

                        if(sizeof($fetch_rombel) > 0){
                            //Jumlah rombongan belajar kelas 10
                            $col_1038=$fetch_rombel[0]->col_1038;
                            //Jumlah rombongan belajar kelas 11
                            $col_1039=$fetch_rombel[0]->col_1039;
                            //Jumlah rombongan belajar kelas 12
                            $col_1040=$fetch_rombel[0]->col_1040;
                            //Jumlah rombongan belajar kelas 13
                            $col_1041=$fetch_rombel[0]->col_1041;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya Kelas 10
                            $col_1067=$fetch_rombel[0]->col_1067;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya Kelas 11
                            $col_1068=$fetch_rombel[0]->col_1068;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya Kelas 12
                            $col_1069=$fetch_rombel[0]->col_1069;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya Kelas 13
                            $col_1070=$fetch_rombel[0]->col_1070;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 13
                            $col_1071=$fetch_rombel[0]->col_1071;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 13
                            $col_1072=$fetch_rombel[0]->col_1072;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 13
                            $col_1073=$fetch_rombel[0]->col_1073;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 13
                            $col_1074=$fetch_rombel[0]->col_1074;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 13
                            $col_1075=$fetch_rombel[0]->col_1075;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Inggris Kelas 13
                            $col_1076=$fetch_rombel[0]->col_1076;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya Kelas 13
                            $col_1077=$fetch_rombel[0]->col_1077;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya Kelas 13
                            $col_1078=$fetch_rombel[0]->col_1078;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sejarah Indonesia Kelas 13
                            $col_1079=$fetch_rombel[0]->col_1079;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya dan Kewirausahaan Kelas 13
                            $col_1080=$fetch_rombel[0]->col_1080;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika (Peminatan) Kelas 13
                            $col_1081=$fetch_rombel[0]->col_1081;
                            //Kriteria ketuntasan minimal Mata Pelajaran Biologi Kelas 13
                            $col_1082=$fetch_rombel[0]->col_1082;
                            //Kriteria ketuntasan minimal Mata Pelajaran Fisika Kelas 13
                            $col_1083=$fetch_rombel[0]->col_1083;
                            //Kriteria ketuntasan minimal Mata Pelajaran Kimia Kelas 13
                            $col_1084=$fetch_rombel[0]->col_1084;
                            //Kriteria ketuntasan minimal Mata Pelajaran Geografi Kelas 13
                            $col_1085=$fetch_rombel[0]->col_1085;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sejarah Kelas 13
                            $col_1086=$fetch_rombel[0]->col_1086;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sosiologi dan Antropologi Kelas 13
                            $col_1087=$fetch_rombel[0]->col_1087;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ekonomi Kelas 13
                            $col_1088=$fetch_rombel[0]->col_1088;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 13
                            $col_1089=$fetch_rombel[0]->col_1089;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Inggris Kelas 13
                            $col_1090=$fetch_rombel[0]->col_1090;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 13
                            $col_1091=$fetch_rombel[0]->col_1091;
                            //Kriteria ketuntasan minimal Mata Pelajaran Antropologi Kelas 13
                            $col_1092=$fetch_rombel[0]->col_1092;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendalaman Kelas 13
                            $col_1093=$fetch_rombel[0]->col_1093;
                            //Jumlah rombongan belajar kelas 1
                            $col_753=$fetch_rombel[0]->col_753;
                            //Jumlah rombongan belajar kelas 2
                            $col_754=$fetch_rombel[0]->col_754;
                            //Jumlah rombongan belajar kelas 3
                            $col_755=$fetch_rombel[0]->col_755;
                            //Jumlah rombongan belajar kelas 4
                            $col_756=$fetch_rombel[0]->col_756;
                            //Jumlah rombongan belajar kelas 5
                            $col_757=$fetch_rombel[0]->col_757;
                            //Jumlah rombongan belajar kelas 6
                            $col_758=$fetch_rombel[0]->col_758;
                            //Jumlah rombongan belajar kelas 7
                            $col_759=$fetch_rombel[0]->col_759;
                            //Jumlah rombongan belajar kelas 8
                            $col_760=$fetch_rombel[0]->col_760;
                            //Jumlah rombongan belajar kelas 9
                            $col_761=$fetch_rombel[0]->col_761;
                            //Alokasi waktu per minggu Mata Pelajaran Dasar Bidang Keahlian Kelas 10
                            $col_995=$fetch_rombel[0]->col_995;
                            //Alokasi waktu per minggu Mata Pelajaran Dasar Program Keahlian Kelas 10
                            $col_996=$fetch_rombel[0]->col_996;
                            //Alokasi waktu per minggu Mata Pelajaran Paket Keahlian Kelas 10
                            $col_997=$fetch_rombel[0]->col_997;
                            //Alokasi waktu per minggu Mata Pelajaran Dasar Bidang Keahlian Kelas 11
                            $col_998=$fetch_rombel[0]->col_998;
                            //Alokasi waktu per minggu Mata Pelajaran Dasar Program Keahlian Kelas 11
                            $col_999=$fetch_rombel[0]->col_999;
                            //Alokasi waktu per minggu Mata Pelajaran Paket Keahlian Kelas 11
                            $col_1000=$fetch_rombel[0]->col_1000;
                            //Alokasi waktu per minggu Mata Pelajaran Dasar Bidang Keahlian Kelas 12
                            $col_1001=$fetch_rombel[0]->col_1001;
                            //Alokasi waktu per minggu Mata Pelajaran Dasar Program Keahlian Kelas 12
                            $col_1002=$fetch_rombel[0]->col_1002;
                            //Alokasi waktu per minggu Mata Pelajaran Paket Keahlian Kelas 12
                            $col_1003=$fetch_rombel[0]->col_1003;
                            //Alokasi waktu per minggu Mata Pelajaran Dasar Bidang Keahlian Kelas 13
                            $col_1004=$fetch_rombel[0]->col_1004;
                            //Alokasi waktu per minggu Mata Pelajaran Dasar Program Keahlian Kelas 13
                            $col_1005=$fetch_rombel[0]->col_1005;
                            //Alokasi waktu per minggu Mata Pelajaran Paket Keahlian Kelas 13
                            $col_1006=$fetch_rombel[0]->col_1006;
                            //Kriteria ketuntasan minimal Mata Pelajaran Dasar Bidang Keahlian Kelas 10
                            $col_1007=$fetch_rombel[0]->col_1007;
                            //Kriteria ketuntasan minimal Mata Pelajaran Dasar Program Keahlian Kelas 10
                            $col_1008=$fetch_rombel[0]->col_1008;
                            //Kriteria ketuntasan minimal Mata Pelajaran Paket Keahlian Kelas 10
                            $col_1009=$fetch_rombel[0]->col_1009;
                            //Kriteria ketuntasan minimal Mata Pelajaran Dasar Bidang Keahlian Kelas 11
                            $col_1010=$fetch_rombel[0]->col_1010;
                            //Kriteria ketuntasan minimal Mata Pelajaran Dasar Program Keahlian Kelas 11
                            $col_1011=$fetch_rombel[0]->col_1011;
                            //Kriteria ketuntasan minimal Mata Pelajaran Paket Keahlian Kelas 11
                            $col_1012=$fetch_rombel[0]->col_1012;
                            //Kriteria ketuntasan minimal Mata Pelajaran Dasar Bidang Keahlian Kelas 12
                            $col_1013=$fetch_rombel[0]->col_1013;
                            //Kriteria ketuntasan minimal Mata Pelajaran Dasar Program Keahlian Kelas 12
                            $col_1014=$fetch_rombel[0]->col_1014;
                            //Kriteria ketuntasan minimal Mata Pelajaran Paket Keahlian Kelas 12
                            $col_1015=$fetch_rombel[0]->col_1015;
                            //Kriteria ketuntasan minimal Mata Pelajaran Dasar Bidang Keahlian Kelas 13
                            $col_1016=$fetch_rombel[0]->col_1016;
                            //Kriteria ketuntasan minimal Mata Pelajaran Dasar Program Keahlian Kelas 13
                            $col_1017=$fetch_rombel[0]->col_1017;
                            //Kriteria ketuntasan minimal Mata Pelajaran Paket Keahlian Kelas 13
                            $col_1018=$fetch_rombel[0]->col_1018;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 1
                            $col_796=$fetch_rombel[0]->col_796;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 2
                            $col_797=$fetch_rombel[0]->col_797;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 3
                            $col_798=$fetch_rombel[0]->col_798;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 4
                            $col_799=$fetch_rombel[0]->col_799;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 5
                            $col_800=$fetch_rombel[0]->col_800;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 6
                            $col_801=$fetch_rombel[0]->col_801;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 7
                            $col_802=$fetch_rombel[0]->col_802;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 8
                            $col_803=$fetch_rombel[0]->col_803;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 9
                            $col_804=$fetch_rombel[0]->col_804;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Inggris Kelas 7
                            $col_805=$fetch_rombel[0]->col_805;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Inggris Kelas 8
                            $col_806=$fetch_rombel[0]->col_806;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Inggris Kelas 9
                            $col_807=$fetch_rombel[0]->col_807;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 1
                            $col_808=$fetch_rombel[0]->col_808;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 2
                            $col_809=$fetch_rombel[0]->col_809;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 3
                            $col_810=$fetch_rombel[0]->col_810;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 4
                            $col_811=$fetch_rombel[0]->col_811;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 5
                            $col_812=$fetch_rombel[0]->col_812;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 6
                            $col_813=$fetch_rombel[0]->col_813;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 7
                            $col_814=$fetch_rombel[0]->col_814;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 8
                            $col_815=$fetch_rombel[0]->col_815;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Alam Kelas 9
                            $col_816=$fetch_rombel[0]->col_816;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 1
                            $col_817=$fetch_rombel[0]->col_817;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 2
                            $col_818=$fetch_rombel[0]->col_818;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 3
                            $col_819=$fetch_rombel[0]->col_819;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 4
                            $col_820=$fetch_rombel[0]->col_820;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 5
                            $col_821=$fetch_rombel[0]->col_821;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 6
                            $col_822=$fetch_rombel[0]->col_822;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 7
                            $col_823=$fetch_rombel[0]->col_823;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 8
                            $col_824=$fetch_rombel[0]->col_824;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 9
                            $col_825=$fetch_rombel[0]->col_825;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 1
                            $col_826=$fetch_rombel[0]->col_826;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 2
                            $col_827=$fetch_rombel[0]->col_827;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 3
                            $col_828=$fetch_rombel[0]->col_828;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 4
                            $col_829=$fetch_rombel[0]->col_829;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 5
                            $col_830=$fetch_rombel[0]->col_830;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 6
                            $col_831=$fetch_rombel[0]->col_831;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 7
                            $col_832=$fetch_rombel[0]->col_832;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 8
                            $col_833=$fetch_rombel[0]->col_833;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 9
                            $col_834=$fetch_rombel[0]->col_834;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 1
                            $col_835=$fetch_rombel[0]->col_835;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 2
                            $col_836=$fetch_rombel[0]->col_836;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 3
                            $col_837=$fetch_rombel[0]->col_837;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 4
                            $col_838=$fetch_rombel[0]->col_838;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 5
                            $col_839=$fetch_rombel[0]->col_839;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 6
                            $col_840=$fetch_rombel[0]->col_840;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 7
                            $col_841=$fetch_rombel[0]->col_841;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 8
                            $col_842=$fetch_rombel[0]->col_842;
                            //Kriteria ketuntasan minimal Mata Pelajaran Muatan Lokal Kelas 9
                            $col_843=$fetch_rombel[0]->col_843;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 1
                            $col_844=$fetch_rombel[0]->col_844;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 2
                            $col_845=$fetch_rombel[0]->col_845;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 3
                            $col_846=$fetch_rombel[0]->col_846;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 4
                            $col_847=$fetch_rombel[0]->col_847;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 5
                            $col_848=$fetch_rombel[0]->col_848;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 6
                            $col_849=$fetch_rombel[0]->col_849;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 7
                            $col_850=$fetch_rombel[0]->col_850;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 8
                            $col_851=$fetch_rombel[0]->col_851;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 9
                            $col_852=$fetch_rombel[0]->col_852;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 1
                            $col_853=$fetch_rombel[0]->col_853;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 2
                            $col_854=$fetch_rombel[0]->col_854;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 3
                            $col_855=$fetch_rombel[0]->col_855;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 4
                            $col_856=$fetch_rombel[0]->col_856;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 5
                            $col_857=$fetch_rombel[0]->col_857;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 6
                            $col_858=$fetch_rombel[0]->col_858;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 7
                            $col_859=$fetch_rombel[0]->col_859;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 8
                            $col_860=$fetch_rombel[0]->col_860;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 9
                            $col_861=$fetch_rombel[0]->col_861;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 1
                            $col_862=$fetch_rombel[0]->col_862;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 2
                            $col_863=$fetch_rombel[0]->col_863;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 3
                            $col_864=$fetch_rombel[0]->col_864;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 4
                            $col_865=$fetch_rombel[0]->col_865;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 5
                            $col_866=$fetch_rombel[0]->col_866;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 6
                            $col_867=$fetch_rombel[0]->col_867;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 7
                            $col_868=$fetch_rombel[0]->col_868;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 8
                            $col_869=$fetch_rombel[0]->col_869;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 9
                            $col_870=$fetch_rombel[0]->col_870;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya Kelas 7
                            $col_871=$fetch_rombel[0]->col_871;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya Kelas 8
                            $col_872=$fetch_rombel[0]->col_872;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya Kelas 9
                            $col_873=$fetch_rombel[0]->col_873;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya dan Prakarya Kelas 1
                            $col_874=$fetch_rombel[0]->col_874;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya dan Prakarya Kelas 2
                            $col_875=$fetch_rombel[0]->col_875;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya dan Prakarya Kelas 3
                            $col_876=$fetch_rombel[0]->col_876;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya dan Prakarya Kelas 4
                            $col_877=$fetch_rombel[0]->col_877;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya dan Prakarya Kelas 5
                            $col_878=$fetch_rombel[0]->col_878;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya dan Prakarya Kelas 6
                            $col_879=$fetch_rombel[0]->col_879;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya Kelas 7
                            $col_880=$fetch_rombel[0]->col_880;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya Kelas 8
                            $col_881=$fetch_rombel[0]->col_881;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya Kelas 9
                            $col_882=$fetch_rombel[0]->col_882;
                            //Total waktu belajar Kelas 1
                            $col_641=$fetch_rombel[0]->col_641;
                            //Total waktu belajar Kelas 2
                            $col_642=$fetch_rombel[0]->col_642;
                            //Total waktu belajar Kelas 3
                            $col_643=$fetch_rombel[0]->col_643;
                            //Total waktu belajar Kelas 4
                            $col_644=$fetch_rombel[0]->col_644;
                            //Total waktu belajar Kelas 5
                            $col_645=$fetch_rombel[0]->col_645;
                            //Total waktu belajar Kelas 6
                            $col_646=$fetch_rombel[0]->col_646;
                            //Total waktu belajar Kelas 7
                            $col_647=$fetch_rombel[0]->col_647;
                            //Total waktu belajar Kelas 8
                            $col_648=$fetch_rombel[0]->col_648;
                            //Total waktu belajar Kelas 9
                            $col_649=$fetch_rombel[0]->col_649;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 1
                            $col_661=$fetch_rombel[0]->col_661;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 2
                            $col_662=$fetch_rombel[0]->col_662;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 3
                            $col_663=$fetch_rombel[0]->col_663;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 4
                            $col_664=$fetch_rombel[0]->col_664;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 5
                            $col_665=$fetch_rombel[0]->col_665;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 6
                            $col_666=$fetch_rombel[0]->col_666;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 7
                            $col_667=$fetch_rombel[0]->col_667;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 8
                            $col_668=$fetch_rombel[0]->col_668;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 9
                            $col_669=$fetch_rombel[0]->col_669;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Inggris Kelas 7
                            $col_670=$fetch_rombel[0]->col_670;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Inggris Kelas 8
                            $col_671=$fetch_rombel[0]->col_671;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Inggris Kelas 9
                            $col_672=$fetch_rombel[0]->col_672;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 1
                            $col_673=$fetch_rombel[0]->col_673;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 2
                            $col_674=$fetch_rombel[0]->col_674;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 3
                            $col_675=$fetch_rombel[0]->col_675;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 4
                            $col_676=$fetch_rombel[0]->col_676;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 5
                            $col_677=$fetch_rombel[0]->col_677;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 6
                            $col_678=$fetch_rombel[0]->col_678;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 7
                            $col_679=$fetch_rombel[0]->col_679;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 8
                            $col_680=$fetch_rombel[0]->col_680;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Alam Kelas 9
                            $col_681=$fetch_rombel[0]->col_681;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 1
                            $col_682=$fetch_rombel[0]->col_682;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 2
                            $col_683=$fetch_rombel[0]->col_683;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 3
                            $col_684=$fetch_rombel[0]->col_684;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 4
                            $col_685=$fetch_rombel[0]->col_685;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 5
                            $col_686=$fetch_rombel[0]->col_686;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 6
                            $col_687=$fetch_rombel[0]->col_687;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 7
                            $col_688=$fetch_rombel[0]->col_688;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 8
                            $col_689=$fetch_rombel[0]->col_689;
                            //Alokasi waktu per minggu Mata Pelajaran Ilmu Pengetahuan Sosial Kelas 9
                            $col_690=$fetch_rombel[0]->col_690;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 1
                            $col_691=$fetch_rombel[0]->col_691;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 2
                            $col_692=$fetch_rombel[0]->col_692;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 3
                            $col_693=$fetch_rombel[0]->col_693;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 4
                            $col_694=$fetch_rombel[0]->col_694;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 5
                            $col_695=$fetch_rombel[0]->col_695;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 6
                            $col_696=$fetch_rombel[0]->col_696;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 7
                            $col_697=$fetch_rombel[0]->col_697;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 8
                            $col_698=$fetch_rombel[0]->col_698;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 9
                            $col_699=$fetch_rombel[0]->col_699;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 1
                            $col_700=$fetch_rombel[0]->col_700;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 2
                            $col_701=$fetch_rombel[0]->col_701;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 3
                            $col_702=$fetch_rombel[0]->col_702;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 4
                            $col_703=$fetch_rombel[0]->col_703;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 5
                            $col_704=$fetch_rombel[0]->col_704;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 6
                            $col_705=$fetch_rombel[0]->col_705;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 7
                            $col_706=$fetch_rombel[0]->col_706;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 8
                            $col_707=$fetch_rombel[0]->col_707;
                            //Alokasi waktu per minggu Mata Pelajaran Muatan Lokal Kelas 9
                            $col_708=$fetch_rombel[0]->col_708;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 1
                            $col_709=$fetch_rombel[0]->col_709;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 2
                            $col_710=$fetch_rombel[0]->col_710;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 3
                            $col_711=$fetch_rombel[0]->col_711;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 4
                            $col_712=$fetch_rombel[0]->col_712;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 5
                            $col_713=$fetch_rombel[0]->col_713;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 6
                            $col_714=$fetch_rombel[0]->col_714;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 7
                            $col_715=$fetch_rombel[0]->col_715;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 8
                            $col_716=$fetch_rombel[0]->col_716;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 9
                            $col_717=$fetch_rombel[0]->col_717;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 1
                            $col_718=$fetch_rombel[0]->col_718;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 2
                            $col_719=$fetch_rombel[0]->col_719;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 3
                            $col_720=$fetch_rombel[0]->col_720;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 4
                            $col_721=$fetch_rombel[0]->col_721;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 5
                            $col_722=$fetch_rombel[0]->col_722;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 6
                            $col_723=$fetch_rombel[0]->col_723;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 7
                            $col_724=$fetch_rombel[0]->col_724;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 8
                            $col_725=$fetch_rombel[0]->col_725;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 9
                            $col_726=$fetch_rombel[0]->col_726;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 1
                            $col_727=$fetch_rombel[0]->col_727;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 2
                            $col_728=$fetch_rombel[0]->col_728;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 3
                            $col_729=$fetch_rombel[0]->col_729;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 4
                            $col_730=$fetch_rombel[0]->col_730;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 5
                            $col_731=$fetch_rombel[0]->col_731;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 6
                            $col_732=$fetch_rombel[0]->col_732;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 7
                            $col_733=$fetch_rombel[0]->col_733;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 8
                            $col_734=$fetch_rombel[0]->col_734;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 9
                            $col_735=$fetch_rombel[0]->col_735;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya Kelas 7
                            $col_736=$fetch_rombel[0]->col_736;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya Kelas 8
                            $col_737=$fetch_rombel[0]->col_737;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya Kelas 9
                            $col_738=$fetch_rombel[0]->col_738;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya dan Prakarya Kelas 1
                            $col_739=$fetch_rombel[0]->col_739;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya dan Prakarya Kelas 2
                            $col_740=$fetch_rombel[0]->col_740;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya dan Prakarya Kelas 3
                            $col_741=$fetch_rombel[0]->col_741;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya dan Prakarya Kelas 4
                            $col_742=$fetch_rombel[0]->col_742;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya dan Prakarya Kelas 5
                            $col_743=$fetch_rombel[0]->col_743;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya dan Prakarya Kelas 6
                            $col_744=$fetch_rombel[0]->col_744;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya Kelas 7
                            $col_745=$fetch_rombel[0]->col_745;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya Kelas 8
                            $col_746=$fetch_rombel[0]->col_746;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya Kelas 9
                            $col_747=$fetch_rombel[0]->col_747;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 10
                            $col_567=$fetch_rombel[0]->col_567;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 10
                            $col_568=$fetch_rombel[0]->col_568;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 10
                            $col_569=$fetch_rombel[0]->col_569;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 10
                            $col_570=$fetch_rombel[0]->col_570;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 10
                            $col_571=$fetch_rombel[0]->col_571;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Inggris Kelas 10
                            $col_572=$fetch_rombel[0]->col_572;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya Kelas 10
                            $col_573=$fetch_rombel[0]->col_573;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya Kelas 10
                            $col_574=$fetch_rombel[0]->col_574;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sejarah Indonesia Kelas 10
                            $col_575=$fetch_rombel[0]->col_575;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya dan Kewirausahaan Kelas 10
                            $col_576=$fetch_rombel[0]->col_576;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika (Peminatan) Kelas 10
                            $col_577=$fetch_rombel[0]->col_577;
                            //Kriteria ketuntasan minimal Mata Pelajaran Biologi Kelas 10
                            $col_578=$fetch_rombel[0]->col_578;
                            //Kriteria ketuntasan minimal Mata Pelajaran Fisika Kelas 10
                            $col_579=$fetch_rombel[0]->col_579;
                            //Kriteria ketuntasan minimal Mata Pelajaran Kimia Kelas 10
                            $col_580=$fetch_rombel[0]->col_580;
                            //Kriteria ketuntasan minimal Mata Pelajaran Geografi Kelas 10
                            $col_581=$fetch_rombel[0]->col_581;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sejarah Kelas 10
                            $col_582=$fetch_rombel[0]->col_582;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sosiologi dan Antropologi Kelas 10
                            $col_583=$fetch_rombel[0]->col_583;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ekonomi Kelas 10
                            $col_584=$fetch_rombel[0]->col_584;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 10
                            $col_585=$fetch_rombel[0]->col_585;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Inggris Kelas 10
                            $col_586=$fetch_rombel[0]->col_586;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 10
                            $col_587=$fetch_rombel[0]->col_587;
                            //Kriteria ketuntasan minimal Mata Pelajaran Antropologi Kelas 10
                            $col_588=$fetch_rombel[0]->col_588;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendalaman Kelas 10
                            $col_589=$fetch_rombel[0]->col_589;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 11
                            $col_590=$fetch_rombel[0]->col_590;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 11
                            $col_591=$fetch_rombel[0]->col_591;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 11
                            $col_592=$fetch_rombel[0]->col_592;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 11
                            $col_593=$fetch_rombel[0]->col_593;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 11
                            $col_594=$fetch_rombel[0]->col_594;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Inggris Kelas 11
                            $col_595=$fetch_rombel[0]->col_595;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya Kelas 11
                            $col_596=$fetch_rombel[0]->col_596;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya Kelas 11
                            $col_597=$fetch_rombel[0]->col_597;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sejarah Indonesia Kelas 11
                            $col_598=$fetch_rombel[0]->col_598;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya dan Kewirausahaan Kelas 11
                            $col_599=$fetch_rombel[0]->col_599;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika (Peminatan) Kelas 11
                            $col_600=$fetch_rombel[0]->col_600;
                            //Kriteria ketuntasan minimal Mata Pelajaran Biologi Kelas 11
                            $col_601=$fetch_rombel[0]->col_601;
                            //Kriteria ketuntasan minimal Mata Pelajaran Fisika Kelas 11
                            $col_602=$fetch_rombel[0]->col_602;
                            //Kriteria ketuntasan minimal Mata Pelajaran Kimia Kelas 11
                            $col_603=$fetch_rombel[0]->col_603;
                            //Kriteria ketuntasan minimal Mata Pelajaran Geografi Kelas 11
                            $col_604=$fetch_rombel[0]->col_604;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sejarah Kelas 11
                            $col_605=$fetch_rombel[0]->col_605;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sosiologi dan Antropologi Kelas 11
                            $col_606=$fetch_rombel[0]->col_606;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ekonomi Kelas 11
                            $col_607=$fetch_rombel[0]->col_607;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 11
                            $col_608=$fetch_rombel[0]->col_608;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Inggris Kelas 11
                            $col_609=$fetch_rombel[0]->col_609;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 11
                            $col_610=$fetch_rombel[0]->col_610;
                            //Kriteria ketuntasan minimal Mata Pelajaran Antropologi Kelas 11
                            $col_611=$fetch_rombel[0]->col_611;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendalaman Kelas 11
                            $col_612=$fetch_rombel[0]->col_612;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 12
                            $col_613=$fetch_rombel[0]->col_613;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 12
                            $col_614=$fetch_rombel[0]->col_614;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Indonesia Kelas 12
                            $col_615=$fetch_rombel[0]->col_615;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika Kelas 12
                            $col_616=$fetch_rombel[0]->col_616;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 12
                            $col_617=$fetch_rombel[0]->col_617;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa Inggris Kelas 12
                            $col_618=$fetch_rombel[0]->col_618;
                            //Kriteria ketuntasan minimal Mata Pelajaran Seni Budaya Kelas 12
                            $col_619=$fetch_rombel[0]->col_619;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya Kelas 12
                            $col_620=$fetch_rombel[0]->col_620;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sejarah Indonesia Kelas 12
                            $col_621=$fetch_rombel[0]->col_621;
                            //Kriteria ketuntasan minimal Mata Pelajaran Prakarya dan Kewirausahaan Kelas 12
                            $col_622=$fetch_rombel[0]->col_622;
                            //Kriteria ketuntasan minimal Mata Pelajaran Matematika (Peminatan) Kelas 12
                            $col_623=$fetch_rombel[0]->col_623;
                            //Kriteria ketuntasan minimal Mata Pelajaran Biologi Kelas 12
                            $col_624=$fetch_rombel[0]->col_624;
                            //Kriteria ketuntasan minimal Mata Pelajaran Fisika Kelas 12
                            $col_625=$fetch_rombel[0]->col_625;
                            //Kriteria ketuntasan minimal Mata Pelajaran Kimia Kelas 12
                            $col_626=$fetch_rombel[0]->col_626;
                            //Kriteria ketuntasan minimal Mata Pelajaran Geografi Kelas 12
                            $col_627=$fetch_rombel[0]->col_627;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sejarah Kelas 12
                            $col_628=$fetch_rombel[0]->col_628;
                            //Kriteria ketuntasan minimal Mata Pelajaran Sosiologi dan Antropologi Kelas 12
                            $col_629=$fetch_rombel[0]->col_629;
                            //Kriteria ketuntasan minimal Mata Pelajaran Ekonomi Kelas 12
                            $col_630=$fetch_rombel[0]->col_630;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 12
                            $col_631=$fetch_rombel[0]->col_631;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Inggris Kelas 12
                            $col_632=$fetch_rombel[0]->col_632;
                            //Kriteria ketuntasan minimal Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 12
                            $col_633=$fetch_rombel[0]->col_633;
                            //Kriteria ketuntasan minimal Mata Pelajaran Antropologi Kelas 12
                            $col_634=$fetch_rombel[0]->col_634;
                            //Kriteria ketuntasan minimal Mata Pelajaran Pendalaman Kelas 12
                            $col_635=$fetch_rombel[0]->col_635;
                            //Total waktu belajar Kelas 10
                            $col_33=$fetch_rombel[0]->col_33;
                            //Total waktu belajar Kelas 11
                            $col_34=$fetch_rombel[0]->col_34;
                            //Total waktu belajar Kelas 12
                            $col_35=$fetch_rombel[0]->col_35;
                            //Total waktu belajar Kelas 13
                            $col_36=$fetch_rombel[0]->col_36;
                            //Jumlah rombongan belajar
                            $col_50=$fetch_rombel[0]->col_50;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 10
                            $col_187=$fetch_rombel[0]->col_187;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 10
                            $col_188=$fetch_rombel[0]->col_188;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 10
                            $col_189=$fetch_rombel[0]->col_189;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 10
                            $col_190=$fetch_rombel[0]->col_190;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 10
                            $col_191=$fetch_rombel[0]->col_191;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Inggris Kelas 10
                            $col_192=$fetch_rombel[0]->col_192;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya Kelas 10
                            $col_193=$fetch_rombel[0]->col_193;
                            //Alokasi waktu per minggu Mata Pelajaran Sejarah Indonesia Kelas 10
                            $col_194=$fetch_rombel[0]->col_194;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya dan Kewirausahaan Kelas 10
                            $col_195=$fetch_rombel[0]->col_195;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika (Peminatan) Kelas 10
                            $col_196=$fetch_rombel[0]->col_196;
                            //Alokasi waktu per minggu Mata Pelajaran Biologi Kelas 10
                            $col_197=$fetch_rombel[0]->col_197;
                            //Alokasi waktu per minggu Mata Pelajaran Fisika Kelas 10
                            $col_198=$fetch_rombel[0]->col_198;
                            //Alokasi waktu per minggu Mata Pelajaran Kimia Kelas 10
                            $col_199=$fetch_rombel[0]->col_199;
                            //Alokasi waktu per minggu Mata Pelajaran Geografi Kelas 10
                            $col_200=$fetch_rombel[0]->col_200;
                            //Alokasi waktu per minggu Mata Pelajaran Sejarah Kelas 10
                            $col_201=$fetch_rombel[0]->col_201;
                            //Alokasi waktu per minggu Mata Pelajaran Sosiologi dan Antropologi Kelas 10
                            $col_202=$fetch_rombel[0]->col_202;
                            //Alokasi waktu per minggu Mata Pelajaran Ekonomi Kelas 10
                            $col_203=$fetch_rombel[0]->col_203;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 10
                            $col_204=$fetch_rombel[0]->col_204;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Inggris Kelas 10
                            $col_205=$fetch_rombel[0]->col_205;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 10
                            $col_206=$fetch_rombel[0]->col_206;
                            //Alokasi waktu per minggu Mata Pelajaran Antropologi Kelas 10
                            $col_207=$fetch_rombel[0]->col_207;
                            //Alokasi waktu per minggu Mata Pelajaran Pendalaman Kelas 10
                            $col_208=$fetch_rombel[0]->col_208;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 11
                            $col_209=$fetch_rombel[0]->col_209;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 11
                            $col_210=$fetch_rombel[0]->col_210;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 11
                            $col_211=$fetch_rombel[0]->col_211;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 11
                            $col_212=$fetch_rombel[0]->col_212;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 11
                            $col_213=$fetch_rombel[0]->col_213;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Inggris Kelas 11
                            $col_214=$fetch_rombel[0]->col_214;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya Kelas 11
                            $col_215=$fetch_rombel[0]->col_215;
                            //Alokasi waktu per minggu Mata Pelajaran Sejarah Indonesia Kelas 11
                            $col_216=$fetch_rombel[0]->col_216;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya dan Kewirausahaan Kelas 11
                            $col_217=$fetch_rombel[0]->col_217;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika (Peminatan) Kelas 11
                            $col_218=$fetch_rombel[0]->col_218;
                            //Alokasi waktu per minggu Mata Pelajaran Biologi Kelas 11
                            $col_219=$fetch_rombel[0]->col_219;
                            //Alokasi waktu per minggu Mata Pelajaran Fisika Kelas 11
                            $col_220=$fetch_rombel[0]->col_220;
                            //Alokasi waktu per minggu Mata Pelajaran Kimia Kelas 11
                            $col_221=$fetch_rombel[0]->col_221;
                            //Alokasi waktu per minggu Mata Pelajaran Geografi Kelas 11
                            $col_222=$fetch_rombel[0]->col_222;
                            //Alokasi waktu per minggu Mata Pelajaran Sejarah Kelas 11
                            $col_223=$fetch_rombel[0]->col_223;
                            //Alokasi waktu per minggu Mata Pelajaran Sosiologi dan Antropologi Kelas 11
                            $col_224=$fetch_rombel[0]->col_224;
                            //Alokasi waktu per minggu Mata Pelajaran Ekonomi Kelas 11
                            $col_225=$fetch_rombel[0]->col_225;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 11
                            $col_226=$fetch_rombel[0]->col_226;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Inggris Kelas 11
                            $col_227=$fetch_rombel[0]->col_227;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 11
                            $col_228=$fetch_rombel[0]->col_228;
                            //Alokasi waktu per minggu Mata Pelajaran Antropologi Kelas 11
                            $col_229=$fetch_rombel[0]->col_229;
                            //Alokasi waktu per minggu Mata Pelajaran Pendalaman Kelas 11
                            $col_230=$fetch_rombel[0]->col_230;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 12
                            $col_231=$fetch_rombel[0]->col_231;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 12
                            $col_232=$fetch_rombel[0]->col_232;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 12
                            $col_233=$fetch_rombel[0]->col_233;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 12
                            $col_234=$fetch_rombel[0]->col_234;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 12
                            $col_235=$fetch_rombel[0]->col_235;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Inggris Kelas 12
                            $col_236=$fetch_rombel[0]->col_236;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya Kelas 12
                            $col_237=$fetch_rombel[0]->col_237;
                            //Alokasi waktu per minggu Mata Pelajaran Sejarah Indonesia Kelas 12
                            $col_238=$fetch_rombel[0]->col_238;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya dan Kewirausahaan Kelas 12
                            $col_239=$fetch_rombel[0]->col_239;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika (Peminatan) Kelas 12
                            $col_240=$fetch_rombel[0]->col_240;
                            //Alokasi waktu per minggu Mata Pelajaran Biologi Kelas 12
                            $col_241=$fetch_rombel[0]->col_241;
                            //Alokasi waktu per minggu Mata Pelajaran Fisika Kelas 12
                            $col_242=$fetch_rombel[0]->col_242;
                            //Alokasi waktu per minggu Mata Pelajaran Kimia Kelas 12
                            $col_243=$fetch_rombel[0]->col_243;
                            //Alokasi waktu per minggu Mata Pelajaran Geografi Kelas 12
                            $col_244=$fetch_rombel[0]->col_244;
                            //Alokasi waktu per minggu Mata Pelajaran Sejarah Kelas 12
                            $col_245=$fetch_rombel[0]->col_245;
                            //Alokasi waktu per minggu Mata Pelajaran Sosiologi dan Antropologi Kelas 12
                            $col_246=$fetch_rombel[0]->col_246;
                            //Alokasi waktu per minggu Mata Pelajaran Ekonomi Kelas 12
                            $col_247=$fetch_rombel[0]->col_247;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 12
                            $col_248=$fetch_rombel[0]->col_248;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Inggris Kelas 12
                            $col_249=$fetch_rombel[0]->col_249;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 12
                            $col_250=$fetch_rombel[0]->col_250;
                            //Alokasi waktu per minggu Mata Pelajaran Antropologi Kelas 12
                            $col_251=$fetch_rombel[0]->col_251;
                            //Alokasi waktu per minggu Mata Pelajaran Pendalaman Kelas 12
                            $col_252=$fetch_rombel[0]->col_252;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Agama dan Budi Pekerti Kelas 13
                            $col_253=$fetch_rombel[0]->col_253;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan Kelas 13
                            $col_254=$fetch_rombel[0]->col_254;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Indonesia Kelas 13
                            $col_255=$fetch_rombel[0]->col_255;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika Kelas 13
                            $col_256=$fetch_rombel[0]->col_256;
                            //Alokasi waktu per minggu Mata Pelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan Kelas 13
                            $col_257=$fetch_rombel[0]->col_257;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa Inggris Kelas 13
                            $col_258=$fetch_rombel[0]->col_258;
                            //Alokasi waktu per minggu Mata Pelajaran Seni Budaya Kelas 13
                            $col_259=$fetch_rombel[0]->col_259;
                            //Alokasi waktu per minggu Mata Pelajaran Sejarah Indonesia Kelas 13
                            $col_260=$fetch_rombel[0]->col_260;
                            //Alokasi waktu per minggu Mata Pelajaran Prakarya dan Kewirausahaan Kelas 13
                            $col_261=$fetch_rombel[0]->col_261;
                            //Alokasi waktu per minggu Mata Pelajaran Matematika (Peminatan) Kelas 13
                            $col_262=$fetch_rombel[0]->col_262;
                            //Alokasi waktu per minggu Mata Pelajaran Biologi Kelas 13
                            $col_263=$fetch_rombel[0]->col_263;
                            //Alokasi waktu per minggu Mata Pelajaran Fisika Kelas 13
                            $col_264=$fetch_rombel[0]->col_264;
                            //Alokasi waktu per minggu Mata Pelajaran Kimia Kelas 13
                            $col_265=$fetch_rombel[0]->col_265;
                            //Alokasi waktu per minggu Mata Pelajaran Geografi Kelas 13
                            $col_266=$fetch_rombel[0]->col_266;
                            //Alokasi waktu per minggu Mata Pelajaran Sejarah Kelas 13
                            $col_267=$fetch_rombel[0]->col_267;
                            //Alokasi waktu per minggu Mata Pelajaran Sosiologi dan Antropologi Kelas 13
                            $col_268=$fetch_rombel[0]->col_268;
                            //Alokasi waktu per minggu Mata Pelajaran Ekonomi Kelas 13
                            $col_269=$fetch_rombel[0]->col_269;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Indonesia Kelas 13
                            $col_270=$fetch_rombel[0]->col_270;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Inggris Kelas 13
                            $col_271=$fetch_rombel[0]->col_271;
                            //Alokasi waktu per minggu Mata Pelajaran Bahasa dan Sastra Asing Lainnya Kelas 13
                            $col_272=$fetch_rombel[0]->col_272;
                            //Alokasi waktu per minggu Mata Pelajaran Antropologi Kelas 13
                            $col_273=$fetch_rombel[0]->col_273;
                            //Alokasi waktu per minggu Mata Pelajaran Pendalaman Kelas 13
                            $col_274=$fetch_rombel[0]->col_274;
                            //Alokasi waktu per satu jam tatap muka
                            $col_275=$fetch_rombel[0]->col_275;
                        }else{
                            //Jumla00
                            $col_1038=0;
                            //Jumla01
                            $col_1039=0;
                            //Jumla02
                            $col_1040=0;
                            //Jumla03
                            $col_1041=0;
                            //Alokas00
                            $col_1067=0;
                            //Alokas01
                            $col_1068=0;
                            //Alokas02
                            $col_1069=0;
                            //Alokas03
                            $col_1070=0;
                            //Kriteri03
                            $col_1071=0;
                            //Kriteri03
                            $col_1072=0;
                            //Kriteri03
                            $col_1073=0;
                            //Kriteri03
                            $col_1074=0;
                            //Kriteri03
                            $col_1075=0;
                            //Kriteri03
                            $col_1076=0;
                            //Kriteri03
                            $col_1077=0;
                            //Kriteri03
                            $col_1078=0;
                            //Kriteri03
                            $col_1079=0;
                            //Kriteri03
                            $col_1080=0;
                            //Kriteri03
                            $col_1081=0;
                            //Kriteri03
                            $col_1082=0;
                            //Kriteri03
                            $col_1083=0;
                            //Kriteri03
                            $col_1084=0;
                            //Kriteri03
                            $col_1085=0;
                            //Kriteri03
                            $col_1086=0;
                            //Kriteri03
                            $col_1087=0;
                            //Kriteri03
                            $col_1088=0;
                            //Kriteri03
                            $col_1089=0;
                            //Kriteri03
                            $col_1090=0;
                            //Kriteri03
                            $col_1091=0;
                            //Kriteri03
                            $col_1092=0;
                            //Kriteri03
                            $col_1093=0;
                            //Jumla01
                            $col_753=0;
                            //Jumla02
                            $col_754=0;
                            //Jumla03
                            $col_755=0;
                            //Jumla04
                            $col_756=0;
                            //Jumla05
                            $col_757=0;
                            //Jumla06
                            $col_758=0;
                            //Jumla07
                            $col_759=0;
                            //Jumla08
                            $col_760=0;
                            //Jumla09
                            $col_761=0;
                            //Alokas00
                            $col_995=0;
                            //Alokas00
                            $col_996=0;
                            //Alokas00
                            $col_997=0;
                            //Alokas01
                            $col_998=0;
                            //Alokas01
                            $col_999=0;
                            //Alokas01
                            $col_1000=0;
                            //Alokas02
                            $col_1001=0;
                            //Alokas02
                            $col_1002=0;
                            //Alokas02
                            $col_1003=0;
                            //Alokas03
                            $col_1004=0;
                            //Alokas03
                            $col_1005=0;
                            //Alokas03
                            $col_1006=0;
                            //Kriteri00
                            $col_1007=0;
                            //Kriteri00
                            $col_1008=0;
                            //Kriteri00
                            $col_1009=0;
                            //Kriteri01
                            $col_1010=0;
                            //Kriteri01
                            $col_1011=0;
                            //Kriteri01
                            $col_1012=0;
                            //Kriteri02
                            $col_1013=0;
                            //Kriteri02
                            $col_1014=0;
                            //Kriteri02
                            $col_1015=0;
                            //Kriteri03
                            $col_1016=0;
                            //Kriteri03
                            $col_1017=0;
                            //Kriteri03
                            $col_1018=0;
                            //Kriteri01
                            $col_796=0;
                            //Kriteri02
                            $col_797=0;
                            //Kriteri03
                            $col_798=0;
                            //Kriteri04
                            $col_799=0;
                            //Kriteri05
                            $col_800=0;
                            //Kriteri06
                            $col_801=0;
                            //Kriteri07
                            $col_802=0;
                            //Kriteri08
                            $col_803=0;
                            //Kriteri09
                            $col_804=0;
                            //Kriteri07
                            $col_805=0;
                            //Kriteri08
                            $col_806=0;
                            //Kriteri09
                            $col_807=0;
                            //Kriteri01
                            $col_808=0;
                            //Kriteri02
                            $col_809=0;
                            //Kriteri03
                            $col_810=0;
                            //Kriteri04
                            $col_811=0;
                            //Kriteri05
                            $col_812=0;
                            //Kriteri06
                            $col_813=0;
                            //Kriteri07
                            $col_814=0;
                            //Kriteri08
                            $col_815=0;
                            //Kriteri09
                            $col_816=0;
                            //Kriteri01
                            $col_817=0;
                            //Kriteri02
                            $col_818=0;
                            //Kriteri03
                            $col_819=0;
                            //Kriteri04
                            $col_820=0;
                            //Kriteri05
                            $col_821=0;
                            //Kriteri06
                            $col_822=0;
                            //Kriteri07
                            $col_823=0;
                            //Kriteri08
                            $col_824=0;
                            //Kriteri09
                            $col_825=0;
                            //Kriteri01
                            $col_826=0;
                            //Kriteri02
                            $col_827=0;
                            //Kriteri03
                            $col_828=0;
                            //Kriteri04
                            $col_829=0;
                            //Kriteri05
                            $col_830=0;
                            //Kriteri06
                            $col_831=0;
                            //Kriteri07
                            $col_832=0;
                            //Kriteri08
                            $col_833=0;
                            //Kriteri09
                            $col_834=0;
                            //Kriteri01
                            $col_835=0;
                            //Kriteri02
                            $col_836=0;
                            //Kriteri03
                            $col_837=0;
                            //Kriteri04
                            $col_838=0;
                            //Kriteri05
                            $col_839=0;
                            //Kriteri06
                            $col_840=0;
                            //Kriteri07
                            $col_841=0;
                            //Kriteri08
                            $col_842=0;
                            //Kriteri09
                            $col_843=0;
                            //Kriteri01
                            $col_844=0;
                            //Kriteri02
                            $col_845=0;
                            //Kriteri03
                            $col_846=0;
                            //Kriteri04
                            $col_847=0;
                            //Kriteri05
                            $col_848=0;
                            //Kriteri06
                            $col_849=0;
                            //Kriteri07
                            $col_850=0;
                            //Kriteri08
                            $col_851=0;
                            //Kriteri09
                            $col_852=0;
                            //Kriteri01
                            $col_853=0;
                            //Kriteri02
                            $col_854=0;
                            //Kriteri03
                            $col_855=0;
                            //Kriteri04
                            $col_856=0;
                            //Kriteri05
                            $col_857=0;
                            //Kriteri06
                            $col_858=0;
                            //Kriteri07
                            $col_859=0;
                            //Kriteri08
                            $col_860=0;
                            //Kriteri09
                            $col_861=0;
                            //Kriteri01
                            $col_862=0;
                            //Kriteri02
                            $col_863=0;
                            //Kriteri03
                            $col_864=0;
                            //Kriteri04
                            $col_865=0;
                            //Kriteri05
                            $col_866=0;
                            //Kriteri06
                            $col_867=0;
                            //Kriteri07
                            $col_868=0;
                            //Kriteri08
                            $col_869=0;
                            //Kriteri09
                            $col_870=0;
                            //Kriteri07
                            $col_871=0;
                            //Kriteri08
                            $col_872=0;
                            //Kriteri09
                            $col_873=0;
                            //Kriteri01
                            $col_874=0;
                            //Kriteri02
                            $col_875=0;
                            //Kriteri03
                            $col_876=0;
                            //Kriteri04
                            $col_877=0;
                            //Kriteri05
                            $col_878=0;
                            //Kriteri06
                            $col_879=0;
                            //Kriteri07
                            $col_880=0;
                            //Kriteri08
                            $col_881=0;
                            //Kriteri09
                            $col_882=0;
                            //Tota01
                            $col_641=0;
                            //Tota02
                            $col_642=0;
                            //Tota03
                            $col_643=0;
                            //Tota04
                            $col_644=0;
                            //Tota05
                            $col_645=0;
                            //Tota06
                            $col_646=0;
                            //Tota07
                            $col_647=0;
                            //Tota08
                            $col_648=0;
                            //Tota09
                            $col_649=0;
                            //Alokas01
                            $col_661=0;
                            //Alokas02
                            $col_662=0;
                            //Alokas03
                            $col_663=0;
                            //Alokas04
                            $col_664=0;
                            //Alokas05
                            $col_665=0;
                            //Alokas06
                            $col_666=0;
                            //Alokas07
                            $col_667=0;
                            //Alokas08
                            $col_668=0;
                            //Alokas09
                            $col_669=0;
                            //Alokas07
                            $col_670=0;
                            //Alokas08
                            $col_671=0;
                            //Alokas09
                            $col_672=0;
                            //Alokas01
                            $col_673=0;
                            //Alokas02
                            $col_674=0;
                            //Alokas03
                            $col_675=0;
                            //Alokas04
                            $col_676=0;
                            //Alokas05
                            $col_677=0;
                            //Alokas06
                            $col_678=0;
                            //Alokas07
                            $col_679=0;
                            //Alokas08
                            $col_680=0;
                            //Alokas09
                            $col_681=0;
                            //Alokas01
                            $col_682=0;
                            //Alokas02
                            $col_683=0;
                            //Alokas03
                            $col_684=0;
                            //Alokas04
                            $col_685=0;
                            //Alokas05
                            $col_686=0;
                            //Alokas06
                            $col_687=0;
                            //Alokas07
                            $col_688=0;
                            //Alokas08
                            $col_689=0;
                            //Alokas09
                            $col_690=0;
                            //Alokas01
                            $col_691=0;
                            //Alokas02
                            $col_692=0;
                            //Alokas03
                            $col_693=0;
                            //Alokas04
                            $col_694=0;
                            //Alokas05
                            $col_695=0;
                            //Alokas06
                            $col_696=0;
                            //Alokas07
                            $col_697=0;
                            //Alokas08
                            $col_698=0;
                            //Alokas09
                            $col_699=0;
                            //Alokas01
                            $col_700=0;
                            //Alokas02
                            $col_701=0;
                            //Alokas03
                            $col_702=0;
                            //Alokas04
                            $col_703=0;
                            //Alokas05
                            $col_704=0;
                            //Alokas06
                            $col_705=0;
                            //Alokas07
                            $col_706=0;
                            //Alokas08
                            $col_707=0;
                            //Alokas09
                            $col_708=0;
                            //Alokas01
                            $col_709=0;
                            //Alokas02
                            $col_710=0;
                            //Alokas03
                            $col_711=0;
                            //Alokas04
                            $col_712=0;
                            //Alokas05
                            $col_713=0;
                            //Alokas06
                            $col_714=0;
                            //Alokas07
                            $col_715=0;
                            //Alokas08
                            $col_716=0;
                            //Alokas09
                            $col_717=0;
                            //Alokas01
                            $col_718=0;
                            //Alokas02
                            $col_719=0;
                            //Alokas03
                            $col_720=0;
                            //Alokas04
                            $col_721=0;
                            //Alokas05
                            $col_722=0;
                            //Alokas06
                            $col_723=0;
                            //Alokas07
                            $col_724=0;
                            //Alokas08
                            $col_725=0;
                            //Alokas09
                            $col_726=0;
                            //Alokas01
                            $col_727=0;
                            //Alokas02
                            $col_728=0;
                            //Alokas03
                            $col_729=0;
                            //Alokas04
                            $col_730=0;
                            //Alokas05
                            $col_731=0;
                            //Alokas06
                            $col_732=0;
                            //Alokas07
                            $col_733=0;
                            //Alokas08
                            $col_734=0;
                            //Alokas09
                            $col_735=0;
                            //Alokas07
                            $col_736=0;
                            //Alokas08
                            $col_737=0;
                            //Alokas09
                            $col_738=0;
                            //Alokas01
                            $col_739=0;
                            //Alokas02
                            $col_740=0;
                            //Alokas03
                            $col_741=0;
                            //Alokas04
                            $col_742=0;
                            //Alokas05
                            $col_743=0;
                            //Alokas06
                            $col_744=0;
                            //Alokas07
                            $col_745=0;
                            //Alokas08
                            $col_746=0;
                            //Alokas09
                            $col_747=0;
                            //Kriteri00
                            $col_567=0;
                            //Kriteri00
                            $col_568=0;
                            //Kriteri00
                            $col_569=0;
                            //Kriteri00
                            $col_570=0;
                            //Kriteri00
                            $col_571=0;
                            //Kriteri00
                            $col_572=0;
                            //Kriteri00
                            $col_573=0;
                            //Kriteri00
                            $col_574=0;
                            //Kriteri00
                            $col_575=0;
                            //Kriteri00
                            $col_576=0;
                            //Kriteri00
                            $col_577=0;
                            //Kriteri00
                            $col_578=0;
                            //Kriteri00
                            $col_579=0;
                            //Kriteri00
                            $col_580=0;
                            //Kriteri00
                            $col_581=0;
                            //Kriteri00
                            $col_582=0;
                            //Kriteri00
                            $col_583=0;
                            //Kriteri00
                            $col_584=0;
                            //Kriteri00
                            $col_585=0;
                            //Kriteri00
                            $col_586=0;
                            //Kriteri00
                            $col_587=0;
                            //Kriteri00
                            $col_588=0;
                            //Kriteri00
                            $col_589=0;
                            //Kriteri01
                            $col_590=0;
                            //Kriteri01
                            $col_591=0;
                            //Kriteri01
                            $col_592=0;
                            //Kriteri01
                            $col_593=0;
                            //Kriteri01
                            $col_594=0;
                            //Kriteri01
                            $col_595=0;
                            //Kriteri01
                            $col_596=0;
                            //Kriteri01
                            $col_597=0;
                            //Kriteri01
                            $col_598=0;
                            //Kriteri01
                            $col_599=0;
                            //Kriteri01
                            $col_600=0;
                            //Kriteri01
                            $col_601=0;
                            //Kriteri01
                            $col_602=0;
                            //Kriteri01
                            $col_603=0;
                            //Kriteri01
                            $col_604=0;
                            //Kriteri01
                            $col_605=0;
                            //Kriteri01
                            $col_606=0;
                            //Kriteri01
                            $col_607=0;
                            //Kriteri01
                            $col_608=0;
                            //Kriteri01
                            $col_609=0;
                            //Kriteri01
                            $col_610=0;
                            //Kriteri01
                            $col_611=0;
                            //Kriteri01
                            $col_612=0;
                            //Kriteri02
                            $col_613=0;
                            //Kriteri02
                            $col_614=0;
                            //Kriteri02
                            $col_615=0;
                            //Kriteri02
                            $col_616=0;
                            //Kriteri02
                            $col_617=0;
                            //Kriteri02
                            $col_618=0;
                            //Kriteri02
                            $col_619=0;
                            //Kriteri02
                            $col_620=0;
                            //Kriteri02
                            $col_621=0;
                            //Kriteri02
                            $col_622=0;
                            //Kriteri02
                            $col_623=0;
                            //Kriteri02
                            $col_624=0;
                            //Kriteri02
                            $col_625=0;
                            //Kriteri02
                            $col_626=0;
                            //Kriteri02
                            $col_627=0;
                            //Kriteri02
                            $col_628=0;
                            //Kriteri02
                            $col_629=0;
                            //Kriteri02
                            $col_630=0;
                            //Kriteri02
                            $col_631=0;
                            //Kriteri02
                            $col_632=0;
                            //Kriteri02
                            $col_633=0;
                            //Kriteri02
                            $col_634=0;
                            //Kriteri02
                            $col_635=0;
                            //Tota00
                            $col_33=0;
                            //Tota01
                            $col_34=0;
                            //Tota02
                            $col_35=0;
                            //Tota03
                            $col_36=0;
                            //Jumla0r
                            $col_50=0;
                            //Alokas00
                            $col_187=0;
                            //Alokas00
                            $col_188=0;
                            //Alokas00
                            $col_189=0;
                            //Alokas00
                            $col_190=0;
                            //Alokas00
                            $col_191=0;
                            //Alokas00
                            $col_192=0;
                            //Alokas00
                            $col_193=0;
                            //Alokas00
                            $col_194=0;
                            //Alokas00
                            $col_195=0;
                            //Alokas00
                            $col_196=0;
                            //Alokas00
                            $col_197=0;
                            //Alokas00
                            $col_198=0;
                            //Alokas00
                            $col_199=0;
                            //Alokas00
                            $col_200=0;
                            //Alokas00
                            $col_201=0;
                            //Alokas00
                            $col_202=0;
                            //Alokas00
                            $col_203=0;
                            //Alokas00
                            $col_204=0;
                            //Alokas00
                            $col_205=0;
                            //Alokas00
                            $col_206=0;
                            //Alokas00
                            $col_207=0;
                            //Alokas00
                            $col_208=0;
                            //Alokas01
                            $col_209=0;
                            //Alokas01
                            $col_210=0;
                            //Alokas01
                            $col_211=0;
                            //Alokas01
                            $col_212=0;
                            //Alokas01
                            $col_213=0;
                            //Alokas01
                            $col_214=0;
                            //Alokas01
                            $col_215=0;
                            //Alokas01
                            $col_216=0;
                            //Alokas01
                            $col_217=0;
                            //Alokas01
                            $col_218=0;
                            //Alokas01
                            $col_219=0;
                            //Alokas01
                            $col_220=0;
                            //Alokas01
                            $col_221=0;
                            //Alokas01
                            $col_222=0;
                            //Alokas01
                            $col_223=0;
                            //Alokas01
                            $col_224=0;
                            //Alokas01
                            $col_225=0;
                            //Alokas01
                            $col_226=0;
                            //Alokas01
                            $col_227=0;
                            //Alokas01
                            $col_228=0;
                            //Alokas01
                            $col_229=0;
                            //Alokas01
                            $col_230=0;
                            //Alokas02
                            $col_231=0;
                            //Alokas02
                            $col_232=0;
                            //Alokas02
                            $col_233=0;
                            //Alokas02
                            $col_234=0;
                            //Alokas02
                            $col_235=0;
                            //Alokas02
                            $col_236=0;
                            //Alokas02
                            $col_237=0;
                            //Alokas02
                            $col_238=0;
                            //Alokas02
                            $col_239=0;
                            //Alokas02
                            $col_240=0;
                            //Alokas02
                            $col_241=0;
                            //Alokas02
                            $col_242=0;
                            //Alokas02
                            $col_243=0;
                            //Alokas02
                            $col_244=0;
                            //Alokas02
                            $col_245=0;
                            //Alokas02
                            $col_246=0;
                            //Alokas02
                            $col_247=0;
                            //Alokas02
                            $col_248=0;
                            //Alokas02
                            $col_249=0;
                            //Alokas02
                            $col_250=0;
                            //Alokas02
                            $col_251=0;
                            //Alokas02
                            $col_252=0;
                            //Alokas03
                            $col_253=0;
                            //Alokas03
                            $col_254=0;
                            //Alokas03
                            $col_255=0;
                            //Alokas03
                            $col_256=0;
                            //Alokas03
                            $col_257=0;
                            //Alokas03
                            $col_258=0;
                            //Alokas03
                            $col_259=0;
                            //Alokas03
                            $col_260=0;
                            //Alokas03
                            $col_261=0;
                            //Alokas03
                            $col_262=0;
                            //Alokas03
                            $col_263=0;
                            //Alokas03
                            $col_264=0;
                            //Alokas03
                            $col_265=0;
                            //Alokas03
                            $col_266=0;
                            //Alokas03
                            $col_267=0;
                            //Alokas03
                            $col_268=0;
                            //Alokas03
                            $col_269=0;
                            //Alokas03
                            $col_270=0;
                            //Alokas03
                            $col_271=0;
                            //Alokas03
                            $col_272=0;
                            //Alokas03
                            $col_273=0;
                            //Alokas03
                            $col_274=0;
                            //Alokas0a
                            $col_275=0;
                        }

                        $sql_sarana = "SELECT TOP 1 * FROM dm.sarana WHERE sekolah_id='".$record->sekolah_id."'";
                        $fetch_sarana = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_sarana));

                        // echo json_encode($fetch_sarana);die;

                        if(sizeof($fetch_sarana) > 0){
                            //Rata-rata jumlah kursi siswa per ruang kelas [ruang kelas] [perabot]
                            $col_159=$fetch_sarana[0]->col_159;
                            //Jumlah kelas yang dilengkapi rak karya siswa [ruang kelas] [perabot]
                            $col_165=$fetch_sarana[0]->col_165;
                            //Tersedia tempat air dalam jamban [Jamban] [perlengkapan lain]
                            $col_52=$fetch_sarana[0]->col_52;
                            //Jenis kloset dalam jamban [Jamban] [perlengkapan lain]
                            $col_53=$fetch_sarana[0]->col_53;
                            //Jumlah kursi siswa yang berkondisi baik [ruang kelas] [perabot]
                            $col_183=$fetch_sarana[0]->col_183;
                            //Jumlah meja siswa yang berkondisi baik [ruang kelas] [perabot]
                            $col_184=$fetch_sarana[0]->col_184;
                            //Rata-rata jumlah ekslempar buku siswa setiap mata pelajaran [ruang perpustakaan] [buku]
                            $col_175=$fetch_sarana[0]->col_175;
                            //Rata-rata jumlah ekslempar buku guru setiap mata pelajaran [ruang perpustakaan] [buku]
                            $col_176=$fetch_sarana[0]->col_176;
                            //Jumlah judul buku referensi [ruang perpustakaan] [buku]
                            $col_297=$fetch_sarana[0]->col_297;
                            //Jumlah judul buku sumber belajar lain [ruang perpustakaan] [buku]
                            $col_305=$fetch_sarana[0]->col_305;
                            //Ketersediaan globe [ruang perpustakaan] [buku]
                            $col_308=$fetch_sarana[0]->col_308;
                            //Ketersediaan peta [ruang perpustakaan] [buku]
                            $col_309=$fetch_sarana[0]->col_309;
                            //Ketersediaan alat peraga matematika [ruang perpustakaan] [buku]
                            $col_312=$fetch_sarana[0]->col_312;
                            //Jumlah rak buku [ruang perpustakaan] [perabot]
                            $col_313=$fetch_sarana[0]->col_313;
                            //Jumlah rak majalah [ruang perpustakaan] [perabot]
                            $col_314=$fetch_sarana[0]->col_314;
                            //Jumlah rak surat kabar [ruang perpustakaan] [perabot]
                            $col_315=$fetch_sarana[0]->col_315;
                            //Jumlah meja baca [ruang perpustakaan] [perabot]
                            $col_316=$fetch_sarana[0]->col_316;
                            //Jumlah kursi baca [ruang perpustakaan] [perabot]
                            $col_317=$fetch_sarana[0]->col_317;
                            //Ketersediaan kursi petugas [ruang perpustakaan] [perabot]
                            $col_318=$fetch_sarana[0]->col_318;
                            //Ketersediaan meja petugas [ruang perpustakaan] [perabot]
                            $col_319=$fetch_sarana[0]->col_319;
                            //Ketersediaan lemari katalog [ruang perpustakaan] [perabot]
                            $col_320=$fetch_sarana[0]->col_320;
                            //Ketersediaan lemari [ruang perpustakaan] [perabot]
                            $col_321=$fetch_sarana[0]->col_321;
                            //Ketersediaan papan pengumuman [ruang perpustakaan] [perabot]
                            $col_322=$fetch_sarana[0]->col_322;
                            //Ketersediaan meja multimedia [ruang perpustakaan] [perabot]
                            $col_323=$fetch_sarana[0]->col_323;
                            //Ketersediaan peralatan multimedia [ruang perpustakaan] [media pendidikan]
                            $col_324=$fetch_sarana[0]->col_324;
                            //Ketersediaan tempat sampah [ruang perpustakaan] [perlengkapan lain]
                            $col_326=$fetch_sarana[0]->col_326;
                            //Ketersediaan soket listrik [ruang perpustakaan] [perlengkapan lain]
                            $col_327=$fetch_sarana[0]->col_327;
                            //Ketersediaan jam dinding [ruang perpustakaan] [perlengkapan lain]
                            $col_328=$fetch_sarana[0]->col_328;
                            //Jumlah meja kerja [konseling] [perabot]
                            $col_417=$fetch_sarana[0]->col_417;
                            //Jumlah kursi kerja [konseling] [perabot]
                            $col_418=$fetch_sarana[0]->col_418;
                            //Jumlah kursi tamu [konseling] [perabot]
                            $col_419=$fetch_sarana[0]->col_419;
                            //Jumlah lemari [konseling] [perabot]
                            $col_420=$fetch_sarana[0]->col_420;
                            //Jumlah papan kegiatan [konseling] [perabot]
                            $col_421=$fetch_sarana[0]->col_421;
                            //Ketersediaan instrumen konseling [konseling] [peralatan pendidikan]
                            $col_422=$fetch_sarana[0]->col_422;
                            //Ketersediaan buku sumber [konseling] [peralatan pendidikan]
                            $col_423=$fetch_sarana[0]->col_423;
                            //Ketersediaan media pengembangan kepribadian [konseling] [peralatan pendidikan]
                            $col_424=$fetch_sarana[0]->col_424;
                            //Ketersediaan jam dinding [konseling] [perlengkapan lain]
                            $col_425=$fetch_sarana[0]->col_425;
                            //Tersedia lemari [gudang] [perabot]
                            $col_397=$fetch_sarana[0]->col_397;
                            //Tersedia rak [gudang] [perabot]
                            $col_398=$fetch_sarana[0]->col_398;
                            //Ketersediaan tiang bendera [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_406=$fetch_sarana[0]->col_406;
                            //Ketersediaan bendera [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_407=$fetch_sarana[0]->col_407;
                            //Jumlah peralatan bola voli [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_408=$fetch_sarana[0]->col_408;
                            //Jumlah peralatan sepak bola [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_409=$fetch_sarana[0]->col_409;
                            //Ketersediaan peralatan senam [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_410=$fetch_sarana[0]->col_410;
                            //Ketersediaan peralatan atletik [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_411=$fetch_sarana[0]->col_411;
                            //Ketersediaan peralatan seni budaya [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_412=$fetch_sarana[0]->col_412;
                            //Ketersediaan peralatan ketrampilan [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_413=$fetch_sarana[0]->col_413;
                            //Ketersediaan pengeras suara [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_414=$fetch_sarana[0]->col_414;
                            //Ketersediaan tape recorder [tempat bermain/olahraga] [peralatan pendidikan]
                            $col_415=$fetch_sarana[0]->col_415;
                            //Ketersediaan tempat tidur [UKS] [perabot]
                            $col_368=$fetch_sarana[0]->col_368;
                            //Ketersediaan lemari [UKS] [perabot]
                            $col_369=$fetch_sarana[0]->col_369;
                            //Ketersediaan meja [UKS] [perabot]
                            $col_370=$fetch_sarana[0]->col_370;
                            //Jumlah kursi yang tersedia [UKS] [perabot]
                            $col_371=$fetch_sarana[0]->col_371;
                            //Ketersediaan catatan kesehatan siswa [UKS] [perlengkapan lain]
                            $col_372=$fetch_sarana[0]->col_372;
                            //Ketersediaan P3K [UKS] [perlengkapan lain]
                            $col_373=$fetch_sarana[0]->col_373;
                            //Ketersediaan tandu [UKS] [perlengkapan lain]
                            $col_374=$fetch_sarana[0]->col_374;
                            //Ketersediaan selimut [UKS] [perlengkapan lain]
                            $col_375=$fetch_sarana[0]->col_375;
                            //Ketersediaan tensimeter [UKS] [perlengkapan lain]
                            $col_376=$fetch_sarana[0]->col_376;
                            //Ketersediaan termometer badan [UKS] [perlengkapan lain]
                            $col_377=$fetch_sarana[0]->col_377;
                            //Ketersediaan timbangan badan [UKS] [perlengkapan lain]
                            $col_378=$fetch_sarana[0]->col_378;
                            //Tersedia pengukur tinggi badan [UKS] [perlengkapan lain]
                            $col_379=$fetch_sarana[0]->col_379;
                            //Ketersediaan tempat sampah [UKS] [perlengkapan lain]
                            $col_380=$fetch_sarana[0]->col_380;
                            //Ketersediaan tempat cuci tangan [UKS] [perlengkapan lain]
                            $col_381=$fetch_sarana[0]->col_381;
                            //Ketersediaan jam dinding [UKS] [perlengkapan lain]
                            $col_382=$fetch_sarana[0]->col_382;
                            //Jumlah jamban untuk guru [Jamban] [ruangan]
                            $col_383=$fetch_sarana[0]->col_383;
                            //Rata-rata luas jamban [Jamban] [ruangan]
                            $col_384=$fetch_sarana[0]->col_384;
                            //Tersedia kloset dalam jamban [Jamban] [perlengkapan lain]
                            $col_390=$fetch_sarana[0]->col_390;
                            //Tersedia gayung dalam jamban [Jamban] [perlengkapan lain]
                            $col_392=$fetch_sarana[0]->col_392;
                            //Tersedia gantungan pakaian [Jamban] [perlengkapan lain]
                            $col_393=$fetch_sarana[0]->col_393;
                            //Tersedia tempat sampah [Jamban] [perlengkapan lain]
                            $col_394=$fetch_sarana[0]->col_394;
                            //Terdapat kursi pimpinan [ruang pimpinan] [perabot]
                            $col_331=$fetch_sarana[0]->col_331;
                            //Terdapat meja pimpinan [ruang pimpinan] [perabot]
                            $col_332=$fetch_sarana[0]->col_332;
                            //Terdapat kursi dan meja tamu [ruang pimpinan] [perabot]
                            $col_333=$fetch_sarana[0]->col_333;
                            //Terdapat lemari [ruang pimpinan] [perabot]
                            $col_334=$fetch_sarana[0]->col_334;
                            //Terdapat papan statistik [ruang pimpinan] [perabot]
                            $col_335=$fetch_sarana[0]->col_335;
                            //Terdapat simbol kenegaraan [ruang pimpinan] [perlengkapan lain]
                            $col_336=$fetch_sarana[0]->col_336;
                            //Terdapat tempat sampah [ruang pimpinan] [perlengkapan lain]
                            $col_337=$fetch_sarana[0]->col_337;
                            //Terdapat mesin ketik/komputer [ruang pimpinan] [perlengkapan lain]
                            $col_338=$fetch_sarana[0]->col_338;
                            //Terdapat filing cabinet [ruang pimpinan] [perlengkapan lain]
                            $col_339=$fetch_sarana[0]->col_339;
                            //Jumlah kursi kerja [ruang guru] [perabot]
                            $col_340=$fetch_sarana[0]->col_340;
                            //Jumlah meja kerja [ruang guru] [perabot]
                            $col_341=$fetch_sarana[0]->col_341;
                            //Jumlah lemari [ruang guru] [perabot]
                            $col_342=$fetch_sarana[0]->col_342;
                            //Ketersediaan kursi tamu [ruang guru] [perabot]
                            $col_343=$fetch_sarana[0]->col_343;
                            //Tersedia papan statistik [ruang guru] [perabot]
                            $col_344=$fetch_sarana[0]->col_344;
                            //Tersedia papa pengumuman [ruang guru] [perabot]
                            $col_345=$fetch_sarana[0]->col_345;
                            //Tersedia tempat sampah [ruang guru] [perlengkapan lain]
                            $col_346=$fetch_sarana[0]->col_346;
                            //Tersedia tempat cuci tangan [ruang guru] [perlengkapan lain]
                            $col_347=$fetch_sarana[0]->col_347;
                            //Tersedia jam dinding [ruang guru] [perlengkapan lain]
                            $col_348=$fetch_sarana[0]->col_348;
                            //Tersedia penanda waktu [ruang guru] [perlengkapan lain]
                            $col_349=$fetch_sarana[0]->col_349;
                            //Jumlah kursi kerja [ruang tata usaha] [perabot]
                            $col_350=$fetch_sarana[0]->col_350;
                            //Jumlah meja kerja [ruang tata usaha] [perabot]
                            $col_351=$fetch_sarana[0]->col_351;
                            //Jumlah lemari [ruang tata usaha] [perabot]
                            $col_352=$fetch_sarana[0]->col_352;
                            //Jumlah papan statistik [ruang tata usaha] [perabot]
                            $col_353=$fetch_sarana[0]->col_353;
                            //Terdapat mesin ketik/komputer [ruang tata usaha] [perlengkapan lain]
                            $col_354=$fetch_sarana[0]->col_354;
                            //Terdapat filing cabinet [ruang tata usaha] [perlengkapan lain]
                            $col_355=$fetch_sarana[0]->col_355;
                            //Terdapat brankas [ruang tata usaha] [perlengkapan lain]
                            $col_356=$fetch_sarana[0]->col_356;
                            //Terdapat telepon [ruang tata usaha] [perlengkapan lain]
                            $col_357=$fetch_sarana[0]->col_357;
                            //Terdapat jam dinding [ruang tata usaha] [perlengkapan lain]
                            $col_358=$fetch_sarana[0]->col_358;
                            //Ketersediaan soket listrik [ruang tata usaha] [perlengkapan lain]
                            $col_359=$fetch_sarana[0]->col_359;
                            //Tersedia penanda waktu [ruang tata usaha] [perlengkapan lain]
                            $col_360=$fetch_sarana[0]->col_360;
                            //Tersedia tempat sampah [ruang tata usaha] [perlengkapan lain]
                            $col_361=$fetch_sarana[0]->col_361;
                            //Jumlah kursi siswa [ruang laboratorium komputer] [perabot]
                            $col_492=$fetch_sarana[0]->col_492;
                            //Jumlah kursi siswa yang berkondisi baik [ruang laboratorium komputer] [perabot]
                            $col_493=$fetch_sarana[0]->col_493;
                            //Jumlah meja siswa [ruang laboratorium komputer] [perabot]
                            $col_494=$fetch_sarana[0]->col_494;
                            //Jumlah meja siswa yang berkondisi baik [ruang laboratorium komputer] [perabot]
                            $col_495=$fetch_sarana[0]->col_495;
                            //Rata-rata kapasitas meja siswa [ruang laboratorium komputer] [perabot]
                            $col_496=$fetch_sarana[0]->col_496;
                            //Ketersediaan kursi guru [ruang laboratorium komputer] [perabot]
                            $col_497=$fetch_sarana[0]->col_497;
                            //Ketersediaan meja guru [ruang laboratorium komputer] [perabot]
                            $col_498=$fetch_sarana[0]->col_498;
                            //Jumlah komputer yang tersedia [ruang laboratorium komputer] [peralatan pendidikan]
                            $col_499=$fetch_sarana[0]->col_499;
                            //Ketersediaan printer [ruang laboratorium komputer] [peralatan pendidikan]
                            $col_500=$fetch_sarana[0]->col_500;
                            //Ketersediaan scanner [ruang laboratorium komputer] [peralatan pendidikan]
                            $col_501=$fetch_sarana[0]->col_501;
                            //Ketersediaan titik akses internet [ruang laboratorium komputer] [peralatan pendidikan]
                            $col_502=$fetch_sarana[0]->col_502;
                            //Ketersediaan stabilizer [ruang laboratorium komputer] [peralatan pendidikan]
                            $col_504=$fetch_sarana[0]->col_504;
                            //Ketersediaan papan tulis [ruang laboratorium komputer] [media pendidikan]
                            $col_506=$fetch_sarana[0]->col_506;
                            //Ketersediaan soket listrik [ruang laboratorium komputer] [perlengkapan lain]
                            $col_507=$fetch_sarana[0]->col_507;
                            //Ketersediaan tempat sampah [ruang laboratorium komputer] [perlengkapan lain]
                            $col_508=$fetch_sarana[0]->col_508;
                            //Ketersediaan jam dinding [ruang laboratorium komputer] [perlengkapan lain]
                            $col_509=$fetch_sarana[0]->col_509;
                            //Jumlah kursi [ruang laboratorium kimia] [perabot]
                            $col_474=$fetch_sarana[0]->col_474;
                            //Jumlah meja siswa [ruang laboratorium kimia] [perabot]
                            $col_475=$fetch_sarana[0]->col_475;
                            //Rata-rata kapasitas meja siswa [ruang laboratorium kimia] [perabot]
                            $col_476=$fetch_sarana[0]->col_476;
                            //Ketersediaan meja demonstrasi [ruang laboratorium kimia] [perabot]
                            $col_477=$fetch_sarana[0]->col_477;
                            //Ketersediaan meja persiapan [ruang laboratorium kimia] [perabot]
                            $col_478=$fetch_sarana[0]->col_478;
                            //Ketersediaan lemari alat [ruang laboratorium kimia] [perabot]
                            $col_479=$fetch_sarana[0]->col_479;
                            //Ketersediaan lemari bahan [ruang laboratorium kimia] [perabot]
                            $col_480=$fetch_sarana[0]->col_480;
                            //Ketersediaan bak cuci [ruang laboratorium kimia] [perabot]
                            $col_482=$fetch_sarana[0]->col_482;
                            //Jumlah ragam peralatan yang tersedia [ruang laboratorium kimia] [peralatan pendidikan]
                            $col_483=$fetch_sarana[0]->col_483;
                            //Ketersediaan papan tulis [ruang laboratorium kimia] [media pendidikan]
                            $col_485=$fetch_sarana[0]->col_485;
                            //Ketersediaan soket listrik [ruang laboratorium kimia] [perlengkapan lain]
                            $col_486=$fetch_sarana[0]->col_486;
                            //Ketersediaan alat pemadam kebakaran [ruang laboratorium kimia] [perlengkapan lain]
                            $col_487=$fetch_sarana[0]->col_487;
                            //Ketersediaan peralatan P3K [ruang laboratorium kimia] [perlengkapan lain]
                            $col_488=$fetch_sarana[0]->col_488;
                            //Ketersediaan tempat sampah [ruang laboratorium kimia] [perlengkapan lain]
                            $col_489=$fetch_sarana[0]->col_489;
                            //Ketersediaan jam dinding [ruang laboratorium kimia] [perlengkapan lain]
                            $col_490=$fetch_sarana[0]->col_490;
                            //Jumlah kursi [ruang laboratorium fisika] [perabot]
                            $col_455=$fetch_sarana[0]->col_455;
                            //Jumlah meja siswa [ruang laboratorium fisika] [perabot]
                            $col_456=$fetch_sarana[0]->col_456;
                            //Rata-rata kapasitas meja siswa [ruang laboratorium fisika] [perabot]
                            $col_457=$fetch_sarana[0]->col_457;
                            //Ketersediaan meja demonstrasi [ruang laboratorium fisika] [perabot]
                            $col_458=$fetch_sarana[0]->col_458;
                            //Ketersediaan meja persiapan [ruang laboratorium fisika] [perabot]
                            $col_459=$fetch_sarana[0]->col_459;
                            //Ketersediaan lemari alat [ruang laboratorium fisika] [perabot]
                            $col_460=$fetch_sarana[0]->col_460;
                            //Ketersediaan lemari bahan [ruang laboratorium fisika] [perabot]
                            $col_461=$fetch_sarana[0]->col_461;
                            //Ketersediaan bak cuci [ruang laboratorium fisika] [perabot]
                            $col_462=$fetch_sarana[0]->col_462;
                            //Jumlah ragam bahan dan alat ukur dasar yang tersedia [ruang laboratorium fisika] [peralatan pendidikan]
                            $col_463=$fetch_sarana[0]->col_463;
                            //Ketersediaan papan tulis [ruang laboratorium fisika] [media pendidikan]
                            $col_465=$fetch_sarana[0]->col_465;
                            //Ketersediaan soket listrik [ruang laboratorium fisika] [perlengkapan lain]
                            $col_466=$fetch_sarana[0]->col_466;
                            //Ketersediaan alat pemadam kebakaran [ruang laboratorium fisika] [perlengkapan lain]
                            $col_467=$fetch_sarana[0]->col_467;
                            //Ketersediaan peralatan P3K [ruang laboratorium fisika] [perlengkapan lain]
                            $col_468=$fetch_sarana[0]->col_468;
                            //Ketersediaan tempat sampah [ruang laboratorium fisika] [perlengkapan lain]
                            $col_469=$fetch_sarana[0]->col_469;
                            //Ketersediaan jam dinding [ruang laboratorium fisika] [perlengkapan lain]
                            $col_470=$fetch_sarana[0]->col_470;
                            //Jumlah kursi [ruang laboratorium biologi] [perabot]
                            $col_435=$fetch_sarana[0]->col_435;
                            //Jumlah meja siswa [ruang laboratorium biologi] [perabot]
                            $col_436=$fetch_sarana[0]->col_436;
                            //Rata-rata kapasitas meja siswa [ruang laboratorium biologi] [perabot]
                            $col_437=$fetch_sarana[0]->col_437;
                            //Ketersediaan meja demonstrasi [ruang laboratorium biologi] [perabot]
                            $col_438=$fetch_sarana[0]->col_438;
                            //Ketersediaan meja persiapan [ruang laboratorium biologi] [perabot]
                            $col_439=$fetch_sarana[0]->col_439;
                            //Ketersediaan lemari alat [ruang laboratorium biologi] [perabot]
                            $col_440=$fetch_sarana[0]->col_440;
                            //Ketersediaan lemari bahan [ruang laboratorium biologi] [perabot]
                            $col_441=$fetch_sarana[0]->col_441;
                            //Ketersediaan bak cuci [ruang laboratorium biologi] [perabot]
                            $col_442=$fetch_sarana[0]->col_442;
                            //Jumlah ragam alat peraga yang tersedia [ruang laboratorium biologi] [peralatan pendidikan]
                            $col_443=$fetch_sarana[0]->col_443;
                            //Jumlah ragam alat dan bahan percobaan yang tersedia [ruang laboratorium biologi] [peralatan pendidikan]
                            $col_444=$fetch_sarana[0]->col_444;
                            //Ketersediaan papan tulis [ruang laboratorium biologi] [media pendidikan]
                            $col_446=$fetch_sarana[0]->col_446;
                            //Ketersediaan soket listrik [ruang laboratorium biologi] [perlengkapan lain]
                            $col_447=$fetch_sarana[0]->col_447;
                            //Ketersediaan alat pemadam kebakaran [ruang laboratorium biologi] [perlengkapan lain]
                            $col_448=$fetch_sarana[0]->col_448;
                            //Ketersediaan peralatan P3K [ruang laboratorium biologi] [perlengkapan lain]
                            $col_449=$fetch_sarana[0]->col_449;
                            //Ketersediaan tempat sampah [ruang laboratorium biologi] [perlengkapan lain]
                            $col_450=$fetch_sarana[0]->col_450;
                            //Ketersediaan jam dinding [ruang laboratorium biologi] [perlengkapan lain]
                            $col_451=$fetch_sarana[0]->col_451;
                            //Jumlah kursi [ruang laboratorium IPA] [perabot]
                            $col_748=$fetch_sarana[0]->col_748;
                            //Jumlah kursi dengan kondisi baik [ruang laboratorium IPA] [perabot]
                            $col_749=$fetch_sarana[0]->col_749;
                            //Jumlah meja siswa [ruang laboratorium IPA] [perabot]
                            $col_750=$fetch_sarana[0]->col_750;
                            //Jumlah meja siswa dengan kondisi baik [ruang laboratorium IPA] [perabot]
                            $col_751=$fetch_sarana[0]->col_751;
                            //Jumlah ragam peralatan yang tersedia [ruang laboratorium IPA] [peralatan pendidikan]
                            $col_752=$fetch_sarana[0]->col_752;
                            //Jumlah kursi siswa [ruang laboratorium bahasa] [perabot]
                            $col_511=$fetch_sarana[0]->col_511;
                            //Jumlah kursi siswa yang berkondisi baik [ruang laboratorium bahasa] [perabot]
                            $col_512=$fetch_sarana[0]->col_512;
                            //Jumlah meja siswa [ruang laboratorium bahasa] [perabot]
                            $col_513=$fetch_sarana[0]->col_513;
                            //Jumlah meja siswa yang berkondisi baik [ruang laboratorium bahasa] [perabot]
                            $col_514=$fetch_sarana[0]->col_514;
                            //Rata-rata kapasitas meja siswa [ruang laboratorium bahasa] [perabot]
                            $col_515=$fetch_sarana[0]->col_515;
                            //Ketersediaan kursi guru [ruang laboratorium bahasa] [perabot]
                            $col_516=$fetch_sarana[0]->col_516;
                            //Ketersediaan meja guru [ruang laboratorium bahasa] [perabot]
                            $col_517=$fetch_sarana[0]->col_517;
                            //Ketersediaan lemari [ruang laboratorium bahasa] [perabot]
                            $col_518=$fetch_sarana[0]->col_518;
                            //Ketersediaan perangkat multimedia [ruang laboratorium bahasa] [peralatan pendidikan]
                            $col_519=$fetch_sarana[0]->col_519;
                            //Ketersediaan papan tulis [ruang laboratorium bahasa] [media pendidikan]
                            $col_520=$fetch_sarana[0]->col_520;
                            //Ketersediaan soket listrik [ruang laboratorium bahasa] [perlengkapan lain]
                            $col_521=$fetch_sarana[0]->col_521;
                            //Ketersediaan tempat sampah [ruang laboratorium bahasa] [perlengkapan lain]
                            $col_522=$fetch_sarana[0]->col_522;
                            //Ketersediaan jam dinding [ruang laboratorium bahasa] [perlengkapan lain]
                            $col_523=$fetch_sarana[0]->col_523;
                            //Tersedia meja [ruang organisasi kesiswaan] [perabot]
                            $col_427=$fetch_sarana[0]->col_427;
                            //Jumlah kursi yang tersedia [ruang organisasi kesiswaan] [perabot]
                            $col_428=$fetch_sarana[0]->col_428;
                            //Tersedia papan tulis [ruang organisasi kesiswaan] [perabot]
                            $col_429=$fetch_sarana[0]->col_429;
                            //Tersedia lemari [ruang organisasi kesiswaan] [perabot]
                            $col_430=$fetch_sarana[0]->col_430;
                            //Tersedia jam dinding [ruang organisasi kesiswaan] [perabot]
                            $col_431=$fetch_sarana[0]->col_431;
                            //Ketersediaan lemari [tempat ibadah] [perabot]
                            $col_364=$fetch_sarana[0]->col_364;
                            //Ketersediaan jam dinding [tempat ibadah] [perlengkapan lain]
                            $col_365=$fetch_sarana[0]->col_365;
                            //Ketersediaan perlengkapan ibadah [tempat ibadah] [perlengkapan lain]
                            $col_366=$fetch_sarana[0]->col_366;
                            //Ketersediaan alat pemadam kebakaran [ruang laboratorium IPA] [perlengkapan lain]
                            $col_781=$fetch_sarana[0]->col_781;
                            //Ketersediaan bak cuci [ruang laboratorium IPA] [perabot]
                            $col_782=$fetch_sarana[0]->col_782;
                            //Terdapat brankas [ruang pimpinan] [perlengkapan lain]
                            $col_982=$fetch_sarana[0]->col_982;
                            //Ketersediaan jam dinding [ruang laboratorium IPA] [perlengkapan lain]
                            $col_784=$fetch_sarana[0]->col_784;
                            //Ketersediaan lemari [ruang laboratorium IPA] [perabot]
                            $col_785=$fetch_sarana[0]->col_785;
                            //Ketersediaan lemari alat [ruang laboratorium IPA] [perabot]
                            $col_786=$fetch_sarana[0]->col_786;
                            //Ketersediaan lemari bahan [ruang laboratorium IPA] [perabot]
                            $col_787=$fetch_sarana[0]->col_787;
                            //Ketersediaan meja demonstrasi [ruang laboratorium IPA] [perabot]
                            $col_788=$fetch_sarana[0]->col_788;
                            //Ketersediaan meja persiapan [ruang laboratorium IPA] [perabot]
                            $col_789=$fetch_sarana[0]->col_789;
                            //Ketersediaan papan tulis [ruang laboratorium IPA] [media pendidikan]
                            $col_790=$fetch_sarana[0]->col_790;
                            //Ketersediaan peralatan P3K [ruang laboratorium IPA] [perlengkapan lain]
                            $col_791=$fetch_sarana[0]->col_791;
                            //Ketersediaan soket listrik [ruang laboratorium IPA] [perlengkapan lain]
                            $col_792=$fetch_sarana[0]->col_792;
                            //Ketersediaan tempat sampah [ruang laboratorium IPA] [perlengkapan lain]
                            $col_793=$fetch_sarana[0]->col_793;
                            //Kondisi meja demonstrasi [ruang laboratorium IPA] [perabot]
                            $col_794=$fetch_sarana[0]->col_794;
                            //Kondisi meja persiapan [ruang laboratorium IPA] [perabot]
                            $col_795=$fetch_sarana[0]->col_795;
                            //Jumlah ruang yang memiliki papan tulis/data [ruang pembelajaran khusus] [media pendidikan]
                            $col_1031=$fetch_sarana[0]->col_1031;
                            //Jumlah ruang yang memiliki soket listrik [ruang pembelajaran khusus] [perlengkapan lain]
                            $col_1032=$fetch_sarana[0]->col_1032;
                            //Jumlah ruang yang memiliki tempat sampah [ruang pembelajaran khusus] [perlengkapan lain]
                            $col_1033=$fetch_sarana[0]->col_1033;
                            //Jumlah ruang yang tersedia meja kerja [ruang praktik gambar teknik] [perabot]
                            $col_1034=$fetch_sarana[0]->col_1034;
                            //Jumlah ruang yang tersedia kursi kerja [ruang praktik gambar teknik] [perabot]
                            $col_1035=$fetch_sarana[0]->col_1035;
                            //Jumlah ruang yang tersedia lemari simpan [ruang praktik gambar teknik] [perabot]
                            $col_1036=$fetch_sarana[0]->col_1036;
                            //Jumlah ruang yang tersedia peralatan yang sesuai [ruang praktik gambar teknik] [peralatan pendidikan]
                            $col_1037=$fetch_sarana[0]->col_1037;
                            //Ketersediaan meja gambar [ruang praktik gambar teknik] [perabot]
                            $col_1020=$fetch_sarana[0]->col_1020;
                            //Ketersediaan kursi gambar [ruang praktik gambar teknik] [perabot]
                            $col_1021=$fetch_sarana[0]->col_1021;
                            //Ketersediaan lemari [ruang praktik gambar teknik] [perabot]
                            $col_1022=$fetch_sarana[0]->col_1022;
                            //Ketersediaan peralatan menggambar [ruang praktik gambar teknik] [peralatan pendidikan]
                            $col_1023=$fetch_sarana[0]->col_1023;
                            //Ketersediaan papan tulis [ruang praktik gambar teknik] [media pendidikan]
                            $col_1024=$fetch_sarana[0]->col_1024;
                            //Ketersediaan soket listrik [ruang praktik gambar teknik] [perlengkapan lain]
                            $col_1025=$fetch_sarana[0]->col_1025;
                            //Ketersediaan tempat sampah [ruang praktik gambar teknik] [perlengkapan lain]
                            $col_1026=$fetch_sarana[0]->col_1026;
                            //Ketersediaan jam dinding [ruang praktik gambar teknik] [perlengkapan lain]
                            $col_1027=$fetch_sarana[0]->col_1027;
                            //Rata-rata jumlah meja siswa per ruang kelas [ruang kelas] [perabot]
                            $col_161=$fetch_sarana[0]->col_161;
                            //Rata-rata kapasitas meja siswa [ruang laboratorium IPA] [perabot]
                            $col_885=$fetch_sarana[0]->col_885;
                            //Tersedia papan pengumuman [ruang guru] [perabot]
                            $col_1098=$fetch_sarana[0]->col_1098;
                            //Total jumlah jamban yang tidak berfungsi
                            $col_1062=$fetch_sarana[0]->col_1062;
                            //Terdapat jam dinding [ruang pimpinan] [perlengkapan lain]
                            $col_1096=$fetch_sarana[0]->col_1096;
                        }else{
                            //Rat0]
                            $col_159=0;
                            //Jumla0]
                            $col_165=0;
                            //Tersedi0]
                            $col_52=0;
                            //Jeni0]
                            $col_53=0;
                            //Jumla0]
                            $col_183=0;
                            //Jumla0]
                            $col_184=0;
                            //Rat0]
                            $col_175=0;
                            //Rat0]
                            $col_176=0;
                            //Jumla0]
                            $col_297=0;
                            //Jumla0]
                            $col_305=0;
                            //Ketersediaa0]
                            $col_308=0;
                            //Ketersediaa0]
                            $col_309=0;
                            //Ketersediaa0]
                            $col_312=0;
                            //Jumla0]
                            $col_313=0;
                            //Jumla0]
                            $col_314=0;
                            //Jumla0]
                            $col_315=0;
                            //Jumla0]
                            $col_316=0;
                            //Jumla0]
                            $col_317=0;
                            //Ketersediaa0]
                            $col_318=0;
                            //Ketersediaa0]
                            $col_319=0;
                            //Ketersediaa0]
                            $col_320=0;
                            //Ketersediaa0]
                            $col_321=0;
                            //Ketersediaa0]
                            $col_322=0;
                            //Ketersediaa0]
                            $col_323=0;
                            //Ketersediaa0]
                            $col_324=0;
                            //Ketersediaa0]
                            $col_326=0;
                            //Ketersediaa0]
                            $col_327=0;
                            //Ketersediaa0]
                            $col_328=0;
                            //Jumla0]
                            $col_417=0;
                            //Jumla0]
                            $col_418=0;
                            //Jumla0]
                            $col_419=0;
                            //Jumla0]
                            $col_420=0;
                            //Jumla0]
                            $col_421=0;
                            //Ketersediaa0]
                            $col_422=0;
                            //Ketersediaa0]
                            $col_423=0;
                            //Ketersediaa0]
                            $col_424=0;
                            //Ketersediaa0]
                            $col_425=0;
                            //Tersedi0]
                            $col_397=0;
                            //Tersedi0]
                            $col_398=0;
                            //Ketersediaa0]
                            $col_406=0;
                            //Ketersediaa0]
                            $col_407=0;
                            //Jumla0]
                            $col_408=0;
                            //Jumla0]
                            $col_409=0;
                            //Ketersediaa0]
                            $col_410=0;
                            //Ketersediaa0]
                            $col_411=0;
                            //Ketersediaa0]
                            $col_412=0;
                            //Ketersediaa0]
                            $col_413=0;
                            //Ketersediaa0]
                            $col_414=0;
                            //Ketersediaa0]
                            $col_415=0;
                            //Ketersediaa0]
                            $col_368=0;
                            //Ketersediaa0]
                            $col_369=0;
                            //Ketersediaa0]
                            $col_370=0;
                            //Jumla0]
                            $col_371=0;
                            //Ketersediaa0]
                            $col_372=0;
                            //Ketersediaa0]
                            $col_373=0;
                            //Ketersediaa0]
                            $col_374=0;
                            //Ketersediaa0]
                            $col_375=0;
                            //Ketersediaa0]
                            $col_376=0;
                            //Ketersediaa0]
                            $col_377=0;
                            //Ketersediaa0]
                            $col_378=0;
                            //Tersedi0]
                            $col_379=0;
                            //Ketersediaa0]
                            $col_380=0;
                            //Ketersediaa0]
                            $col_381=0;
                            //Ketersediaa0]
                            $col_382=0;
                            //Jumla0]
                            $col_383=0;
                            //Rat0]
                            $col_384=0;
                            //Tersedi0]
                            $col_390=0;
                            //Tersedi0]
                            $col_392=0;
                            //Tersedi0]
                            $col_393=0;
                            //Tersedi0]
                            $col_394=0;
                            //Terdapa0]
                            $col_331=0;
                            //Terdapa0]
                            $col_332=0;
                            //Terdapa0]
                            $col_333=0;
                            //Terdapa0]
                            $col_334=0;
                            //Terdapa0]
                            $col_335=0;
                            //Terdapa0]
                            $col_336=0;
                            //Terdapa0]
                            $col_337=0;
                            //Terdapa0]
                            $col_338=0;
                            //Terdapa0]
                            $col_339=0;
                            //Jumla0]
                            $col_340=0;
                            //Jumla0]
                            $col_341=0;
                            //Jumla0]
                            $col_342=0;
                            //Ketersediaa0]
                            $col_343=0;
                            //Tersedi0]
                            $col_344=0;
                            //Tersedi0]
                            $col_345=0;
                            //Tersedi0]
                            $col_346=0;
                            //Tersedi0]
                            $col_347=0;
                            //Tersedi0]
                            $col_348=0;
                            //Tersedi0]
                            $col_349=0;
                            //Jumla0]
                            $col_350=0;
                            //Jumla0]
                            $col_351=0;
                            //Jumla0]
                            $col_352=0;
                            //Jumla0]
                            $col_353=0;
                            //Terdapa0]
                            $col_354=0;
                            //Terdapa0]
                            $col_355=0;
                            //Terdapa0]
                            $col_356=0;
                            //Terdapa0]
                            $col_357=0;
                            //Terdapa0]
                            $col_358=0;
                            //Ketersediaa0]
                            $col_359=0;
                            //Tersedi0]
                            $col_360=0;
                            //Tersedi0]
                            $col_361=0;
                            //Jumla0]
                            $col_492=0;
                            //Jumla0]
                            $col_493=0;
                            //Jumla0]
                            $col_494=0;
                            //Jumla0]
                            $col_495=0;
                            //Rat0]
                            $col_496=0;
                            //Ketersediaa0]
                            $col_497=0;
                            //Ketersediaa0]
                            $col_498=0;
                            //Jumla0]
                            $col_499=0;
                            //Ketersediaa0]
                            $col_500=0;
                            //Ketersediaa0]
                            $col_501=0;
                            //Ketersediaa0]
                            $col_502=0;
                            //Ketersediaa0]
                            $col_504=0;
                            //Ketersediaa0]
                            $col_506=0;
                            //Ketersediaa0]
                            $col_507=0;
                            //Ketersediaa0]
                            $col_508=0;
                            //Ketersediaa0]
                            $col_509=0;
                            //Jumla0]
                            $col_474=0;
                            //Jumla0]
                            $col_475=0;
                            //Rat0]
                            $col_476=0;
                            //Ketersediaa0]
                            $col_477=0;
                            //Ketersediaa0]
                            $col_478=0;
                            //Ketersediaa0]
                            $col_479=0;
                            //Ketersediaa0]
                            $col_480=0;
                            //Ketersediaa0]
                            $col_482=0;
                            //Jumla0]
                            $col_483=0;
                            //Ketersediaa0]
                            $col_485=0;
                            //Ketersediaa0]
                            $col_486=0;
                            //Ketersediaa0]
                            $col_487=0;
                            //Ketersediaa0]
                            $col_488=0;
                            //Ketersediaa0]
                            $col_489=0;
                            //Ketersediaa0]
                            $col_490=0;
                            //Jumla0]
                            $col_455=0;
                            //Jumla0]
                            $col_456=0;
                            //Rat0]
                            $col_457=0;
                            //Ketersediaa0]
                            $col_458=0;
                            //Ketersediaa0]
                            $col_459=0;
                            //Ketersediaa0]
                            $col_460=0;
                            //Ketersediaa0]
                            $col_461=0;
                            //Ketersediaa0]
                            $col_462=0;
                            //Jumla0]
                            $col_463=0;
                            //Ketersediaa0]
                            $col_465=0;
                            //Ketersediaa0]
                            $col_466=0;
                            //Ketersediaa0]
                            $col_467=0;
                            //Ketersediaa0]
                            $col_468=0;
                            //Ketersediaa0]
                            $col_469=0;
                            //Ketersediaa0]
                            $col_470=0;
                            //Jumla0]
                            $col_435=0;
                            //Jumla0]
                            $col_436=0;
                            //Rat0]
                            $col_437=0;
                            //Ketersediaa0]
                            $col_438=0;
                            //Ketersediaa0]
                            $col_439=0;
                            //Ketersediaa0]
                            $col_440=0;
                            //Ketersediaa0]
                            $col_441=0;
                            //Ketersediaa0]
                            $col_442=0;
                            //Jumla0]
                            $col_443=0;
                            //Jumla0]
                            $col_444=0;
                            //Ketersediaa0]
                            $col_446=0;
                            //Ketersediaa0]
                            $col_447=0;
                            //Ketersediaa0]
                            $col_448=0;
                            //Ketersediaa0]
                            $col_449=0;
                            //Ketersediaa0]
                            $col_450=0;
                            //Ketersediaa0]
                            $col_451=0;
                            //Jumla0]
                            $col_748=0;
                            //Jumla0]
                            $col_749=0;
                            //Jumla0]
                            $col_750=0;
                            //Jumla0]
                            $col_751=0;
                            //Jumla0]
                            $col_752=0;
                            //Jumla0]
                            $col_511=0;
                            //Jumla0]
                            $col_512=0;
                            //Jumla0]
                            $col_513=0;
                            //Jumla0]
                            $col_514=0;
                            //Rat0]
                            $col_515=0;
                            //Ketersediaa0]
                            $col_516=0;
                            //Ketersediaa0]
                            $col_517=0;
                            //Ketersediaa0]
                            $col_518=0;
                            //Ketersediaa0]
                            $col_519=0;
                            //Ketersediaa0]
                            $col_520=0;
                            //Ketersediaa0]
                            $col_521=0;
                            //Ketersediaa0]
                            $col_522=0;
                            //Ketersediaa0]
                            $col_523=0;
                            //Tersedi0]
                            $col_427=0;
                            //Jumla0]
                            $col_428=0;
                            //Tersedi0]
                            $col_429=0;
                            //Tersedi0]
                            $col_430=0;
                            //Tersedi0]
                            $col_431=0;
                            //Ketersediaa0]
                            $col_364=0;
                            //Ketersediaa0]
                            $col_365=0;
                            //Ketersediaa0]
                            $col_366=0;
                            //Ketersediaa0]
                            $col_781=0;
                            //Ketersediaa0]
                            $col_782=0;
                            //Terdapa0]
                            $col_982=0;
                            //Ketersediaa0]
                            $col_784=0;
                            //Ketersediaa0]
                            $col_785=0;
                            //Ketersediaa0]
                            $col_786=0;
                            //Ketersediaa0]
                            $col_787=0;
                            //Ketersediaa0]
                            $col_788=0;
                            //Ketersediaa0]
                            $col_789=0;
                            //Ketersediaa0]
                            $col_790=0;
                            //Ketersediaa0]
                            $col_791=0;
                            //Ketersediaa0]
                            $col_792=0;
                            //Ketersediaa0]
                            $col_793=0;
                            //Kondis0]
                            $col_794=0;
                            //Kondis0]
                            $col_795=0;
                            //Jumla0]
                            $col_1031=0;
                            //Jumla0]
                            $col_1032=0;
                            //Jumla0]
                            $col_1033=0;
                            //Jumla0]
                            $col_1034=0;
                            //Jumla0]
                            $col_1035=0;
                            //Jumla0]
                            $col_1036=0;
                            //Jumla0]
                            $col_1037=0;
                            //Ketersediaa0]
                            $col_1020=0;
                            //Ketersediaa0]
                            $col_1021=0;
                            //Ketersediaa0]
                            $col_1022=0;
                            //Ketersediaa0]
                            $col_1023=0;
                            //Ketersediaa0]
                            $col_1024=0;
                            //Ketersediaa0]
                            $col_1025=0;
                            //Ketersediaa0]
                            $col_1026=0;
                            //Ketersediaa0]
                            $col_1027=0;
                            //Rat0]
                            $col_161=0;
                            //Rat0]
                            $col_885=0;
                            //Tersedi0]
                            $col_1098=0;
                            //Tota0i
                            $col_1062=0;
                            //Terdapa0]
                            $col_1096=0;
                        }

                        $sql_sekolah = "SELECT TOP 1 * FROM dm.sekolah WHERE sekolah_id='".$record->sekolah_id."'";
                        $fetch_sekolah = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_sekolah));

                        // echo json_encode($fetch_sekolah);die;

                        if(sizeof($fetch_sekolah) > 0){
                            //Jumlah daya listrik sekolah [Bangunan gedung]
                            $col_51=$fetch_sekolah[0]->col_51;
                            //Bersedia menerima BOS
                            $col_62=$fetch_sekolah[0]->col_62;
                            //Jumlah blockgrant yang diterima
                            $col_63=$fetch_sekolah[0]->col_63;
                            //Kesediaan izin pemanfaatan tanah dari pemerintah setempat [Lahan]
                            $col_278=$fetch_sekolah[0]->col_278;
                            //Status hak atas tanah [Lahan]
                            $col_279=$fetch_sekolah[0]->col_279;
                            //Terdapat izin pendirian dan penggunaan bangunan [Bangunan gedung]
                            $col_291=$fetch_sekolah[0]->col_291;
                            //Luas lahan sekolah [Lahan]
                            $col_636=$fetch_sekolah[0]->col_636;
                            //Luas lahan sekolah [Lahan]
                            $col_638=$fetch_sekolah[0]->col_638;
                            //Kelas kualitas bangunan gedung [Bangunan gedung]
                            $col_1094=$fetch_sekolah[0]->col_1094;
                            //Daya tahan gedung bangunan [Bangunan gedung]
                            $col_1095=$fetch_sekolah[0]->col_1095;
                        }

                        $luasbangunan=($col_11*$col_12)+$col_13+$col_173+$col_174+$col_177+$col_178+$col_179+$col_180+$col_181+$col_182+$col_367+$col_395+$col_416+$col_426+$col_1099+$col_1056+$col_1097+$col_1099;
                        
                        if($col_2==0){$col_2=100000;};
                        if($col_50==0){$col_50=100000;};
                        
                        // tulis data
                        try {
                        
                        // switch ($bentuk_pendidikan_id) {
                        //     case '5':
                        //         self::ke_tigastandar($sekolah_id,'20001',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20002',number_format(self::maksi($col_42/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20003',number_format(self::maksi($col_43/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20005',number_format(self::maksi($col_783)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20006',number_format(self::maksi($col_525)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20007',number_format(self::maksi($col_157/self::cekbaginol($col_2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20012',number_format($col_659*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20013',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20014',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20015',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20016',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20017',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20023',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20024',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20026',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20027',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20036',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20037',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20039',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20040',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20041',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20042',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20043',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20054',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20055',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20057',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20058',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20059',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20069',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20070',number_format(self::stdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20071',self::statuslahan($col_278),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20072',self::milik($col_279),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20077',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20086',self::statuslahan($col_291),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20087',number_format(self::stdpln($col_51,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20088',number_format(self::stdpln($col_395,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20089',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20093',number_format(self::maksi($col_174/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20096',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20097',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20100',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20101',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20103',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20105',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20106',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20107',number_format(self::maksi($col_12/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20108',number_format(self::maksi($col_168/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20109',number_format(self::maksi($col_169/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20110',number_format(self::maksi($col_170/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20111',number_format(self::maksi($col_171/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20112',number_format(self::maksi($col_172/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20113',number_format(self::maksi($col_158/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20114',number_format(self::maksi($col_159/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20115',number_format(self::maksi($col_160/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20116',number_format(self::maksi($col_161/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20117',number_format(self::maksi($col_162/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20118',number_format(self::maksi($col_163/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20119',number_format(self::maksi($col_164/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20120',number_format(self::maksi($col_165/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20121',number_format(self::maksi($col_166/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20122',number_format(self::maksi($col_167/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20123',number_format(self::maksi($col_174/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20124',number_format(self::maksi($col_790)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20125',number_format(self::maksi($col_792)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20131',number_format(self::maksi($col_748/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20132',number_format(self::maksi($col_750/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20140',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20141',number_format(self::maksi($col_175/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20142',number_format(self::maksi($col_176/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20156',number_format(self::maksi($col_308)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20157',number_format(self::maksi($col_309)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20163',number_format(self::maksi($col_326)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20164',number_format(self::maksi($col_327)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20165',number_format(self::maksi($col_328)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20166',number_format(self::maksi($col_313)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20167',number_format(self::maksi($col_314)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20168',number_format(self::maksi($col_315)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20169',number_format(self::maksi($col_316)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20170',number_format(self::maksi($col_317)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20171',number_format(self::maksi($col_318)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20172',number_format(self::maksi($col_319)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20173',number_format(self::maksi($col_320)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20174',number_format(self::maksi($col_321)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20175',number_format(self::maksi($col_322)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20176',number_format(self::maksi($col_323)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20178',number_format(self::maksi($col_406)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20179',number_format(self::maksi($col_407)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20180',number_format(self::maksi($col_408)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20181',number_format(self::maksi($col_409)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20182',number_format(self::maksi($col_410)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20183',number_format(self::maksi($col_411)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20184',number_format(self::maksi($col_412)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20185',number_format(self::maksi($col_413)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20186',number_format(self::maksi($col_414)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20187',number_format(self::maksi($col_415)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20270',number_format(self::maksi($col_17/$col_11)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20271',number_format(self::maksi($col_183/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20272',number_format(self::maksi($col_184/(self::cekbaginol($col_14+$col_15)/2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20275',number_format(self::maksi_s($col_329)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20276',number_format(self::maksi($col_780)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20277',number_format(self::maksi($col_749/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20278',number_format(self::maksi($col_351/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20279',number_format(self::maksi($col_885/7)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20282',number_format(self::maksi($col_19)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20284',number_format(self::maksi_s($col_54)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20307',number_format(self::maksi($col_330/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20308',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20309',number_format(self::maksi($col_336)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20310',number_format(self::maksi($col_337)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20311',number_format(self::maksi($col_338)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20312',number_format(self::maksi($col_339)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20313',number_format(self::maksi($col_982)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20314',number_format(self::maksi($col_1096)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20315',number_format(self::maksi($col_331)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20316',number_format(self::maksi($col_332)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20317',number_format(self::maksi($col_333)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20318',number_format(self::maksi($col_334)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20319',number_format(self::maksi($col_335)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20320',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20321',number_format(self::maksi($col_340/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20322',number_format(self::maksi($col_341/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20323',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20324',number_format(self::maksi($col_343)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20325',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20326',number_format(self::maksi($col_345)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20327',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20328',number_format(self::maksi($col_368)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20329',number_format(self::maksi($col_369)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20330',number_format(self::maksi($col_370)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20331',number_format(self::maksi($col_371)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20332',number_format(self::maksi($col_362)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20333',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20334',number_format(self::maksi($col_364)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20335',number_format(self::stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20336',number_format(self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20337',number_format(self::maksi($col_383)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20338',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20339',number_format(self::maksi($col_390)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20341',number_format(self::maksi($col_392)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20342',number_format(self::maksi($col_393)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20343',number_format(self::maksi($col_394)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20344',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20345',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20346',number_format(self::maksi($col_395/18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20347',number_format(self::maksi($col_397)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20348',number_format(self::maksi($col_398)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20349',number_format(self::maksi($col_1099/self::cekbaginol(0.3*$col_638))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20364',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20366',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20367',number_format(self::maksi($col_346)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20368',number_format(self::maksi($col_347)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20369',number_format(self::maksi($col_348)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20370',number_format(self::maksi($col_349)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20371',number_format(self::maksi($col_372)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20372',number_format(self::maksi($col_373)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20373',number_format(self::maksi($col_374)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20374',number_format(self::maksi($col_375)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20375',number_format(self::maksi($col_376)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20376',number_format(self::maksi($col_377)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20377',number_format(self::maksi($col_378)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20378',number_format(self::maksi($col_379)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20379',number_format(self::maksi($col_380)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20380',number_format(self::maksi($col_381)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20381',number_format(self::maksi($col_382)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20382',number_format(self::maksi($col_365)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20383',number_format(self::maksi($col_366)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20384',number_format(self::maksi($col_16/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20385',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20386',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         break;
                        //     case '6':
                        //         self::ke_tigastandar($sekolah_id,'20001',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20004',number_format($col_1100*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20007',number_format(self::maksi($col_157/self::cekbaginol($col_2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20012',number_format($col_659*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20013',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20014',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20015',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20016',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20017',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20023',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20024',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20026',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20027',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20036',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20037',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20039',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20040',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20041',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20042',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20043',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20054',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20055',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20057',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20058',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20059',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20069',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20070',number_format(self::stdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20071',self::statuslahan($col_278),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20072',self::milik($col_279),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20077',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20086',self::statuslahan($col_291),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20087',number_format(self::stdpln($col_51,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20088',number_format(self::stdpln($col_395,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20089',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20093',number_format(self::maksi($col_174/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20096',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20097',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20098',number_format(self::maksi($col_416/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20099',number_format(self::maksi($col_426/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20100',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20101',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20102',number_format(self::maksi($col_182/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20103',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20105',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20106',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20107',number_format(self::maksi($col_12/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20108',number_format(self::maksi($col_168/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20109',number_format(self::maksi($col_169/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20110',number_format(self::maksi($col_170/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20111',number_format(self::maksi($col_171/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20112',number_format(self::maksi($col_172/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20113',number_format(self::maksi($col_158/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20114',number_format(self::maksi($col_159/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20115',number_format(self::maksi($col_160/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20116',number_format(self::maksi($col_161/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20117',number_format(self::maksi($col_162/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20118',number_format(self::maksi($col_163/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20119',number_format(self::maksi($col_164/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20120',number_format(self::maksi($col_165/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20121',number_format(self::maksi($col_166/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20122',number_format(self::maksi($col_167/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20123',number_format(self::maksi($col_174/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20125',number_format(self::maksi($col_792)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20126',number_format(self::maksi($col_781)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20127',number_format(self::maksi($col_791)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20128',number_format(self::maksi($col_793)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20129',number_format(self::maksi($col_784)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20131',number_format(self::maksi($col_748/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20132',number_format(self::maksi($col_750/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20133',number_format(self::maksi($col_788)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20134',number_format(self::maksi($col_789)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20135',number_format(self::maksi($col_786)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20136',number_format(self::maksi($col_787)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20137',number_format(self::maksi($col_782)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20138',number_format(self::maksi($col_785)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20140',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20141',number_format(self::maksi($col_175/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20142',number_format(self::maksi($col_176/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20156',number_format(self::maksi($col_308)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20157',number_format(self::maksi($col_309)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20163',number_format(self::maksi($col_326)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20164',number_format(self::maksi($col_327)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20165',number_format(self::maksi($col_328)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20166',number_format(self::maksi($col_313)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20167',number_format(self::maksi($col_314)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20168',number_format(self::maksi($col_315)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20169',number_format(self::maksi($col_316)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20170',number_format(self::maksi($col_317)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20171',number_format(self::maksi($col_318)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20172',number_format(self::maksi($col_319)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20173',number_format(self::maksi($col_320)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20174',number_format(self::maksi($col_321)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20175',number_format(self::maksi($col_322)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20176',number_format(self::maksi($col_323)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20178',number_format(self::maksi($col_406)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20179',number_format(self::maksi($col_407)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20180',number_format(self::maksi($col_408)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20181',number_format(self::maksi($col_409)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20182',number_format(self::maksi($col_410)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20183',number_format(self::maksi($col_411)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20184',number_format(self::maksi($col_412)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20185',number_format(self::maksi($col_413)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20186',number_format(self::maksi($col_414)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20187',number_format(self::maksi($col_415)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20270',number_format(self::maksi($col_17/$col_11)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20271',number_format(self::maksi($col_183/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20272',number_format(self::maksi($col_184/(self::cekbaginol($col_14+$col_15)/2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20275',number_format(self::maksi_s($col_329)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20276',number_format(self::maksi($col_780)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20277',number_format(self::maksi($col_749/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20278',number_format(self::maksi($col_351/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20279',number_format(self::maksi($col_885/7)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20280',number_format(self::maksi($col_794)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20281',number_format(self::maksi($col_795)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20282',number_format(self::maksi($col_19)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20284',number_format(self::maksi_s($col_54)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20307',number_format(self::maksi($col_330/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20308',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20309',number_format(self::maksi($col_336)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20310',number_format(self::maksi($col_337)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20311',number_format(self::maksi($col_338)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20312',number_format(self::maksi($col_339)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20313',number_format(self::maksi($col_982)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20314',number_format(self::maksi($col_1096)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20315',number_format(self::maksi($col_331)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20316',number_format(self::maksi($col_332)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20317',number_format(self::maksi($col_333)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20318',number_format(self::maksi($col_334)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20319',number_format(self::maksi($col_335)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20320',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20321',number_format(self::maksi($col_340/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20322',number_format(self::maksi($col_341/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20323',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20324',number_format(self::maksi($col_343)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20325',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20326',number_format(self::maksi($col_345)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20327',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20328',number_format(self::maksi($col_368)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20329',number_format(self::maksi($col_369)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20330',number_format(self::maksi($col_370)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20331',number_format(self::maksi($col_371)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20332',number_format(self::maksi($col_362)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20333',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20334',number_format(self::maksi($col_364)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20335',number_format(self::stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20336',number_format(self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20337',number_format(self::maksi($col_383)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20338',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20339',number_format(self::maksi($col_390)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20341',number_format(self::maksi($col_392)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20342',number_format(self::maksi($col_393)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20343',number_format(self::maksi($col_394)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20344',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20345',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20346',number_format(self::maksi($col_395/18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20347',number_format(self::maksi($col_397)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20348',number_format(self::maksi($col_398)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20349',number_format(self::maksi($col_1099/self::cekbaginol(0.3*$col_638))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20350',number_format(self::maksi($col_182/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20351',number_format(self::maksi($col_350)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20352',number_format(self::maksi($col_351)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20353',number_format(self::maksi($col_352)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20354',number_format(self::maksi($col_353)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20355',number_format(self::maksi($col_416/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20356',number_format(self::maksi($col_417)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20357',number_format(self::maksi($col_418)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20358',number_format(self::maksi($col_419)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20359',number_format(self::maksi($col_420)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20360',number_format(self::maksi($col_421)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20361',number_format(self::maksi($col_426/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20362',number_format(self::maksi($col_427)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20363',number_format(self::maksi($col_428/4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20364',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20366',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20367',number_format(self::maksi($col_346)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20368',number_format(self::maksi($col_347)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20369',number_format(self::maksi($col_348)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20370',number_format(self::maksi($col_349)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20371',number_format(self::maksi($col_372)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20372',number_format(self::maksi($col_373)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20373',number_format(self::maksi($col_374)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20374',number_format(self::maksi($col_375)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20375',number_format(self::maksi($col_376)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20376',number_format(self::maksi($col_377)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20377',number_format(self::maksi($col_378)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20378',number_format(self::maksi($col_379)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20379',number_format(self::maksi($col_380)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20380',number_format(self::maksi($col_381)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20381',number_format(self::maksi($col_382)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20382',number_format(self::maksi($col_365)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20383',number_format(self::maksi($col_366)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20384',number_format(self::maksi($col_16/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20385',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20386',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20393',number_format(self::maksi($col_354)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20394',number_format(self::maksi($col_355)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20395',number_format(self::maksi($col_356)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20396',number_format(self::maksi($col_357)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20397',number_format(self::maksi($col_358)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20398',number_format(self::maksi($col_359)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20399',number_format(self::maksi($col_360)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20400',number_format(self::maksi($col_361)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20401',number_format(self::maksi($col_422)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20402',number_format(self::maksi($col_423)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20403',number_format(self::maksi($col_424)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20404',number_format(self::maksi($col_452)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20405',number_format(self::maksi($col_429)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20406',number_format(self::maksi($col_430)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20407',number_format(self::maksi($col_31)*7,2),($iSekolah+1),sizeof($fetch));
                        //         break;
                        //     case '13':
                        //         self::ke_tigastandar($sekolah_id,'20001',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20004',number_format($col_1100*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20007',number_format(self::maksi($col_157/self::cekbaginol($col_2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20012',number_format($col_659*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20013',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20014',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20015',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20016',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20017',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20023',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20024',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20026',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20027',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20036',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20037',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20039',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20040',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20041',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20042',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20043',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20054',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20055',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20057',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20058',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20059',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20069',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20070',number_format(self::stdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20071',self::statuslahan($col_278),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20072',self::milik($col_279),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20077',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20086',self::statuslahan($col_291),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20087',number_format(self::stdpln($col_51,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20088',number_format(self::stdpln($col_395,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20089',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20090',number_format(self::maksi($col_181/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20091',number_format(self::maksi($col_177/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20092',number_format(self::maksi($col_178/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20094',number_format(self::maksi($col_179/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20095',number_format(self::maksi($col_180/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20096',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20097',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20098',number_format(self::maksi($col_416/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20099',number_format(self::maksi($col_426/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20100',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20101',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20102',number_format(self::maksi($col_182/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20103',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20105',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20106',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20107',number_format(self::maksi($col_12/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20108',number_format(self::maksi($col_168/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20109',number_format(self::maksi($col_169/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20110',number_format(self::maksi($col_170/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20111',number_format(self::maksi($col_171/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20112',number_format(self::maksi($col_172/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20113',number_format(self::maksi($col_158/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20114',number_format(self::maksi($col_159/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20115',number_format(self::maksi($col_160/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20116',number_format(self::maksi($col_161/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20117',number_format(self::maksi($col_162/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20118',number_format(self::maksi($col_163/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20119',number_format(self::maksi($col_164/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20120',number_format(self::maksi($col_165/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20121',number_format(self::maksi($col_166/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20122',number_format(self::maksi($col_167/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20140',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20141',number_format(self::maksi($col_175/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20142',number_format(self::maksi($col_176/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20156',number_format(self::maksi($col_308)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20157',number_format(self::maksi($col_309)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20163',number_format(self::maksi($col_326)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20164',number_format(self::maksi($col_327)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20165',number_format(self::maksi($col_328)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20166',number_format(self::maksi($col_313)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20167',number_format(self::maksi($col_314)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20168',number_format(self::maksi($col_315)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20169',number_format(self::maksi($col_316)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20170',number_format(self::maksi($col_317)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20171',number_format(self::maksi($col_318)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20172',number_format(self::maksi($col_319)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20173',number_format(self::maksi($col_320)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20174',number_format(self::maksi($col_321)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20175',number_format(self::maksi($col_322)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20176',number_format(self::maksi($col_323)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20178',number_format(self::maksi($col_406)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20179',number_format(self::maksi($col_407)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20180',number_format(self::maksi($col_408)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20181',number_format(self::maksi($col_409)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20182',number_format(self::maksi($col_410)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20183',number_format(self::maksi($col_411)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20184',number_format(self::maksi($col_412)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20185',number_format(self::maksi($col_413)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20186',number_format(self::maksi($col_414)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20187',number_format(self::maksi($col_415)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20188',number_format(self::maksi($col_177/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20190',number_format(self::maksi($col_446)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20191',number_format(self::maksi($col_447)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20192',number_format(self::maksi($col_448)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20193',number_format(self::maksi($col_449)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20194',number_format(self::maksi($col_450)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20195',number_format(self::maksi($col_451)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20196',number_format(self::maksi($col_435/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20197',number_format(self::maksi($col_436/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20198',number_format(self::maksi($col_437/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20199',number_format(self::maksi($col_438/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20200',number_format(self::maksi($col_439/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20201',number_format(self::maksi($col_440/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20202',number_format(self::maksi($col_441/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20203',number_format(self::maksi($col_442/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20206',number_format(self::maksi($col_178/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20207',number_format(self::maksi($col_465)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20208',number_format(self::maksi($col_466)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20209',number_format(self::maksi($col_467)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20210',number_format(self::maksi($col_468)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20211',number_format(self::maksi($col_469)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20212',number_format(self::maksi($col_470)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20213',number_format(self::maksi($col_455/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20214',number_format(self::maksi($col_456/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20215',number_format(self::maksi($col_457/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20216',number_format(self::maksi($col_458)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20217',number_format(self::maksi($col_459)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20218',number_format(self::maksi($col_460)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20219',number_format(self::maksi($col_461)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20220',number_format(self::maksi($col_462)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20223',number_format(self::maksi($col_179/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20224',number_format(self::maksi($col_485)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20225',number_format(self::maksi($col_486)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20226',number_format(self::maksi($col_487)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20227',number_format(self::maksi($col_488)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20228',number_format(self::maksi($col_489)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20229',number_format(self::maksi($col_490)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20230',number_format(self::maksi($col_474/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20231',number_format(self::maksi($col_475/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20232',number_format(self::maksi($col_476/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20233',number_format(self::maksi($col_477)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20234',number_format(self::maksi($col_478)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20235',number_format(self::maksi($col_479)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20236',number_format(self::maksi($col_480)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20238',number_format(self::maksi($col_482)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20241',number_format(self::maksi($col_474/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20242',number_format(self::maksi($col_506)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20243',number_format(self::maksi($col_507)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20244',number_format(self::maksi($col_508)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20245',number_format(self::maksi($col_509)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20246',number_format(self::maksi($col_492/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20247',number_format(self::maksi($col_494/8)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20248',number_format(self::maksi($col_506)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20249',number_format(self::maksi($col_497)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20250',number_format(self::maksi($col_498)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20251',number_format(self::maksi($col_499)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20252',number_format(self::maksi($col_500)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20253',number_format(self::maksi($col_501)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20254',number_format(self::maksi($col_502)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20256',number_format(self::maksi($col_504)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20258',number_format(self::maksi($col_181/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20259',number_format(self::maksi($col_520)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20260',number_format(self::maksi($col_521)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20261',number_format(self::maksi($col_522)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20262',number_format(self::maksi($col_523)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20263',number_format(self::maksi($col_511/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20264',number_format(self::maksi($col_513/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20265',number_format(self::maksi($col_515)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20266',number_format(self::maksi($col_516)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20267',number_format(self::maksi($col_517)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20268',number_format(self::maksi($col_518)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20269',number_format(self::maksi($col_519)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20270',number_format(self::maksi($col_17/$col_11)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20271',number_format(self::maksi($col_183/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20272',number_format(self::maksi($col_184/(self::cekbaginol($col_14+$col_15)/2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20282',number_format(self::maksi($col_19)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20284',number_format(self::maksi_s($col_54)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20292',number_format(self::maksi_s($col_432)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20295',number_format(self::maksi_s($col_452)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20298',number_format(self::maksi_s($col_471)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20301',number_format(self::maksi_s($col_491)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20302',number_format(self::maksi($col_493/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20303',number_format(self::maksi($col_495/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20304',number_format(self::maksi_s($col_510)),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20305',number_format(self::maksi($col_512/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20306',number_format(self::maksi_s($col_514)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20307',number_format(self::maksi($col_330/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20308',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20309',number_format(self::maksi($col_336)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20310',number_format(self::maksi($col_337)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20311',number_format(self::maksi($col_338)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20312',number_format(self::maksi($col_339)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20313',number_format(self::maksi($col_982)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20314',number_format(self::maksi($col_1096)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20315',number_format(self::maksi($col_331)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20316',number_format(self::maksi($col_332)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20317',number_format(self::maksi($col_333)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20318',number_format(self::maksi($col_334)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20319',number_format(self::maksi($col_335)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20320',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20321',number_format(self::maksi($col_340/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20322',number_format(self::maksi($col_341/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20323',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20324',number_format(self::maksi($col_343)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20325',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20326',number_format(self::maksi($col_345)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20327',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20328',number_format(self::maksi($col_368)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20329',number_format(self::maksi($col_369)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20330',number_format(self::maksi($col_370)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20331',number_format(self::maksi($col_371)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20332',number_format(self::maksi($col_362)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20333',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20334',number_format(self::maksi($col_364)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20335',number_format(self::stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20336',number_format(self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20337',number_format(self::maksi($col_383)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20338',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20339',number_format(self::maksi($col_390)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20341',number_format(self::maksi($col_392)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20342',number_format(self::maksi($col_393)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20343',number_format(self::maksi($col_394)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20344',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20345',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20346',number_format(self::maksi($col_395/18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20347',number_format(self::maksi($col_397)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20348',number_format(self::maksi($col_398)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20349',number_format(self::maksi($col_1099/self::cekbaginol(0.3*$col_638))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20350',number_format(self::maksi($col_182/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20351',number_format(self::maksi($col_350)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20352',number_format(self::maksi($col_351)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20353',number_format(self::maksi($col_352)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20354',number_format(self::maksi($col_353)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20355',number_format(self::maksi($col_416/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20356',number_format(self::maksi($col_417)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20357',number_format(self::maksi($col_418)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20358',number_format(self::maksi($col_419)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20359',number_format(self::maksi($col_420)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20360',number_format(self::maksi($col_421)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20361',number_format(self::maksi($col_426/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20362',number_format(self::maksi($col_427)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20363',number_format(self::maksi($col_428/4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20364',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20366',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20367',number_format(self::maksi($col_346)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20368',number_format(self::maksi($col_347)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20369',number_format(self::maksi($col_348)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20370',number_format(self::maksi($col_349)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20371',number_format(self::maksi($col_372)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20372',number_format(self::maksi($col_373)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20373',number_format(self::maksi($col_374)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20374',number_format(self::maksi($col_375)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20375',number_format(self::maksi($col_376)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20376',number_format(self::maksi($col_377)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20377',number_format(self::maksi($col_378)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20378',number_format(self::maksi($col_379)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20379',number_format(self::maksi($col_380)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20380',number_format(self::maksi($col_381)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20381',number_format(self::maksi($col_382)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20382',number_format(self::maksi($col_365)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20383',number_format(self::maksi($col_366)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20384',number_format(self::maksi($col_16/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20385',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20386',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20393',number_format(self::maksi($col_354)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20394',number_format(self::maksi($col_355)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20395',number_format(self::maksi($col_356)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20396',number_format(self::maksi($col_357)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20397',number_format(self::maksi($col_358)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20398',number_format(self::maksi($col_359)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20399',number_format(self::maksi($col_360)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20400',number_format(self::maksi($col_361)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20401',number_format(self::maksi($col_422)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20402',number_format(self::maksi($col_423)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20403',number_format(self::maksi($col_424)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20404',number_format(self::maksi($col_452)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20405',number_format(self::maksi($col_429)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20406',number_format(self::maksi($col_430)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20407',number_format(self::maksi($col_31)*7,2),($iSekolah+1),sizeof($fetch));
                        //         break;
                        //     case '15':
                        //         self::ke_tigastandar($sekolah_id,'20001',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20004',number_format($col_1100*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20007',number_format(self::maksi($col_157/self::cekbaginol($col_2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20012',number_format($col_659*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20013',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20014',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20015',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20016',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20017',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20023',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20024',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20026',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20027',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20036',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20037',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20039',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20040',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20041',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20042',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20043',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20054',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20055',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20057',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20058',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20059',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20069',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20070',number_format(self::stdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20071',self::statuslahan($col_278),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20072',self::milik($col_279),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20077',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20086',self::statuslahan($col_291),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20087',number_format(self::stdpln($col_51,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20088',number_format(self::stdpln($col_395,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20089',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20090',number_format(self::maksi($col_181/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20091',number_format(self::maksi($col_177/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20092',number_format(self::maksi($col_178/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20093',number_format(self::maksi($col_174/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20094',number_format(self::maksi($col_179/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20095',number_format(self::maksi($col_180/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20096',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20097',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20098',number_format(self::maksi($col_416/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20099',number_format(self::maksi($col_426/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20100',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20101',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20102',number_format(self::maksi($col_182/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20103',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20105',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20106',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20107',number_format(self::maksi($col_12/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20108',number_format(self::maksi($col_168/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20109',number_format(self::maksi($col_169/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20110',number_format(self::maksi($col_170/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20111',number_format(self::maksi($col_171/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20112',number_format(self::maksi($col_172/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20113',number_format(self::maksi($col_158/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20114',number_format(self::maksi($col_159/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20115',number_format(self::maksi($col_160/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20116',number_format(self::maksi($col_161/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20117',number_format(self::maksi($col_162/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20118',number_format(self::maksi($col_163/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20119',number_format(self::maksi($col_164/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20120',number_format(self::maksi($col_165/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20121',number_format(self::maksi($col_166/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20122',number_format(self::maksi($col_167/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20123',number_format(self::maksi($col_174/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20125',number_format(self::maksi($col_792)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20126',number_format(self::maksi($col_781)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20127',number_format(self::maksi($col_791)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20128',number_format(self::maksi($col_793)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20129',number_format(self::maksi($col_784)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20131',number_format(self::maksi($col_748/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20132',number_format(self::maksi($col_750/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20133',number_format(self::maksi($col_788)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20134',number_format(self::maksi($col_789)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20135',number_format(self::maksi($col_786)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20137',number_format(self::maksi($col_782)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20138',number_format(self::maksi($col_785)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20140',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20141',number_format(self::maksi($col_175/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20142',number_format(self::maksi($col_176/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20156',number_format(self::maksi($col_308)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20157',number_format(self::maksi($col_309)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20163',number_format(self::maksi($col_326)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20164',number_format(self::maksi($col_327)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20165',number_format(self::maksi($col_328)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20166',number_format(self::maksi($col_313)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20167',number_format(self::maksi($col_314)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20168',number_format(self::maksi($col_315)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20169',number_format(self::maksi($col_316)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20170',number_format(self::maksi($col_317)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20171',number_format(self::maksi($col_318)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20172',number_format(self::maksi($col_319)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20173',number_format(self::maksi($col_320)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20174',number_format(self::maksi($col_321)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20175',number_format(self::maksi($col_322)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20176',number_format(self::maksi($col_323)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20178',number_format(self::maksi($col_406)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20179',number_format(self::maksi($col_407)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20180',number_format(self::maksi($col_408)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20181',number_format(self::maksi($col_409)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20182',number_format(self::maksi($col_410)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20183',number_format(self::maksi($col_411)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20184',number_format(self::maksi($col_412)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20185',number_format(self::maksi($col_413)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20186',number_format(self::maksi($col_414)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20187',number_format(self::maksi($col_415)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20188',number_format(self::maksi($col_177/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20190',number_format(self::maksi($col_446)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20191',number_format(self::maksi($col_447)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20192',number_format(self::maksi($col_448)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20193',number_format(self::maksi($col_449)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20194',number_format(self::maksi($col_450)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20195',number_format(self::maksi($col_451)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20196',number_format(self::maksi($col_435/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20197',number_format(self::maksi($col_436/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20198',number_format(self::maksi($col_437/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20199',number_format(self::maksi($col_438/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20200',number_format(self::maksi($col_439/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20201',number_format(self::maksi($col_440/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20202',number_format(self::maksi($col_441/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20203',number_format(self::maksi($col_442/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20206',number_format(self::maksi($col_178/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20207',number_format(self::maksi($col_465)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20208',number_format(self::maksi($col_466)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20209',number_format(self::maksi($col_467)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20210',number_format(self::maksi($col_468)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20211',number_format(self::maksi($col_469)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20212',number_format(self::maksi($col_470)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20213',number_format(self::maksi($col_455/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20214',number_format(self::maksi($col_456/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20215',number_format(self::maksi($col_457/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20216',number_format(self::maksi($col_458)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20217',number_format(self::maksi($col_459)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20218',number_format(self::maksi($col_460)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20219',number_format(self::maksi($col_461)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20220',number_format(self::maksi($col_462)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20223',number_format(self::maksi($col_179/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20224',number_format(self::maksi($col_485)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20225',number_format(self::maksi($col_486)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20226',number_format(self::maksi($col_487)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20227',number_format(self::maksi($col_488)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20228',number_format(self::maksi($col_489)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20229',number_format(self::maksi($col_490)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20230',number_format(self::maksi($col_474/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20231',number_format(self::maksi($col_475/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20232',number_format(self::maksi($col_476/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20233',number_format(self::maksi($col_477)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20234',number_format(self::maksi($col_478)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20235',number_format(self::maksi($col_479)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20236',number_format(self::maksi($col_480)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20238',number_format(self::maksi($col_482)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20241',number_format(self::maksi($col_474/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20242',number_format(self::maksi($col_506)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20243',number_format(self::maksi($col_507)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20244',number_format(self::maksi($col_508)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20245',number_format(self::maksi($col_509)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20246',number_format(self::maksi($col_492/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20247',number_format(self::maksi($col_494/8)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20248',number_format(self::maksi($col_506)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20249',number_format(self::maksi($col_497)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20250',number_format(self::maksi($col_498)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20251',number_format(self::maksi($col_499)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20252',number_format(self::maksi($col_500)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20253',number_format(self::maksi($col_501)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20254',number_format(self::maksi($col_502)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20256',number_format(self::maksi($col_504)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20258',number_format(self::maksi($col_181/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20259',number_format(self::maksi($col_520)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20260',number_format(self::maksi($col_521)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20261',number_format(self::maksi($col_522)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20262',number_format(self::maksi($col_523)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20263',number_format(self::maksi($col_511/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20264',number_format(self::maksi($col_513/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20265',number_format(self::maksi($col_515)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20266',number_format(self::maksi($col_516)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20267',number_format(self::maksi($col_517)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20268',number_format(self::maksi($col_518)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20269',number_format(self::maksi($col_519)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20270',number_format(self::maksi($col_17/$col_11)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20271',number_format(self::maksi($col_183/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20272',number_format(self::maksi($col_184/(self::cekbaginol($col_14+$col_15)/2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20275',number_format(self::maksi_s($col_329)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20276',number_format(self::maksi($col_780)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20277',number_format(self::maksi($col_749/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20278',number_format(self::maksi($col_351/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20279',number_format(self::maksi($col_885/7)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20280',number_format(self::maksi($col_794)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20282',number_format(self::maksi($col_19)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20284',number_format(self::maksi_s($col_54)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20292',number_format(self::maksi_s($col_432)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20295',number_format(self::maksi_s($col_452)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20298',number_format(self::maksi_s($col_471)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20301',number_format(self::maksi_s($col_491)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20302',number_format(self::maksi($col_493/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20303',number_format(self::maksi($col_495/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20304',number_format(self::maksi_s($col_510)),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20305',number_format(self::maksi($col_512/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20306',number_format(self::maksi_s($col_514)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20307',number_format(self::maksi($col_330/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20308',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20309',number_format(self::maksi($col_336)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20310',number_format(self::maksi($col_337)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20311',number_format(self::maksi($col_338)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20312',number_format(self::maksi($col_339)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20313',number_format(self::maksi($col_982)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20314',number_format(self::maksi($col_1096)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20315',number_format(self::maksi($col_331)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20316',number_format(self::maksi($col_332)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20317',number_format(self::maksi($col_333)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20318',number_format(self::maksi($col_334)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20319',number_format(self::maksi($col_335)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20320',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20321',number_format(self::maksi($col_340/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20322',number_format(self::maksi($col_341/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20323',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20324',number_format(self::maksi($col_343)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20325',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20326',number_format(self::maksi($col_345)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20327',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20328',number_format(self::maksi($col_368)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20329',number_format(self::maksi($col_369)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20330',number_format(self::maksi($col_370)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20331',number_format(self::maksi($col_371)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20332',number_format(self::maksi($col_362)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20333',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20334',number_format(self::maksi($col_364)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20335',number_format(self::stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20336',number_format(self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20337',number_format(self::maksi($col_383)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20338',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20339',number_format(self::maksi($col_390)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20341',number_format(self::maksi($col_392)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20342',number_format(self::maksi($col_393)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20343',number_format(self::maksi($col_394)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20344',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20345',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20346',number_format(self::maksi($col_395/18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20347',number_format(self::maksi($col_397)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20348',number_format(self::maksi($col_398)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20349',number_format(self::maksi($col_1099/self::cekbaginol(0.3*$col_638))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20350',number_format(self::maksi($col_182/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20351',number_format(self::maksi($col_350)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20352',number_format(self::maksi($col_351)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20353',number_format(self::maksi($col_352)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20354',number_format(self::maksi($col_353)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20355',number_format(self::maksi($col_416/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20356',number_format(self::maksi($col_417)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20357',number_format(self::maksi($col_418)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20358',number_format(self::maksi($col_419)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20359',number_format(self::maksi($col_420)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20360',number_format(self::maksi($col_421)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20361',number_format(self::maksi($col_426/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20362',number_format(self::maksi($col_427)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20363',number_format(self::maksi($col_428/4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20364',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20366',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20367',number_format(self::maksi($col_346)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20368',number_format(self::maksi($col_347)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20369',number_format(self::maksi($col_348)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20370',number_format(self::maksi($col_349)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20371',number_format(self::maksi($col_372)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20372',number_format(self::maksi($col_373)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20373',number_format(self::maksi($col_374)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20374',number_format(self::maksi($col_375)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20375',number_format(self::maksi($col_376)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20376',number_format(self::maksi($col_377)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20377',number_format(self::maksi($col_378)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20378',number_format(self::maksi($col_379)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20379',number_format(self::maksi($col_380)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20380',number_format(self::maksi($col_381)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20381',number_format(self::maksi($col_382)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20382',number_format(self::maksi($col_365)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20383',number_format(self::maksi($col_366)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20384',number_format(self::maksi($col_16/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20385',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20386',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20393',number_format(self::maksi($col_354)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20394',number_format(self::maksi($col_355)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20395',number_format(self::maksi($col_356)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20396',number_format(self::maksi($col_357)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20397',number_format(self::maksi($col_358)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20398',number_format(self::maksi($col_359)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20399',number_format(self::maksi($col_360)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20400',number_format(self::maksi($col_361)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20401',number_format(self::maksi($col_422)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20402',number_format(self::maksi($col_423)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20403',number_format(self::maksi($col_424)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20404',number_format(self::maksi($col_452)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20405',number_format(self::maksi($col_429)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20406',number_format(self::maksi($col_430)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20407',number_format(self::maksi($col_31)*7,2),($iSekolah+1),sizeof($fetch));
                        //         break;
                        //     default:
                        //         self::ke_tigastandar($sekolah_id,'20001',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20002',number_format(self::maksi($col_42/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20003',number_format(self::maksi($col_43/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20004',number_format($col_1100*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20005',number_format(self::maksi($col_783)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20006',number_format(self::maksi($col_525)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20007',number_format(self::maksi($col_157/self::cekbaginol($col_2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20012',number_format($col_659*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20013',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20014',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20015',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20016',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20017',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20023',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20024',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20026',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20027',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20036',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20037',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20039',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20040',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20041',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20042',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20043',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20054',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20055',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20057',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20058',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20059',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20069',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20070',number_format(self::stdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20071',self::statuslahan($col_278),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20072',self::milik($col_279),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20077',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20086',self::statuslahan($col_291),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20087',number_format(self::stdpln($col_51,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20088',number_format(self::stdpln($col_395,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20089',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20090',number_format(self::maksi($col_181/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20091',number_format(self::maksi($col_177/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20092',number_format(self::maksi($col_178/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20093',number_format(self::maksi($col_174/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20094',number_format(self::maksi($col_179/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20095',number_format(self::maksi($col_180/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20096',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20097',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20098',number_format(self::maksi($col_416/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20099',number_format(self::maksi($col_426/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20100',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20101',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20102',number_format(self::maksi($col_182/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20103',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20105',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20106',number_format(self::maksi($col_11/self::cekbaginol($col_50))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20107',number_format(self::maksi($col_12/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20108',number_format(self::maksi($col_168/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20109',number_format(self::maksi($col_169/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20110',number_format(self::maksi($col_170/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20111',number_format(self::maksi($col_171/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20112',number_format(self::maksi($col_172/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20113',number_format(self::maksi($col_158/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20114',number_format(self::maksi($col_159/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20115',number_format(self::maksi($col_160/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20116',number_format(self::maksi($col_161/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20117',number_format(self::maksi($col_162/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20118',number_format(self::maksi($col_163/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20119',number_format(self::maksi($col_164/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20120',number_format(self::maksi($col_165/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20121',number_format(self::maksi($col_166/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20122',number_format(self::maksi($col_167/self::cekbaginol($col_11))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20123',number_format(self::maksi($col_174/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20124',number_format(self::maksi($col_790)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20125',number_format(self::maksi($col_792)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20126',number_format(self::maksi($col_781)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20127',number_format(self::maksi($col_791)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20128',number_format(self::maksi($col_793)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20129',number_format(self::maksi($col_784)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20131',number_format(self::maksi($col_748/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20132',number_format(self::maksi($col_750/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20133',number_format(self::maksi($col_788)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20134',number_format(self::maksi($col_789)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20135',number_format(self::maksi($col_786)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20136',number_format(self::maksi($col_787)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20137',number_format(self::maksi($col_782)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20138',number_format(self::maksi($col_785)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20140',number_format(self::maksi($col_13/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20141',number_format(self::maksi($col_175/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20142',number_format(self::maksi($col_176/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20156',number_format(self::maksi($col_308)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20157',number_format(self::maksi($col_309)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20163',number_format(self::maksi($col_326)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20164',number_format(self::maksi($col_327)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20165',number_format(self::maksi($col_328)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20166',number_format(self::maksi($col_313)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20167',number_format(self::maksi($col_314)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20168',number_format(self::maksi($col_315)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20169',number_format(self::maksi($col_316)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20170',number_format(self::maksi($col_317)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20171',number_format(self::maksi($col_318)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20172',number_format(self::maksi($col_319)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20173',number_format(self::maksi($col_320)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20174',number_format(self::maksi($col_321)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20175',number_format(self::maksi($col_322)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20176',number_format(self::maksi($col_323)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20178',number_format(self::maksi($col_406)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20179',number_format(self::maksi($col_407)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20180',number_format(self::maksi($col_408)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20181',number_format(self::maksi($col_409)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20182',number_format(self::maksi($col_410)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20183',number_format(self::maksi($col_411)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20184',number_format(self::maksi($col_412)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20185',number_format(self::maksi($col_413)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20186',number_format(self::maksi($col_414)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20187',number_format(self::maksi($col_415)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20188',number_format(self::maksi($col_177/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20190',number_format(self::maksi($col_446)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20191',number_format(self::maksi($col_447)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20192',number_format(self::maksi($col_448)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20193',number_format(self::maksi($col_449)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20194',number_format(self::maksi($col_450)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20195',number_format(self::maksi($col_451)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20196',number_format(self::maksi($col_435/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20197',number_format(self::maksi($col_436/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20198',number_format(self::maksi($col_437/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20199',number_format(self::maksi($col_438/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20200',number_format(self::maksi($col_439/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20201',number_format(self::maksi($col_440/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20202',number_format(self::maksi($col_441/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20203',number_format(self::maksi($col_442/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20206',number_format(self::maksi($col_178/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20207',number_format(self::maksi($col_465)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20208',number_format(self::maksi($col_466)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20209',number_format(self::maksi($col_467)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20210',number_format(self::maksi($col_468)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20211',number_format(self::maksi($col_469)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20212',number_format(self::maksi($col_470)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20213',number_format(self::maksi($col_455/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20214',number_format(self::maksi($col_456/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20215',number_format(self::maksi($col_457/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20216',number_format(self::maksi($col_458)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20217',number_format(self::maksi($col_459)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20218',number_format(self::maksi($col_460)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20219',number_format(self::maksi($col_461)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20220',number_format(self::maksi($col_462)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20223',number_format(self::maksi($col_179/48)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20224',number_format(self::maksi($col_485)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20225',number_format(self::maksi($col_486)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20226',number_format(self::maksi($col_487)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20227',number_format(self::maksi($col_488)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20228',number_format(self::maksi($col_489)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20229',number_format(self::maksi($col_490)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20230',number_format(self::maksi($col_474/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20231',number_format(self::maksi($col_475/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20232',number_format(self::maksi($col_476/(($col_14+$col_15)/self::cekbaginol($col_50)))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20233',number_format(self::maksi($col_477)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20234',number_format(self::maksi($col_478)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20235',number_format(self::maksi($col_479)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20236',number_format(self::maksi($col_480)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20238',number_format(self::maksi($col_482)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20241',number_format(self::maksi($col_474/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20242',number_format(self::maksi($col_506)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20243',number_format(self::maksi($col_507)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20244',number_format(self::maksi($col_508)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20245',number_format(self::maksi($col_509)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20246',number_format(self::maksi($col_492/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20247',number_format(self::maksi($col_494/8)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20248',number_format(self::maksi($col_506)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20249',number_format(self::maksi($col_497)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20250',number_format(self::maksi($col_498)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20251',number_format(self::maksi($col_499)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20252',number_format(self::maksi($col_500)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20253',number_format(self::maksi($col_501)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20254',number_format(self::maksi($col_502)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20256',number_format(self::maksi($col_504)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20258',number_format(self::maksi($col_181/30)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20259',number_format(self::maksi($col_520)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20260',number_format(self::maksi($col_521)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20261',number_format(self::maksi($col_522)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20262',number_format(self::maksi($col_523)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20263',number_format(self::maksi($col_511/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20264',number_format(self::maksi($col_513/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20265',number_format(self::maksi($col_515)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20266',number_format(self::maksi($col_516)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20267',number_format(self::maksi($col_517)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20268',number_format(self::maksi($col_518)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20269',number_format(self::maksi($col_519)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20270',number_format(self::maksi($col_17/$col_11)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20271',number_format(self::maksi($col_183/self::cekbaginol($col_14+$col_15))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20272',number_format(self::maksi($col_184/(self::cekbaginol($col_14+$col_15)/2))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20275',number_format(self::maksi_s($col_329)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20276',number_format(self::maksi($col_780)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20277',number_format(self::maksi($col_749/21)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20278',number_format(self::maksi($col_351/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20279',number_format(self::maksi($col_885/7)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20280',number_format(self::maksi($col_794)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20281',number_format(self::maksi($col_795)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20282',number_format(self::maksi($col_19)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20284',number_format(self::maksi_s($col_54)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20292',number_format(self::maksi_s($col_432)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20295',number_format(self::maksi_s($col_452)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20298',number_format(self::maksi_s($col_471)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20301',number_format(self::maksi_s($col_491)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20302',number_format(self::maksi($col_493/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20303',number_format(self::maksi($col_495/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20304',number_format(self::maksi_s($col_510)),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20305',number_format(self::maksi($col_512/15)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20306',number_format(self::maksi_s($col_514)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20307',number_format(self::maksi($col_330/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20308',number_format(self::maksi($col_1056/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20309',number_format(self::maksi($col_336)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20310',number_format(self::maksi($col_337)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20311',number_format(self::maksi($col_338)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20312',number_format(self::maksi($col_339)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20313',number_format(self::maksi($col_982)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20314',number_format(self::maksi($col_1096)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20315',number_format(self::maksi($col_331)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20316',number_format(self::maksi($col_332)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20317',number_format(self::maksi($col_333)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20318',number_format(self::maksi($col_334)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20319',number_format(self::maksi($col_335)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20320',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20321',number_format(self::maksi($col_340/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20322',number_format(self::maksi($col_341/self::cekbaginol($col_43))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20323',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20324',number_format(self::maksi($col_343)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20325',number_format(self::maksi($col_342)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20326',number_format(self::maksi($col_345)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20327',number_format(self::maksi($col_367/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20328',number_format(self::maksi($col_368)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20329',number_format(self::maksi($col_369)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20330',number_format(self::maksi($col_370)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20331',number_format(self::maksi($col_371)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20332',number_format(self::maksi($col_362)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20333',number_format(self::maksi($col_363/12)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20334',number_format(self::maksi($col_364)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20335',number_format(self::stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20336',number_format(self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20337',number_format(self::maksi($col_383)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20338',number_format(self::maksi($col_384/2)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20339',number_format(self::maksi($col_390)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20341',number_format(self::maksi($col_392)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20342',number_format(self::maksi($col_393)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20343',number_format(self::maksi($col_394)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20344',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20345',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20346',number_format(self::maksi($col_395/18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20347',number_format(self::maksi($col_397)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20348',number_format(self::maksi($col_398)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20349',number_format(self::maksi($col_1099/self::cekbaginol(0.3*$col_638))*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20350',number_format(self::maksi($col_182/16)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20351',number_format(self::maksi($col_350)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20352',number_format(self::maksi($col_351)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20353',number_format(self::maksi($col_352)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20354',number_format(self::maksi($col_353)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20355',number_format(self::maksi($col_416/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20356',number_format(self::maksi($col_417)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20357',number_format(self::maksi($col_418)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20358',number_format(self::maksi($col_419)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20359',number_format(self::maksi($col_420)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20360',number_format(self::maksi($col_421)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20361',number_format(self::maksi($col_426/9)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20362',number_format(self::maksi($col_427)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20363',number_format(self::maksi($col_428/4)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20364',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20366',number_format(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20367',number_format(self::maksi($col_346)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20368',number_format(self::maksi($col_347)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20369',number_format(self::maksi($col_348)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20370',number_format(self::maksi($col_349)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20371',number_format(self::maksi($col_372)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20372',number_format(self::maksi($col_373)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20373',number_format(self::maksi($col_374)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20374',number_format(self::maksi($col_375)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20375',number_format(self::maksi($col_376)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20376',number_format(self::maksi($col_377)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20377',number_format(self::maksi($col_378)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20378',number_format(self::maksi($col_379)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20379',number_format(self::maksi($col_380)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20380',number_format(self::maksi($col_381)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20381',number_format(self::maksi($col_382)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20382',number_format(self::maksi($col_365)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20383',number_format(self::maksi($col_366)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20384',number_format(self::maksi($col_16/3)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20385',number_format(self::maksi($col_52)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20386',number_format(self::stdkloset($col_53)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20393',number_format(self::maksi($col_354)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20394',number_format(self::maksi($col_355)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20395',number_format(self::maksi($col_356)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20396',number_format(self::maksi($col_357)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20397',number_format(self::maksi($col_358)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20398',number_format(self::maksi($col_359)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20399',number_format(self::maksi($col_360)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20400',number_format(self::maksi($col_361)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20401',number_format(self::maksi($col_422)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20402',number_format(self::maksi($col_423)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20403',number_format(self::maksi($col_424)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20404',number_format(self::maksi($col_452)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20405',number_format(self::maksi($col_429)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20406',number_format(self::maksi($col_430)*7,2),($iSekolah+1),sizeof($fetch));
                        //         self::ke_tigastandar($sekolah_id,'20407',number_format(self::maksi($col_31)*7,2),($iSekolah+1),sizeof($fetch));
                        //         break;
                        // }

                            switch ($bentuk_pendidikan_id) {
                            case '5':
                                self::ke_ptksp($sekolah_id,'5.1.1.',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.2.',number_format(max(self::maksi($col_42/$col_50)*7,self::maksi($col_43/$col_50)*7),2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.3.',number_format(((self::maksi($col_525)*7)+(self::maksi($col_783)*7))/2,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.4.',number_format(self::maksi($col_157/$col_2)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.1.',number_format($col_659*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.2.',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.3.',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.4.',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.5.',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.6.',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.1.',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.2.',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.4.',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.5.',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.1.',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.2.',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.4.',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.5.',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.6.',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.7.',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.8.',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.1.',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.2.',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.4.',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.5.',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.6.',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                            
                                self::ke_ptksp($sekolah_id,'6.1.1.',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.2.',number_format(self::stdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.3.',max(self::statuslahan($col_278),self::milik($col_279)),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.4.',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.5.',number_format(max(self::statuslahan($col_291),self::stdpln($col_51,$bentuk_pendidikan_id)*7),2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdpln($col_395,$bentuk_pendidikan_id)*7+self::maksi($col_384/2)*7+self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_11/$col_50)*7+self::maksi($col_174/48)*7+self::maksi($col_13/30)*7+self::maksi($col_1056/12)*7+self::maksi($col_367/12)*7+self::maksi($col_363/12)*7)/9;
                                self::ke_ptksp($sekolah_id,'6.1.6.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_11/$col_50)*7+self::maksi($col_12/30)*7+self::maksi($col_168/$col_11)*7+self::maksi($col_169/$col_11)*7+self::maksi($col_170/$col_11)*7+self::maksi($col_171/$col_11)*7+self::maksi($col_172/$col_11)*7+self::maksi($col_158/$col_11)*7+self::maksi($col_159/(($col_14+$col_15)/$col_50))*7+self::maksi($col_160/$col_11)*7+self::maksi($col_161/(($col_14+$col_15)/$col_50))*7+self::maksi($col_162/$col_11)*7+self::maksi($col_163/$col_11)*7+self::maksi($col_164/$col_11)*7+self::maksi($col_165/$col_11)*7+self::maksi($col_166/$col_11)*7+self::maksi($col_167/$col_11)*7)/17;
                                self::ke_ptksp($sekolah_id,'6.2.1.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_174/48)*7+self::maksi($col_790)*7+self::maksi($col_792)*7+self::maksi($col_748/21)*7+self::maksi($col_750/3)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.2.2.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_13/30)*7+self::maksi($col_175/($col_14+$col_15))*7+self::maksi($col_176/$col_43)*7+self::maksi($col_308)*7+self::maksi($col_309)*7+self::maksi($col_326)*7+self::maksi($col_327)*7+self::maksi($col_328)*7+self::maksi($col_313)*7+self::maksi($col_314)*7+self::maksi($col_315)*7+self::maksi($col_316/10)*7+self::maksi($col_317)*7+self::maksi($col_318)*7+self::maksi($col_319)*7+self::maksi($col_320)*7+self::maksi($col_321)*7+self::maksi($col_322)*7+self::maksi($col_323)*7)/18;
                            
                                self::ke_ptksp($sekolah_id,'6.2.3.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_406)*7+self::maksi($col_407)*7+self::maksi($col_408)*7+self::maksi($col_409)*7+self::maksi($col_410)*7+self::maksi($col_411)*7+self::maksi($col_412)*7+self::maksi($col_413)*7+self::maksi($col_414)*7+self::maksi($col_415)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.2.4.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.2.12.',number_format((self::maksi($col_19)*7+maksi_s($col_54)*7)/2,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_330/3)*7+self::maksi($col_1056/12)*7+self::maksi($col_336)*7+self::maksi($col_338/3)*7+self::maksi($col_339)*7+self::maksi($col_982)*7+self::maksi($col_1096)*7+self::maksi($col_331)*7+self::maksi($col_332)*7+self::maksi($col_333)*7+self::maksi($col_334)*7+self::maksi($col_335)*7)/12;
                                self::ke_ptksp($sekolah_id,'6.3.1.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_340/$col_43)*7+self::maksi($col_341/$col_43)*7+self::maksi($col_342)*7+self::maksi($col_343)*7+self::maksi($col_344)*7+self::maksi($col_345)*7)/7;
                                self::ke_ptksp($sekolah_id,'6.3.2.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_367/12)*7+self::maksi($col_368)*7+self::maksi($col_369)*7+self::maksi($col_370)*7+self::maksi($col_371)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.3.3.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_362)*7+self::maksi($col_363/12)*7+self::maksi($col_364)*7)/3;
                                self::ke_ptksp($sekolah_id,'6.3.4.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7+self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7+self::maksi($col_383)*7+self::maksi($col_384/2)*7+self::maksi($col_390)*7+self::maksi($col_392)*7+self::maksi($col_393)*7+self::maksi($col_394)*7+self::maksi($col_52)*7+self::stdkloset($col_53)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.3.5.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_395/18)*7+self::maksi($col_397)*7+self::maksi($col_398)*7)/3;
                                self::ke_ptksp($sekolah_id,'6.3.6.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                if ($col_638<>0){
                                $hasilnya=(self::maksi($col_1099/(0.3*$col_638))*7);
                                }else{$hasilnya=0;} 
                                self::ke_ptksp($sekolah_id,'6.3.7.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.3.14.',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_346)*7+self::maksi($col_347)*7+self::maksi($col_348)*7+self::maksi($col_349)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.3.15.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_373)*7+self::maksi($col_374)*7+self::maksi($col_375)*7+self::maksi($col_376)*7+self::maksi($col_377)*7+self::maksi($col_378)*7+self::maksi($col_379)*7+self::maksi($col_380)*7+self::maksi($col_381)*7+self::maksi($col_382)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.3.16.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_365)*7+self::maksi($col_366)*7)/2;
                                self::ke_ptksp($sekolah_id,'6.3.17.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                break;
                            case '6':
                                self::ke_ptksp($sekolah_id,'5.1.1.',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.2.',number_format(max(self::maksi($col_42/$col_50)*7,self::maksi($col_43/$col_50)*7),2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.3.',number_format($col_1100*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.4.',number_format(self::maksi($col_157/$col_2)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.2.',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.3.',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.4.',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.5.',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.6.',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.1.',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.2.',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.4.',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.5.',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.1.',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.2.',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.4.',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.5.',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.6.',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.7.',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.8.',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.1.',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.2.',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.4.',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.5.',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.6.',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                            
                                self::ke_ptksp($sekolah_id,'6.1.1.',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.2.',number_format(self::stdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.3.',max(self::statuslahan($col_278),milik($col_279)),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.4.',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.5.',number_format(max(self::statuslahan($col_291),stdpln($col_51,$bentuk_pendidikan_id)*7),2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdpln($col_395,$bentuk_pendidikan_id)*7+self::maksi($col_384/2)*7+self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_11/$col_50)*7+self::maksi($col_174/48)*7+self::maksi($col_13/30)*7+self::maksi($col_1056/12)*7+self::maksi($col_367/12)*7+self::maksi($col_363/12)*7+self::maksi($col_416/9)*7+self::maksi($col_426/9)*7+self::maksi($col_182/16)*7)/12;
                                self::ke_ptksp($sekolah_id,'6.1.6.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_11/$col_50)*7+self::maksi($col_12/30)*7+self::maksi($col_168/$col_11)*7+self::maksi($col_169/$col_11)*7+self::maksi($col_170/$col_11)*7+self::maksi($col_171/$col_11)*7+self::maksi($col_172/$col_11)*7+self::maksi($col_158/$col_11)*7+self::maksi($col_159/(($col_14+$col_15)/$col_50))*7+self::maksi($col_160/$col_11)*7+self::maksi($col_161/(($col_14+$col_15)/$col_50))*7+self::maksi($col_162/$col_11)*7+self::maksi($col_163/$col_11)*7+self::maksi($col_164/$col_11)*7+self::maksi($col_165/$col_11)*7+self::maksi($col_166/$col_11)*7+self::maksi($col_167/$col_11)*7)/17;
                                self::ke_ptksp($sekolah_id,'6.2.1.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_174/48)*7+self::maksi($col_790)*7+self::maksi($col_792)*7+self::maksi($col_748/21)*7+self::maksi($col_750/3)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.2.2.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_13/30)*7+self::maksi($col_175/($col_14+$col_15))*7+self::maksi($col_176/$col_43)*7+self::maksi($col_308)*7+self::maksi($col_309)*7+self::maksi($col_326)*7+self::maksi($col_327)*7+self::maksi($col_328)*7+self::maksi($col_313)*7+self::maksi($col_314)*7+self::maksi($col_315)*7+self::maksi($col_316/10)*7+self::maksi($col_317)*7+self::maksi($col_318)*7+self::maksi($col_319)*7+self::maksi($col_320)*7+self::maksi($col_321)*7+self::maksi($col_322)*7+self::maksi($col_323)*7)/18;
                                self::ke_ptksp($sekolah_id,'6.2.3.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_406)*7+self::maksi($col_407)*7+self::maksi($col_408)*7+self::maksi($col_409)*7+self::maksi($col_410)*7+self::maksi($col_411)*7+self::maksi($col_412)*7+self::maksi($col_413)*7+self::maksi($col_414)*7+self::maksi($col_415)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.2.4.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                            
                                self::ke_ptksp($sekolah_id,'6.2.12.',number_format((self::maksi($col_19)*7+maksi_s($col_54)*7)/2,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_330/3)*7+self::maksi($col_1056/12)*7+self::maksi($col_336)*7+self::maksi($col_338/3)*7+self::maksi($col_339)*7+self::maksi($col_982)*7+self::maksi($col_1096)*7+self::maksi($col_331)*7+self::maksi($col_332)*7+self::maksi($col_333)*7+self::maksi($col_334)*7+self::maksi($col_335)*7)/12;
                                self::ke_ptksp($sekolah_id,'6.3.1.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_340/$col_43)*7+self::maksi($col_341/$col_43)*7+self::maksi($col_342)*7+self::maksi($col_343)*7+self::maksi($col_344)*7+self::maksi($col_345)*7)/7;
                                self::ke_ptksp($sekolah_id,'6.3.2.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_367/12)*7+self::maksi($col_368)*7+self::maksi($col_369)*7+self::maksi($col_370)*7+self::maksi($col_371)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.3.3.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_362)*7+self::maksi($col_363/12)*7+self::maksi($col_364)*7)/3;
                                self::ke_ptksp($sekolah_id,'6.3.4.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7+self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7+self::maksi($col_383)*7+self::maksi($col_384/2)*7+self::maksi($col_390)*7+self::maksi($col_392)*7+self::maksi($col_393)*7+self::maksi($col_394)*7+self::maksi($col_52)*7+self::stdkloset($col_53)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.3.5.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_395/18)*7+self::maksi($col_397)*7+self::maksi($col_398)*7)/3;
                                self::ke_ptksp($sekolah_id,'6.3.6.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                if ($col_638<>0){
                                $hasilnya=(self::maksi($col_1099/(0.3*$col_638))*7);
                                }else{$hasilnya=0;} 
                                self::ke_ptksp($sekolah_id,'6.3.7.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.3.14.',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_346)*7+self::maksi($col_347)*7+self::maksi($col_348)*7+self::maksi($col_349)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.3.15.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_373)*7+self::maksi($col_374)*7+self::maksi($col_375)*7+self::maksi($col_376)*7+self::maksi($col_377)*7+self::maksi($col_378)*7+self::maksi($col_379)*7+self::maksi($col_380)*7+self::maksi($col_381)*7+self::maksi($col_382)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.3.16.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_365)*7+self::maksi($col_366)*7)/2;
                                self::ke_ptksp($sekolah_id,'6.3.17.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                            
                                break;
                            case '13':
                                self::ke_ptksp($sekolah_id,'5.1.1.',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.2.',number_format(max(self::maksi($col_42/$col_50)*7,self::maksi($col_43/$col_50)*7),2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.3.',number_format($col_1100*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.4.',number_format(self::maksi($col_157/$col_2)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.2.',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.3.',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.4.',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.5.',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.6.',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.1.',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.2.',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.4.',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.5.',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.1.',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.2.',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.4.',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.5.',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.6.',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.7.',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.8.',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.1.',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.2.',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.4.',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.5.',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.6.',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                            
                                self::ke_ptksp($sekolah_id,'6.1.1.',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.2.',number_format(self::stdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.3.',max(self::statuslahan($col_278),self::milik($col_279)),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.4.',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.5.',number_format(max(self::statuslahan($col_291),self::stdpln($col_51,$bentuk_pendidikan_id)*7),2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdpln($col_395,$bentuk_pendidikan_id)*7+self::maksi($col_384/2)*7+self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_11/$col_50)*7+self::maksi($col_174/48)*7+self::maksi($col_13/30)*7+self::maksi($col_1056/12)*7+self::maksi($col_367/12)*7+self::maksi($col_363/12)*7+self::maksi($col_416/9)*7+self::maksi($col_426/9)*7+self::maksi($col_182/16)*7)/12;
                                self::ke_ptksp($sekolah_id,'6.1.6.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_11/$col_50)*7+self::maksi($col_12/30)*7+self::maksi($col_168/$col_11)*7+self::maksi($col_169/$col_11)*7+self::maksi($col_170/$col_11)*7+self::maksi($col_171/$col_11)*7+self::maksi($col_172/$col_11)*7+self::maksi($col_158/$col_11)*7+self::maksi($col_159/(($col_14+$col_15)/$col_50))*7+self::maksi($col_160/$col_11)*7+self::maksi($col_161/(($col_14+$col_15)/$col_50))*7+self::maksi($col_162/$col_11)*7+self::maksi($col_163/$col_11)*7+self::maksi($col_164/$col_11)*7+self::maksi($col_165/$col_11)*7+self::maksi($col_166/$col_11)*7+self::maksi($col_167/$col_11)*7)/17;
                                self::ke_ptksp($sekolah_id,'6.2.1.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_174/48)*7+self::maksi($col_790)*7+self::maksi($col_792)*7+self::maksi($col_748/21)*7+self::maksi($col_750/3)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.2.2.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_13/30)*7+self::maksi($col_175/($col_14+$col_15))*7+self::maksi($col_176/$col_43)*7+self::maksi($col_308)*7+self::maksi($col_309)*7+self::maksi($col_326)*7+self::maksi($col_327)*7+self::maksi($col_328)*7+self::maksi($col_313)*7+self::maksi($col_314)*7+self::maksi($col_315)*7+self::maksi($col_316/10)*7+self::maksi($col_317)*7+self::maksi($col_318)*7+self::maksi($col_319)*7+self::maksi($col_320)*7+self::maksi($col_321)*7+self::maksi($col_322)*7+self::maksi($col_323)*7)/18;
                                self::ke_ptksp($sekolah_id,'6.2.3.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_406)*7+self::maksi($col_407)*7+self::maksi($col_408)*7+self::maksi($col_409)*7+self::maksi($col_410)*7+self::maksi($col_411)*7+self::maksi($col_412)*7+self::maksi($col_413)*7+self::maksi($col_414)*7+self::maksi($col_415)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.2.4.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                
                                self::ke_ptksp($sekolah_id,'6.2.12.',number_format((self::maksi($col_19)*7+self::maksi_s($col_54)*7)/2,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_330/3)*7+self::maksi($col_1056/12)*7+self::maksi($col_336)*7+self::maksi($col_338/3)*7+self::maksi($col_339)*7+self::maksi($col_982)*7+self::maksi($col_1096)*7+self::maksi($col_331)*7+self::maksi($col_332)*7+self::maksi($col_333)*7+self::maksi($col_334)*7+self::maksi($col_335)*7)/12;
                                self::ke_ptksp($sekolah_id,'6.3.1.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_340/$col_43)*7+self::maksi($col_341/$col_43)*7+self::maksi($col_342)*7+self::maksi($col_343)*7+self::maksi($col_344)*7+self::maksi($col_345)*7)/7;
                                self::ke_ptksp($sekolah_id,'6.3.2.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_367/12)*7+self::maksi($col_368)*7+self::maksi($col_369)*7+self::maksi($col_370)*7+self::maksi($col_371)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.3.3.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_362)*7+self::maksi($col_363/12)*7+self::maksi($col_364)*7)/3;
                                self::ke_ptksp($sekolah_id,'6.3.4.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7+self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7+self::maksi($col_383)*7+self::maksi($col_384/2)*7+self::maksi($col_390)*7+self::maksi($col_392)*7+self::maksi($col_393)*7+self::maksi($col_394)*7+self::maksi($col_52)*7+stdkloset($col_53)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.3.5.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_395/18)*7+self::maksi($col_397)*7+self::maksi($col_398)*7)/3;
                                self::ke_ptksp($sekolah_id,'6.3.6.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                if ($col_638<>0){
                                $hasilnya=(self::maksi($col_1099/(0.3*$col_638))*7);
                                }else{$hasilnya=0;} 
                                self::ke_ptksp($sekolah_id,'6.3.7.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.3.14.',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_346)*7+self::maksi($col_347)*7+self::maksi($col_348)*7+self::maksi($col_349)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.3.15.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_373)*7+self::maksi($col_374)*7+self::maksi($col_375)*7+self::maksi($col_376)*7+self::maksi($col_377)*7+self::maksi($col_378)*7+self::maksi($col_379)*7+self::maksi($col_380)*7+self::maksi($col_381)*7+self::maksi($col_382)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.3.16.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_365)*7+self::maksi($col_366)*7)/2;
                                self::ke_ptksp($sekolah_id,'6.3.17.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                            
                                break;
                            default:
                                self::ke_ptksp($sekolah_id,'5.1.1.',number_format($col_2*7/$col_43,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.2.',number_format(max(self::maksi($col_42/$col_50)*7,self::maksi($col_43/$col_50)*7),2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.3.',number_format($col_1100*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.1.4.',number_format(self::maksi($col_157/$col_2)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.2.',number_format(self::maksi($col_660/35)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.3.',number_format(self::maksi($col_526/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.4.',number_format($col_37*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.5.',number_format($col_534*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.2.6.',number_format($col_535*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.1.',number_format(self::maksi($col_38)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.2.',number_format(self::maksi($col_39)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.4.',number_format(self::maksi($col_40)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.3.5.',number_format(self::maksi($col_3)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.1.',number_format(self::maksi($col_46)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.2.',number_format(self::maksi($col_47)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.4.',number_format(self::maksi($col_531/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.5.',number_format(self::maksi($col_48)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.6.',number_format(self::maksi($col_5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.7.',number_format(self::maksi($col_49)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.4.8.',number_format(self::maksi($col_6)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.1.',number_format(self::maksi($col_41)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.2.',number_format(self::maksi($col_44)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.4.',number_format(self::maksi($col_529/5)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.5.',number_format(self::maksi($col_45)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'5.5.6.',number_format(self::maksi($col_4)*7,2),($iSekolah+1),sizeof($fetch));
                            
                                self::ke_ptksp($sekolah_id,'6.1.1.',number_format(self::stdrombel($col_50,$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.2.',number_format(vstdrasiolahan($col_638,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.3.',max(self::statuslahan($col_278),self::milik($col_279)),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.4.',number_format(self::stdrasiolahan($luasbangunan,($col_14+$col_15),$bentuk_pendidikan_id)*7,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.1.5.',number_format(max(self::statuslahan($col_291),self::stdpln($col_51,$bentuk_pendidikan_id)*7),2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdpln($col_395,$bentuk_pendidikan_id)*7+self::maksi($col_384/2)*7+self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_11/$col_50)*7+self::maksi($col_174/48)*7+self::maksi($col_13/30)*7+self::maksi($col_1056/12)*7+self::maksi($col_367/12)*7+self::maksi($col_363/12)*7+self::maksi($col_416/9)*7+self::maksi($col_426/9)*7+self::maksi($col_182/16)*7)/12;
                                self::ke_ptksp($sekolah_id,'6.1.6.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_11/$col_50)*7+self::maksi($col_12/30)*7+self::maksi($col_168/$col_11)*7+self::maksi($col_169/$col_11)*7+self::maksi($col_170/$col_11)*7+self::maksi($col_171/$col_11)*7+self::maksi($col_172/$col_11)*7+self::maksi($col_158/$col_11)*7+self::maksi($col_159/(($col_14+$col_15)/$col_50))*7+self::maksi($col_160/$col_11)*7+self::maksi($col_161/(($col_14+$col_15)/$col_50))*7+self::maksi($col_162/$col_11)*7+self::maksi($col_163/$col_11)*7+self::maksi($col_164/$col_11)*7+self::maksi($col_165/$col_11)*7+self::maksi($col_166/$col_11)*7+self::maksi($col_167/$col_11)*7)/17;
                                self::ke_ptksp($sekolah_id,'6.2.1.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_174/48)*7+self::maksi($col_790)*7+self::maksi($col_792)*7+self::maksi($col_748/21)*7+self::maksi($col_750/3)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.2.2.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_13/30)*7+self::maksi($col_175/($col_14+$col_15))*7+self::maksi($col_176/$col_43)*7+self::maksi($col_308)*7+self::maksi($col_309)*7+self::maksi($col_326)*7+self::maksi($col_327)*7+self::maksi($col_328)*7+self::maksi($col_313)*7+self::maksi($col_314)*7+self::maksi($col_315)*7+self::maksi($col_316/10)*7+self::maksi($col_317)*7+self::maksi($col_318)*7+self::maksi($col_319)*7+self::maksi($col_320)*7+self::maksi($col_321)*7+self::maksi($col_322)*7+self::maksi($col_323)*7)/18;
                                self::ke_ptksp($sekolah_id,'6.2.3.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_406)*7+self::maksi($col_407)*7+self::maksi($col_408)*7+self::maksi($col_409)*7+self::maksi($col_410)*7+self::maksi($col_411)*7+self::maksi($col_412)*7+self::maksi($col_413)*7+self::maksi($col_414)*7+self::maksi($col_415)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.2.4.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                
                                self::ke_ptksp($sekolah_id,'6.2.12.',number_format((self::maksi($col_19)*7+self::maksi_s($col_54)*7)/2,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_330/3)*7+self::maksi($col_1056/12)*7+self::maksi($col_336)*7+self::maksi($col_338/3)*7+self::maksi($col_339)*7+self::maksi($col_982)*7+self::maksi($col_1096)*7+self::maksi($col_331)*7+self::maksi($col_332)*7+self::maksi($col_333)*7+self::maksi($col_334)*7+self::maksi($col_335)*7)/12;
                                self::ke_ptksp($sekolah_id,'6.3.1.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_340/$col_43)*7+self::maksi($col_341/$col_43)*7+self::maksi($col_342)*7+self::maksi($col_343)*7+self::maksi($col_344)*7+self::maksi($col_345)*7)/7;
                                self::ke_ptksp($sekolah_id,'6.3.2.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_367/12)*7+self::maksi($col_368)*7+self::maksi($col_369)*7+self::maksi($col_370)*7+self::maksi($col_371)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.3.3.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_362)*7+self::maksi($col_363/12)*7+self::maksi($col_364)*7)/3;
                                self::ke_ptksp($sekolah_id,'6.3.4.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdjamban($col_1060,$col_14, $bentuk_pendidikan_id,1)*7+self::stdjamban($col_1061,$col_15, $bentuk_pendidikan_id,0)*7+self::maksi($col_383)*7+self::maksi($col_384/2)*7+self::maksi($col_390)*7+self::maksi($col_392)*7+self::maksi($col_393)*7+self::maksi($col_394)*7+self::maksi($col_52)*7+self::stdkloset($col_53)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.3.5.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_395/18)*7+self::maksi($col_397)*7+self::maksi($col_398)*7)/3;
                                self::ke_ptksp($sekolah_id,'6.3.6.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                if ($col_638<>0){
                                $hasilnya=(self::maksi($col_1099/(0.3*$col_638))*7);
                                }else{$hasilnya=0;} 
                                self::ke_ptksp($sekolah_id,'6.3.7.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                self::ke_ptksp($sekolah_id,'6.3.14.',number_format(self::maksi($col_18)*7,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::stdruangguru($col_1097,$bentuk_pendidikan_id)*7+self::maksi($col_346)*7+self::maksi($col_347)*7+self::maksi($col_348)*7+self::maksi($col_349)*7)/5;
                                self::ke_ptksp($sekolah_id,'6.3.15.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_373)*7+self::maksi($col_374)*7+self::maksi($col_375)*7+self::maksi($col_376)*7+self::maksi($col_377)*7+self::maksi($col_378)*7+self::maksi($col_379)*7+self::maksi($col_380)*7+self::maksi($col_381)*7+self::maksi($col_382)*7)/10;
                                self::ke_ptksp($sekolah_id,'6.3.16.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                                $hasilnya=(self::maksi($col_365)*7+self::maksi($col_366)*7)/2;
                                self::ke_ptksp($sekolah_id,'6.3.17.',number_format($hasilnya,2),($iSekolah+1),sizeof($fetch));
                            
                                break;
                            
                            }
                        
                        } catch (\Throwable $th) {
                            //throw $th;
                            echo "[INF] [GAGAL]".PHP_EOL;
                        }

                        // // start of ruang kelas rombel
                        // snp060101::index($sekolah_id);
                        // // end of ruang kelas rombel

                        $sql_child = "SELECT
                                        sekolah_id,
                                        concat ( LEFT ( urut, 6 ), '00' ) AS urut,
                                        AVG (
                                        isnull( r19, 0 )) AS r19
                                    FROM
                                        master_pmp 
                                    WHERE
                                        sekolah_id = '".$sekolah_id."' 
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

                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$sekolah_id." MASTER_PMP INDIKATOR [BERHASIL]".PHP_EOL;
                            } catch (\Throwable $th) {
                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$sekolah_id." MASTER_PMP INDIKATOR [GAGAL]".PHP_EOL;
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
                                            sekolah_id = '".$sekolah_id."' 
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

                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$sekolah_id." MASTER_PMP STANDAR [BERHASIL]".PHP_EOL;
                            } catch (\Throwable $th) {
                                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$sekolah_id." MASTER_PMP STANDAR [GAGAL]".PHP_EOL;
                            }
                        }

                    }
                
                }

            }
        }
    }
}
