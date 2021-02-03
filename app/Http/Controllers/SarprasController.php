<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SarprasController extends Controller{

    static function getSarprasKebutuhanRkbWilayah(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $jenis_prasarana = $request->input('jenis_prasarana') ? $request->input('jenis_prasarana') : 'r_kelas';
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;

        $data = self::getRekapSekolahSarprasWilayah($request);

        switch ($tipe) {
            case 'chart':

                $arrLabel = array();
                $arrData = array();

                for ($i=0; $i < sizeof($data['rows']); $i++) { 
                    array_push($arrLabel, $data['rows'][$i]->{'nama'});
                    array_push($arrData, (int)$data['rows'][$i]->{'sekolah_butuh_rkb'});
                }

                $return = array();
                $return['labels'] = $arrLabel;
                $return['datasets'] = [
                    [
                        "data" => $arrData,
                        "label"  => "Jenis Prasarana",
                        "backgroundColor"  => "rgba(8, 134, 140, 0.2)",
                        "borderColor"  => "rgba(8, 134, 140, 1)",
                        "borderWidth" => 1,
                        "hoverBackgroundColor"  => "rgba(8, 134, 140, 0.2)",
                        "hoverBorderColor"  => "rgba(8, 134, 140, 1)",
                    ]
                ];

                return $return;
                break;
            
            default:
                return $data['rows'];
                break;
        }

        // return $data;
    }

    static function getRekapSekolahSarpras(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $skip = $request->input('skip') ? $request->input('skip') : null;
        $take = $request->input('take') ? $request->input('take') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah_sarpras with(nolock)'))
        ->join(DB::raw('rekap_sekolah with(nolock)'), function ($join){
            $join->on('rekap_sekolah_sarpras.sekolah_id', '=', 'rekap_sekolah.sekolah_id')
            ->on('rekap_sekolah_sarpras.semester_id', '=', 'rekap_sekolah.semester_id');
        })
        // ->where('rekap_sekolah_sarpras.soft_delete','=',0)
        ->where('rekap_sekolah_sarpras.semester_id','=',20182)
        ;

        if($sekolah_id){
            $fetch->where('rekap_sekolah_sarpras.sekolah_id', '=', $sekolah_id);
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,55,";
                    array_push($arrBentuk,'55');
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,53,";
                    array_push($arrBentuk,'53');
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,54,";
                    array_push($arrBentuk,'54');
                }else{
                    $strBentuk .= $arrBentuk[$iBentuk].",";
                }
            }

            $strBentuk = substr($strBentuk, 0, (strlen($strBentuk)-1));
            $strBentuk .= ")";

            $fetch->whereIn('rekap_sekolah.bentuk_pendidikan_id',$arrBentuk);

            // return $strBentuk;
            // $param_bentuk = "AND rekap.rekap_rapor_dapodik_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            // $param_bentuk = "";
        }


        $return = array();
        $return['total'] = $fetch->select(DB::raw('sum(1) as total'))->first()->{'total'};
        $return['rows'] = $fetch->select('rekap_sekolah_sarpras.*')->skip($skip)->take($take)->get();

        return $return;
        // return $records;
    }

    static function getRekapSekolahSarprasWilayah(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $id_level_wilayah = $request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;

        switch ($id_level_wilayah) {
            case 2:
                $kolom_kode_wilayah = 'kode_wilayah_kecamatan';
                $kolom_nama = 'kecamatan';
                $kolom_id_level_wilayah = 'id_level_wilayah_kecamatan';
                $kolom_induk_kode_wilayah = 'kode_wilayah_kabupaten';
                $param_kode_wilayah = 'kode_wilayah_kabupaten';
                break;
            case 0:
                $kolom_kode_wilayah = 'kode_wilayah_propinsi';
                $kolom_nama = 'propinsi';
                $kolom_id_level_wilayah = 'id_level_wilayah_propinsi';
                $kolom_induk_kode_wilayah = 'kode_wilayah_propinsi';
                $param_kode_wilayah = 'kode_wilayah_propinsi';
                break;
            default:
                # code...
                break;
        }

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah_sarpras with(nolock)'))
        ->join(DB::raw('rekap_sekolah with(nolock)'), function ($join){
            $join->on('rekap_sekolah_sarpras.sekolah_id', '=', 'rekap_sekolah.sekolah_id')
                ->on('rekap_sekolah_sarpras.semester_id', '=', 'rekap_sekolah.semester_id');
        })
        ->where('rekap_sekolah_sarpras.semester_id','=',20182)
        ->select(
            DB::raw('rekap_sekolah.'.$kolom_kode_wilayah.' as kode_wilayah'),
            DB::raw('rekap_sekolah.'.$kolom_nama.' as nama'),
            DB::raw('rekap_sekolah.'.$kolom_id_level_wilayah.' as id_level_wilayah'),
            DB::raw('rekap_sekolah.'.$kolom_induk_kode_wilayah.' as induk_kode_wilayah'),
            DB::raw('sum(rekap_sekolah_sarpras.r_guru) as r_guru'),
            DB::raw('sum(rekap_sekolah_sarpras.r_kepsek) as r_kepsek'),
            DB::raw('sum(rekap_sekolah_sarpras.perpustakaan) as perpustakaan'),
            DB::raw('sum(rekap_sekolah_sarpras.lab_ipa) as lab_ipa'),
            DB::raw('sum(rekap_sekolah_sarpras.lab_fisika) as lab_fisika'),
            DB::raw('sum(rekap_sekolah_sarpras.lab_kimia) as lab_kimia'),
            DB::raw('sum(rekap_sekolah_sarpras.lab_biologi) as lab_biologi'),
            DB::raw('sum(rekap_sekolah_sarpras.lab_komputer) as lab_komputer'),
            DB::raw('sum(rekap_sekolah_sarpras.r_kelas) as r_kelas'),
            DB::raw('sum(rekap_sekolah_sarpras.r_kelas_baik) as r_kelas_baik'),
            DB::raw('sum(rekap_sekolah_sarpras.r_kelas_rusak_ringan) as r_kelas_rusak_ringan'),
            DB::raw('sum(rekap_sekolah_sarpras.r_kelas_rusak_sedang) as r_kelas_rusak_sedang'),
            DB::raw('sum(rekap_sekolah_sarpras.r_kelas_rusak_berat) as r_kelas_rusak_berat'),
            DB::raw('sum(rekap_sekolah_sarpras.perpustakaan) as perpustakaan'),
            DB::raw('sum(rekap_sekolah_sarpras.perpustakaan_baik) as perpustakaan_baik'),
            DB::raw('sum(rekap_sekolah_sarpras.perpustakaan_rusak_ringan) as perpustakaan_rusak_ringan'),
            DB::raw('sum(rekap_sekolah_sarpras.perpustakaan_rusak_sedang) as perpustakaan_rusak_sedang'),
            DB::raw('sum(rekap_sekolah_sarpras.perpustakaan_rusak_berat) as perpustakaan_rusak_berat'),
            DB::raw('sum( 1 ) as sekolah'),
            DB::raw('sum( case when (rekap_sekolah.rombel - rekap_sekolah_sarpras.r_kelas) > 0 then 1 else 0 end  ) as sekolah_butuh_rkb'),
            DB::raw('sum( case when (rekap_sekolah.rombel - rekap_sekolah_sarpras.r_kelas) > 0 then (rekap_sekolah.rombel - rekap_sekolah_sarpras.r_kelas) else 0 end  ) as kebutuhan_rkb')
        )
        ->groupBy('rekap_sekolah.'.$kolom_kode_wilayah)
        ->groupBy('rekap_sekolah.'.$kolom_nama)
        ->groupBy('rekap_sekolah.'.$kolom_id_level_wilayah)
        ->groupBy('rekap_sekolah.'.$kolom_induk_kode_wilayah)
        ;

        if($kode_wilayah && $id_level_wilayah != 0){
            $fetch->where('rekap_sekolah.'.$param_kode_wilayah, '=', $kode_wilayah);
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,55,";
                    array_push($arrBentuk,'55');
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,53,";
                    array_push($arrBentuk,'53');
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,54,";
                    array_push($arrBentuk,'54');
                }else{
                    $strBentuk .= $arrBentuk[$iBentuk].",";
                }
            }

            $strBentuk = substr($strBentuk, 0, (strlen($strBentuk)-1));
            $strBentuk .= ")";

            // return "oke";die;

            $fetch->whereIn('rekap_sekolah.bentuk_pendidikan_id',$arrBentuk);

            // return $strBentuk;
            // $param_bentuk = "AND rekap.rekap_rapor_dapodik_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            // $param_bentuk = "";
        }

        // return $fetch->toSql();die;

        $records = $fetch->get();
        // return $records;die;

        $return = array();
        $return['rows'] = $records;
        $return['total'] = sizeof($records);

        return $return;
    }

    public function getSarprasJenisWilayah(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $jenis_prasarana = $request->input('jenis_prasarana') ? $request->input('jenis_prasarana') : 'r_kelas';
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;

        $data = self::getRekapSekolahSarprasWilayah($request);

        $r_kelas = 0;
        $r_guru = 0;
        $r_kepsek = 0;
        $perpustakaan = 0;
        $lab_ipa = 0;
        $lab_fisika = 0;
        $lab_kimia = 0;
        $lab_biologi = 0;
        $lab_komputer = 0;

        for ($i=0; $i < sizeof($data['rows']); $i++) { 
            $r_kelas += (int)$data['rows'][$i]->{'r_kelas'};
            $r_guru += (int)$data['rows'][$i]->{'r_guru'};
            $r_kepsek += (int)$data['rows'][$i]->{'r_kepsek'};
            $perpustakaan += (int)$data['rows'][$i]->{'perpustakaan'};
            $lab_ipa += (int)$data['rows'][$i]->{'lab_ipa'};
            $lab_fisika += (int)$data['rows'][$i]->{'lab_fisika'};
            $lab_kimia += (int)$data['rows'][$i]->{'lab_kimia'};
            $lab_biologi += (int)$data['rows'][$i]->{'lab_biologi'};
            $lab_komputer += (int)$data['rows'][$i]->{'lab_komputer'};
        }

        switch ($tipe) {
            case 'chart':
                $return = array();
                $return['labels'] = [
                    // 'R.Kelas', 
                    'R.Guru', 
                    'R.Kepsek', 
                    'Perpustakaan',
                    'Lab IPA',
                    'Lab Fisika',
                    'Lab Kimia',
                    'Lab Biologi',
                    'Lab Komputer',
                ];
                $return['datasets'] = [
                    [
                        "data" => [
                            // $r_kelas, 
                            $r_guru, 
                            $r_kepsek,
                            $perpustakaan,
                            $lab_ipa,
                            $lab_fisika,
                            $lab_kimia,
                            $lab_biologi,
                            $lab_komputer
                        ],
                        "label"  => "Jenis Prasarana",
                        "backgroundColor"  => "rgba(255,99,132,0.2)",
                        "borderColor"  => "rgba(255,99,132,1)",
                        "borderWidth" => 1,
                        "hoverBackgroundColor"  => "rgba(255,99,132,0.2)",
                        "hoverBorderColor"  => "rgba(255,99,132,1)",
                    ]
                ];

                return $return;
                break;
            
            default:
                return array(
                    // [
                    //     "keterangan"  => "R.Kelas",
                    //     "jumlah" => (int)$r_kelas
                    // ],
                    [
                        "keterangan"  => "R.Guru",
                        "jumlah" => (int)$r_guru
                    ],
                    [
                        "keterangan"  => "R.Kepsek",
                        "jumlah" => (int)$r_kepsek
                    ],
                    [
                        "keterangan"  => "Perpustakaan",
                        "jumlah" => (int)$perpustakaan
                    ],
                    [
                        "keterangan"  => "Lab IPA",
                        "jumlah" => (int)$lab_ipa
                    ],
                    [
                        "keterangan"  => "Lab Fisika",
                        "jumlah" => (int)$lab_fisika
                    ],
                    [
                        "keterangan"  => "Lab Kimia",
                        "jumlah" => (int)$lab_kimia
                    ],
                    [
                        "keterangan"  => "Lab Biologi",
                        "jumlah" => (int)$lab_biologi
                    ],
                    [
                        "keterangan"  => "Lab Komputer",
                        "jumlah" => (int)$lab_komputer
                    ]
                );
                break;
        }
    }

    public function getSarprasKerusakanWilayah(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $jenis_prasarana = $request->input('jenis_prasarana') ? $request->input('jenis_prasarana') : 'r_kelas';
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;

        $data = self::getRekapSekolahSarprasWilayah($request);

        $baik = 0;
        $rusak_ringan = 0;
        $rusak_sedang = 0;
        $rusak_berat = 0;

        for ($i=0; $i < sizeof($data['rows']); $i++) { 
            $baik += (int)$data['rows'][$i]->{$jenis_prasarana.'_baik'};
            $rusak_ringan += (int)$data['rows'][$i]->{$jenis_prasarana.'_rusak_ringan'};
            $rusak_sedang += (int)$data['rows'][$i]->{$jenis_prasarana.'_rusak_sedang'};
            $rusak_berat += (int)$data['rows'][$i]->{$jenis_prasarana.'_rusak_berat'};
        }

        switch ($tipe) {
            case 'chart':

                $return = array();
                $return['labels'] = [$jenis_prasarana.'_baik', $jenis_prasarana.'_rusak_ringan', $jenis_prasarana.'_rusak_sedang', $jenis_prasarana.'_rusak_berat'];
                $return['datasets'] = [
                    [
                        "data" => [
                            (int)$baik, 
                            (int)$rusak_ringan,
                            (int)$rusak_sedang,
                            (int)$rusak_berat
                        ],
                        "backgroundColor"  => [
                            '#558b2f',
                            '#039be5',
                            '#ff6f00',
                            '#bf360c'
                        ],
                        "hoverBackgroundColor"  => [
                            '#558b2f',
                            '#039be5',
                            '#ff6f00',
                            '#bf360c'
                        ]
                    ]
                ];

                return $return;
                break;
            
            default:
                return array(
                    [
                        "keterangan"  => str_replace("_"," ",$jenis_prasarana)." Baik",
                        "jumlah" => (int)$baik
                    ]
                    ,[
                        "keterangan"  => str_replace("_"," ",$jenis_prasarana)." Rusak Ringan",
                        "jumlah" => (int)$rusak_ringan
                    ]
                    ,[
                        "keterangan"  => str_replace("_"," ",$jenis_prasarana)." Rusak Sedang",
                        "jumlah" => (int)$rusak_sedang
                    ]
                    ,[
                        "keterangan"  => str_replace("_"," ",$jenis_prasarana)." Rusak Berat",
                        "jumlah" => (int)$rusak_berat
                    ]
                );
                break;
        }

        // return $data['rows'];
        // for ($i=0; $i < sizeof($data['rows']); $i++) { 
           
        // }

    }

    
}

?>