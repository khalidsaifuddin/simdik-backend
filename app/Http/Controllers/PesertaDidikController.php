<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PesertaDidikController extends Controller
{

    public function getPesertaDidikJenisKelamin(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah with(nolock)'))
        ->where('rekap_sekolah.soft_delete','=',0)
        ->where('rekap_sekolah.semester_id','=',20182)
        ->select(
            'pd_laki',
            'pd_perempuan'
        )
        ;

        if($sekolah_id){
            $fetch->where('sekolah_id', '=', $sekolah_id);
        }

        $records = $fetch->get();
        // return $records;die;
        
        if($records){  
            switch ($tipe) {
                case 'chart':
                
                    $return = array();
                    $return['labels'] = ['Laki-laki', 'Perempuan'];
                    $return['datasets'] = [
                        [
                            "data" => [
                                (int)$records[0]->{"pd_laki"}, 
                                (int)$records[0]->{"pd_perempuan"}
                            ],
                            "backgroundColor"  => [
                                '#FF6384',
                                '#36A2EB'
                            ],
                            "hoverBackgroundColor"  => [
                                '#FF6384',
                                '#36A2EB'
                            ]
                        ]
                    ];

                    return $return;
                    break;
                    
                default:
                    return array(
                        [
                            "keterangan"  => "Laki-laki",
                            "jumlah" => (int)$records[0]->{"pd_laki"}
                        ]
                        ,[
                            "keterangan"  => "Perempuan",
                            "jumlah" => (int)$records[0]->{"pd_perempuan"}
                        ]
                    );
                    break;
            }
        }
    }

    public function getPesertaDidikTingkatKelasPie(Request $request){
        switch ($request->input('id_level_wilayah')) {
			case "0":
				$params = 'propinsi';
				$mst_kode_wilayah_induk = '';
				$mst_kode_wilayah_induk_group = '';
				$param_kode_wilayah = '';

				$add_induk_propinsi = "null as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "null as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "null as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi";
				$add_group_induk_kabupaten = "";
				$add_group_kode_wilayah_induk_kabupaten = "";
				break;
			case "1":
				$params = 'kabupaten';
				$mst_kode_wilayah_induk = 'mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
				$mst_kode_wilayah_induk_group = 'mst_kode_wilayah_propinsi,';
				$param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

				$add_induk_propinsi = "propinsi as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "null as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi";
				$add_group_induk_kabupaten = "";
				$add_group_kode_wilayah_induk_kabupaten = "";
				break;
			case "2":
				$params = 'kecamatan';
				$mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
				$mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
				$param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

				$add_induk_propinsi = "propinsi as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "kabupaten as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi,";
				$add_group_induk_kabupaten = "kabupaten,";
				$add_group_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten";
				break;
			default:
				break;
        }
        
        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,55,";
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,53,";
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,54,";
                }else{
                    $strBentuk .= $arrBentuk[$iBentuk].",";
                }
            }

            $strBentuk = substr($strBentuk, 0, (strlen($strBentuk)-1));
            $strBentuk .= ")";

            // return $strBentuk;
            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap_sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND ".$params." LIKE '%".$request->input('keyword')."%'";
        }else{
            $param_keyword = "";
        }

        $sql = "";

        for ($i=1; $i <= 13; $i++) { 
            $sql .= "SELECT
                'pd_kelas_".$i."' AS keterangan,
                SUM ( pd_kelas_".$i." ) AS jumlah
            FROM
                rekap_sekolah WITH (NOLOCK)
            WHERE
                semester_id = '".$request->input('semester_id')."'
            {$param_kode_wilayah}
            {$param_bentuk}
            {$param_status}
            {$param_keyword}
            AND soft_delete = 0
            UNION ";
        }

        $sql = substr($sql,0,(strlen($sql)-6));
        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        // $return = array();
        // $return['rows'] = $fetch;
        // $return['total'] = sizeof($fetch);

        // return $fetch;

        $label = array();
        $data = array();

        for ($i=0; $i < sizeof($fetch); $i++) { 
            array_push($label, $fetch[$i]->keterangan);
            array_push($data, $fetch[$i]->jumlah);
        }

        $return = array();
        $return['labels'] = $label;
        $return['datasets'] = [
            [
                "data" => $data,
                "backgroundColor"  => [
                    '#558b2f',
                    '#039be5',
                    '#ff6f00',
                    '#bf360c',
                    '#003f5c',
                    '#2f4b7c',
                    '#665191',
                    '#a05195',
                    '#d45087',
                    '#f95d6a',
                    '#ff7c43',
                    '#ffa600',
                    '#3266D6'
                ],
                "hoverBackgroundColor"  => [
                    '#558b2f',
                    '#039be5',
                    '#ff6f00',
                    '#bf360c',
                    '#003f5c',
                    '#2f4b7c',
                    '#665191',
                    '#a05195',
                    '#d45087',
                    '#f95d6a',
                    '#ff7c43',
                    '#ffa600',
                    '#3266D6'
                ]
            ]
        ];

        return $return;
    }

    public function getPesertaDidikTingkatKelas(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $jenjang = $request->input('jenjang') ? $request->input('jenjang') : 'SMA';

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah with(nolock)'))
        ->where('rekap_sekolah.soft_delete','=',0)
        ->where('rekap_sekolah.semester_id','=',20182)
        ;

        $fetch->select(
            '*'
        );

        switch ($jenjang) {
            case 'SMA':
                $labels = ['Kelas 10', 'Kelas 11', 'Kelas 12'];
                $kelas_awal = 10;
                $kelas_akhir = 12;
                break;
            case 'SMP':
                $labels = ['Kelas 7', 'Kelas 8', 'Kelas 9'];
                $kelas_awal = 7;
                $kelas_akhir = 9;
                break;
            case 'SD':
                $labels = ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'];
                $kelas_awal = 1;
                $kelas_akhir = 6;
                break;
            default:
                $labels = ['Kelas 10', 'Kelas 11', 'Kelas 12'];
                $kelas_awal = 10;
                $kelas_akhir = 12;
                break;
        }

        if($sekolah_id){
            $fetch->where('sekolah_id', '=', $sekolah_id);
        }

        $records = $fetch->get();
        // return $records;die;
        
        if($records){  

            $arrDataLaki = array();
            $arrDataPerempuan = array();
    
            
            switch ($tipe) {
                case 'chart':
                    for ($i=$kelas_awal; $i <= $kelas_akhir ; $i++) { 
                        array_push($arrDataLaki, (int)$records[0]->{"pd_kelas_".$i."_laki"});
                        array_push($arrDataPerempuan, (int)$records[0]->{"pd_kelas_".$i."_perempuan"});
                    }
                
                    $return = array();
                    $return['labels'] = $labels;
                    $return['datasets'] = [
                        [
                            "data" => $arrDataLaki,
                            "label"  => "Laki-laki",
                            "backgroundColor"  => "rgba(255,99,132,0.2)",
                            "borderColor"  => "rgba(255,99,132,1)",
                            "borderWidth" => 1,
                            "hoverBackgroundColor"  => "rgba(255,99,132,0.2)",
                            "hoverBorderColor"  => "rgba(255,99,132,1)",
                        ]
                        ,[
                            "data" => $arrDataPerempuan,
                            "label"  => "Perempuan",
                            "backgroundColor"  => "rgba(0,99,132,0.2)",
                            "borderColor"  => "rgba(0,99,132,1)",
                            "borderWidth" => 1,
                            "hoverBackgroundColor"  => "rgba(0,99,132,0.2)",
                            "hoverBorderColor"  => "rgba(0,99,132,1)",
                        ]
                    ];

                    return $return;
                    break;
                    
                default:
                    
                    $return = array();
                    $arrTempLaki = array();
                    $arrTempPerempuan = array();

                    for ($i=$kelas_awal; $i <= $kelas_akhir ; $i++) { 
                        array_push($arrTempLaki, array(
                            "keterangan" => 'Kelas '.$i,
                            "jumlah" =>(int)$records[0]->{"pd_kelas_".$i."_laki"}
                        ));
                        array_push($arrTempPerempuan, array(
                            "keterangan" => 'Kelas '.$i,
                            "jumlah" =>(int)$records[0]->{"pd_kelas_".$i."_perempuan"}
                        ));
                        // $arrTempLaki['Kelas '.$i] = (int)$records[0]->{"pd_kelas_".$i."_laki"};
                        // $arrTempPerempuan['Kelas '.$i] = (int)$records[0]->{"pd_kelas_".$i."_perempuan"};
                    }

                    array_push($return, array("keterangan" => "Laki-laki", "kelas" => $arrTempLaki));
                    array_push($return, array("keterangan" => "Perempuan", "kelas" => $arrTempPerempuan));

                    // array_push($return, $arrDataLaki);
                    // array_push($return, $arrDataPerempuan);

                    return $return;

                    break;
            }
        }
    }

    public function getPesertaDidikUsia(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $jenjang = $request->input('jenjang') ? $request->input('jenjang') : 'SMA';

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah with(nolock)'))
        ->where('rekap_sekolah.soft_delete','=',0)
        ->where('rekap_sekolah.semester_id','=',20182)
        ;

        $fetch->select(
            '*'
        );

        switch ($jenjang) {
            case 'SMA':
                
                // $labels = ['Kelas 10', 'Kelas 11', 'Kelas 12'];
                $umur_awal = 16;
                $umur_akhir = 18;
                break;
            case 'SMP':
                
                // $labels = ['Kelas 7', 'Kelas 8', 'Kelas 9'];
                $umur_awal = 13;
                $umur_akhir = 15;
                break;
            case 'SD':
                
                // $labels = ['Kelas 1', 'Kelas 2', 'Kelas 3', 'Kelas 4', 'Kelas 5', 'Kelas 6'];
                $umur_awal = 7;
                $umur_akhir = 12;
                break;
            default:
                
                // $labels = ['Kelas 10', 'Kelas 11', 'Kelas 12'];
                $umur_awal = 16;
                $umur_akhir = 18;
                break;
        }

        if($sekolah_id){
            $fetch->where('sekolah_id', '=', $sekolah_id);
        }

        $records = $fetch->get();
        // return $records;die;
        
        if($records){  

            $arrDataLaki = array();
            $arrDataPerempuan = array();
            $arrLabel = array();
            
            $kurangLaki = 0;
            $kurangPerempuan = 0;

            for ($i=0; $i < $umur_awal ; $i++) { 
                $kurangLaki = $kurangLaki+(int)$records[0]->{"pd_laki_".$i."_tahun"};
                $kurangPerempuan = $kurangPerempuan+(int)$records[0]->{"pd_perempuan_".$i."_tahun"};
            }

            array_push($arrDataLaki, (int)$kurangLaki);
            array_push($arrDataPerempuan, (int)$kurangPerempuan);
            array_push($arrLabel, "< ".$umur_awal." Tahun");
            
            for ($i=$umur_awal; $i <= $umur_akhir ; $i++) { 
                array_push($arrDataLaki, (int)$records[0]->{"pd_laki_".$i."_tahun"});
                array_push($arrDataPerempuan, (int)$records[0]->{"pd_perempuan_".$i."_tahun"});
                array_push($arrLabel, $i." Tahun");
            }

            $lebihLaki = 0;
            $lebihPerempuan = 0;

            for ($i>$umur_akhir; $i <= 20; $i++) { 
                $lebihLaki = $lebihLaki+(int)$records[0]->{"pd_laki_".$i."_tahun"};
                $lebihPerempuan = $lebihPerempuan+(int)$records[0]->{"pd_perempuan_".$i."_tahun"};
            }

            array_push($arrDataLaki, (int)$lebihLaki);
            array_push($arrDataPerempuan, (int)$lebihPerempuan);
            array_push($arrLabel, "> ".$umur_akhir." Tahun");

            switch ($tipe) {
                case 'chart':
                
                    $return = array();
                    $return['labels'] = $arrLabel;
                    $return['datasets'] = [
                        [
                            "data" => $arrDataLaki,
                            "label"  => "Laki-laki",
                            "backgroundColor"  => "rgba(255,99,132,0.2)",
                            "borderColor"  => "rgba(255,99,132,1)",
                            "borderWidth" => 1,
                            "hoverBackgroundColor"  => "rgba(255,99,132,0.2)",
                            "hoverBorderColor"  => "rgba(255,99,132,1)",
                        ]
                        ,[
                            "data" => $arrDataPerempuan,
                            "label"  => "Perempuan",
                            "backgroundColor"  => "rgba(0,99,132,0.2)",
                            "borderColor"  => "rgba(0,99,132,1)",
                            "borderWidth" => 1,
                            "hoverBackgroundColor"  => "rgba(0,99,132,0.2)",
                            "hoverBorderColor"  => "rgba(0,99,132,1)",
                        ]
                    ];

                    return $return;
                    break;
                    
                default:
                    $return = array();
                    $arrTempLaki = array();
                    $arrTempPerempuan = array();

                    $kurangLaki = 0;
                    $kurangPerempuan = 0;

                    for ($i=0; $i < $umur_awal ; $i++) { 
                        $kurangLaki = $kurangLaki+(int)$records[0]->{"pd_laki_".$i."_tahun"};
                        $kurangPerempuan = $kurangPerempuan+(int)$records[0]->{"pd_perempuan_".$i."_tahun"};
                    }

                    array_push($arrTempLaki, array(
                        "keterangan" => "< ".$umur_awal."  Tahun",
                        "jumlah" => $kurangLaki
                    ));
                    array_push($arrTempPerempuan, array(
                        "keterangan" => "< ".$umur_awal."  Tahun",
                        "jumlah" => $kurangPerempuan
                    ));
                    
                    for ($i=$umur_awal; $i <= $umur_akhir ; $i++) { 
                        array_push($arrTempLaki, array(
                            "keterangan" => $i."  Tahun",
                            "jumlah" =>(int)$records[0]->{"pd_laki_".$i."_tahun"}
                        ));
                        array_push($arrTempPerempuan, array(
                            "keterangan" => $i." Tahun",
                            "jumlah" =>(int)$records[0]->{"pd_laki_".$i."_tahun"}
                        ));
                        // $arrTempLaki['Kelas '.$i] = (int)$records[0]->{"pd_kelas_".$i."_laki"};
                        // $arrTempPerempuan['Kelas '.$i] = (int)$records[0]->{"pd_kelas_".$i."_perempuan"};
                    }

                    $lebihLaki = 0;
                    $lebihPerempuan = 0;

                    for ($i>$umur_akhir; $i <= 20 ; $i++) { 
                        $lebihLaki = $lebihLaki+(int)$records[0]->{"pd_laki_".$i."_tahun"};
                        $lebihPerempuan = $lebihPerempuan+(int)$records[0]->{"pd_perempuan_".$i."_tahun"};
                    }

                    array_push($arrTempLaki, array(
                        "keterangan" => "> ".$umur_akhir."  Tahun",
                        "jumlah" => $lebihLaki
                    ));
                    array_push($arrTempPerempuan, array(
                        "keterangan" => "> ".$umur_akhir."  Tahun",
                        "jumlah" => $lebihPerempuan
                    ));

                    array_push($return, array("keterangan" => "Laki-laki", "usia" => $arrTempLaki));
                    array_push($return, array("keterangan" => "Perempuan", "usia" => $arrTempPerempuan));
                    return $return;
                    break;
            }
        }
    }
}