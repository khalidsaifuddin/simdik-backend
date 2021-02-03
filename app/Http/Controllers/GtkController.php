<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class GtkController extends Controller
{

    public function getGTKJenisPie(Request $request){
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

        $sql = "SELECT
                    'guru_laki' AS keterangan,
                    SUM ( guru_laki ) AS jumlah
                FROM
                    rekap_sekolah WITH (NOLOCK)
                WHERE
                    semester_id = '".$request->input('semester_id')."'
                {$param_kode_wilayah}
                {$param_bentuk}
                {$param_status}
                {$param_keyword}
                AND soft_delete = 0
                UNION
                SELECT
                    'guru_perempuan' AS keterangan,
                    SUM ( guru_perempuan ) AS jumlah
                FROM
                    rekap_sekolah WITH (NOLOCK)
                WHERE
                    semester_id = '".$request->input('semester_id')."'
                {$param_kode_wilayah}
                {$param_bentuk}
                {$param_status}
                {$param_keyword}
                AND soft_delete = 0
                UNION
                SELECT
                    'tendik_laki' AS keterangan,
                    SUM ( pegawai_laki ) AS jumlah
                FROM
                    rekap_sekolah WITH (NOLOCK)
                WHERE
                    semester_id = '".$request->input('semester_id')."'
                {$param_kode_wilayah}
                {$param_bentuk}
                {$param_status}
                {$param_keyword}
                AND soft_delete = 0
                UNION
                SELECT
                    'tendik_perempuan' AS keterangan,
                    SUM ( pegawai_perempuan ) AS jumlah
                FROM
                    rekap_sekolah WITH (NOLOCK)
                WHERE
                    semester_id = '".$request->input('semester_id')."'
                {$param_kode_wilayah}
                {$param_bentuk}
                {$param_status}
                {$param_keyword}
                AND soft_delete = 0";

        // for ($i=1; $i <= 13; $i++) { 
        //     $sql .= "SELECT
        //         'pd_kelas_".$i."' AS keterangan,
        //         SUM ( pd_kelas_".$i." ) AS jumlah
        //     FROM
        //         rekap_sekolah WITH (NOLOCK)
        //     WHERE
        //         semester_id = '".$request->input('semester_id')."'
        //     {$param_kode_wilayah}
        //     {$param_bentuk}
        //     {$param_status}
        //     {$param_keyword}
        //     AND soft_delete = 0
        //     UNION ";
        // }

        // $sql = substr($sql,0,(strlen($sql)-6));
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

    public function getGtkJenisKelamin(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $jenis = $request->input('jenis') ? $request->input('jenis') : 'guru';

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah with(nolock)'))
        ->where('rekap_sekolah.soft_delete','=',0)
        ->where('rekap_sekolah.semester_id','=',20181)
        ;

        $fetch->select(
            'guru_laki',
            'guru_perempuan'
        );

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
                                (int)$records[0]->{"guru_laki"}, 
                                (int)$records[0]->{"guru_perempuan"}
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
                            "jumlah" => (int)$records[0]->{"guru_laki"}
                        ]
                        ,[
                            "keterangan"  => "Perempuan",
                            "jumlah" => (int)$records[0]->{"guru_perempuan"}
                        ]
                    );
                    break;
            }
        }
    }

    public function getGtkKualifikasi(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $jenjang = $request->input('jenjang') ? $request->input('jenjang') : 'SMA';
        $jenis = $request->input('jenis') ? $request->input('jenis') : 'guru';

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah with(nolock)'))
        ->where('rekap_sekolah.soft_delete','=',0)
        ->where('rekap_sekolah.semester_id','=',20181)
        ;

        $fetch->select(
            '*'
        );

        if($sekolah_id){
            $fetch->where('sekolah_id', '=', $sekolah_id);
        }

        $records = $fetch->get();
        // return $records;die;
        
        if($records){  

            $arrDataLaki = array();
            $arrDataPerempuan = array();
            
            $arrayKualifikasi = ['d3','d4','s1','s2','s3'];
            
            switch ($tipe) {
                case 'chart':
                    
                    for ($i=0; $i < sizeof($arrayKualifikasi) ; $i++) { 
                        // return $records;die;
                        array_push($arrDataLaki, ((int)$records[0]->{"guru_".$arrayKualifikasi[$i]."_laki_kependidikan"}+(int)$records[0]->{"guru_".$arrayKualifikasi[$i]."_laki_non_kependidikan"}));
                        array_push($arrDataPerempuan, ((int)$records[0]->{"guru_".$arrayKualifikasi[$i]."_perempuan_kependidikan"}+(int)$records[0]->{"guru_".$arrayKualifikasi[$i]."_perempuan_non_kependidikan"}));
                    }
                
                    $return = array();
                    $return['labels'] = $arrayKualifikasi;
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

                    for ($i=0; $i < sizeof($arrayKualifikasi) ; $i++) { 
                        array_push($arrTempLaki, array(
                            "keterangan" => $arrayKualifikasi[$i],
                            "jumlah" => ((int)$records[0]->{"guru_".$arrayKualifikasi[$i]."_laki_kependidikan"}+(int)$records[0]->{"guru_".$arrayKualifikasi[$i]."_laki_non_kependidikan"})
                        ));
                        array_push($arrTempPerempuan, array(
                            "keterangan" => $arrayKualifikasi[$i],
                            "jumlah" => ((int)$records[0]->{"guru_".$arrayKualifikasi[$i]."_perempuan_kependidikan"}+(int)$records[0]->{"guru_".$arrayKualifikasi[$i]."_perempuan_non_kependidikan"})
                        ));
                    }

                    array_push($return, array("keterangan" => "Laki-laki", "kualifikasi" => $arrTempLaki));
                    array_push($return, array("keterangan" => "Perempuan", "kualifikasi" => $arrTempPerempuan));

                    return $return;
                    break;
            }
        }
    }

    public function getGtkNUPTK(Request $request){
        $tipe = $request->input('tipe') ? $request->input('tipe') : 'tabel';
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $jenjang = $request->input('jenjang') ? $request->input('jenjang') : 'SMA';
        $jenis = $request->input('jenis') ? $request->input('jenis') : 'guru';

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah with(nolock)'))
        ->where('rekap_sekolah.soft_delete','=',0)
        ->where('rekap_sekolah.semester_id','=',20181)
        ;

        $fetch->select(
            '*'
        );

        if($sekolah_id){
            $fetch->where('sekolah_id', '=', $sekolah_id);
        }

        $records = $fetch->get();
        // return $records;die;
        
        if($records){  

            $arrDataLaki = array();
            $arrDataPerempuan = array();
            $arrLabel = array();

            $arrayKualifikasi = ['ada','tidak_ada'];
            
            for ($i=0; $i < sizeof($arrayKualifikasi) ; $i++) { 
                array_push($arrDataLaki, (int)$records[0]->{"guru_nuptk_".$arrayKualifikasi[$i]."_laki"});
                array_push($arrDataPerempuan, (int)$records[0]->{"guru_nuptk_".$arrayKualifikasi[$i]."_perempuan"});
                array_push($arrLabel, str_replace("_", " ", $arrayKualifikasi[$i])." NUPTK");
            }

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

                    $arrayKualifikasi = ['ada','tidak_ada'];
                    
                    for ($i=0; $i < sizeof($arrayKualifikasi) ; $i++) { 
                        array_push($arrTempLaki, array(
                            "keterangan" => str_replace("_", " ", $arrayKualifikasi[$i])." NUPTK",
                            "jumlah" =>(int)$records[0]->{"guru_nuptk_".$arrayKualifikasi[$i]."_laki"}
                        ));
                        array_push($arrTempPerempuan, array(
                            "keterangan" => str_replace("_", " ", $arrayKualifikasi[$i])." NUPTK",
                            "jumlah" =>(int)$records[0]->{"guru_nuptk_".$arrayKualifikasi[$i]."_perempuan"}
                        ));
                        // $arrTempLaki['Kelas '.$i] = (int)$records[0]->{"pd_kelas_".$i."_laki"};
                        // $arrTempPerempuan['Kelas '.$i] = (int)$records[0]->{"pd_kelas_".$i."_perempuan"};
                    }

                    array_push($return, array("keterangan" => "Laki-laki", "nuptk" => $arrTempLaki));
                    array_push($return, array("keterangan" => "Perempuan", "nuptk" => $arrTempPerempuan));

                    return $return;
                    break;
            }
        }
    }
}