<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SekolahController extends Controller
{
    static function init(){
        return true;
    }

    public function tes(Request $request){
        return "oke";
    }

    static function cekKoreg(Request $request){
        $npsn = $request->input('npsn') ? $request->input('npsn') : null;
        $koreg = $request->input('koreg') ? $request->input('koreg') : null;

        if($npsn){
            $fetch = DB::connection('sqlsrv')
            ->table(DB::raw('sekolah with(nolock)'))
            ->where('sekolah.soft_delete','=',0)
            ->where('sekolah.npsn','=',$npsn)
            ->get();

            $koreg_db = base64_encode($fetch[0]->kode_registrasi);

            return response([ 'koreg' => $koreg_db, 'rows' => $fetch, 'count' => sizeof($fetch) ], 200);

        }else{
            return response([ 'rows' => array(), 'count' => 0 ], 201);
        }
    }

    static function getCountSekolah(Request $request){
        $keyword = $request->input('keyword') ? $request->input('keyword') : null;
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $id_level_wilayah = $request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : null;
        $kecamatan = $request->input('kecamatan') ? $request->input('kecamatan') : null;
        $kabupaten = $request->input('kabupaten') ? $request->input('kabupaten') : null;
        $propinsi = $request->input('propinsi') ? $request->input('propinsi') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;
        $status_sekolah = $request->input('status_sekolah') ? $request->input('status_sekolah') : null;
        $start = $request->input('start') ? $request->input('start') : 0;
        $limit = $request->input('limit') ? $request->input('limit') : 20;
        $rapor = $request->input('rapor') ? $request->input('rapor') : 'yes';

        $fetch = DB::connection('sqlsrv')
        ->table(DB::raw('sekolah with(nolock)'))
        ->select(
            DB::raw("ISNULL(SUM(1),0) as total")
        )
        ->leftJoin(DB::raw('ref.mst_wilayah AS kec with(nolock)'), DB::raw('left(sekolah.kode_wilayah,6)'), '=', 'kec.kode_wilayah')
        ->leftJoin(DB::raw('ref.mst_wilayah AS kab with(nolock)'), 'kec.mst_kode_wilayah', '=', 'kab.kode_wilayah')
        ->leftJoin(DB::raw('ref.mst_wilayah AS prop with(nolock)'), 'kab.mst_kode_wilayah', '=', 'prop.kode_wilayah')
        ->leftJoin(DB::raw('ref.bentuk_pendidikan AS bp with(nolock)'), 'bp.bentuk_pendidikan_id', '=', 'sekolah.bentuk_pendidikan_id')
        ->where('sekolah.soft_delete','=',0)
        ;

        if($keyword){
            $fetch->where(function ($query) use ($keyword){
                $query->where('sekolah.nama', 'like', '%'.$keyword.'%')
                ->orWhere('sekolah.npsn', 'like', '%'.$keyword.'%');
            });
        }
        
        if($sekolah_id){
            $fetch->where('sekolah.sekolah_id', '=', $sekolah_id);
        }
        
        if($kecamatan && (int)$kecamatan != 99){
            $fetch->where('kec.kode_wilayah', '=', trim($kecamatan));
        }
        
        if($kabupaten && (int)$kabupaten != 99){
            $fetch->where('kab.kode_wilayah', '=', trim($kabupaten));
        }
        
        if($propinsi && (int)$propinsi != 99){
            $fetch->where('prop.kode_wilayah', '=', trim($propinsi));
        }
        
        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,55,";
                    array_push($arrBentuk, 55);
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,53,";
                    array_push($arrBentuk, 53);
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,54,";
                    array_push($arrBentuk, 54);
                }else{
                    $strBentuk .= $arrBentuk[$iBentuk].",";
                }
            }

            $strBentuk = substr($strBentuk, 0, (strlen($strBentuk)-1));
            $strBentuk .= ")";

            $fetch->whereIn('sekolah.bentuk_pendidikan_id', $arrBentuk);

        }else{
            // do nothing
        }
        
        if($status_sekolah && (int)$status_sekolah != 99){
            $fetch->where('sekolah.status_sekolah', '=', trim($status_sekolah));
        }
        
        if($id_level_wilayah){
            switch ($id_level_wilayah) {
                case 2:
                    $fetch->where('kab.kode_wilayah', '=', $kode_wilayah);
                    break;
                case 1:
                    $fetch->where('prop.kode_wilayah', '=', $kode_wilayah);
                    break;
                default:
                    # no dothing
                    break;
            }
        }

        $return = array();

        $return['total'] = $fetch->skip($start)->take($limit)->get()[0]->total;

        return $return;
    }

    static function getSekolah(Request $request){
        // return "oke";
        $keyword = $request->input('keyword') ? $request->input('keyword') : null;
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $id_level_wilayah = $request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : null;
        $kecamatan = $request->input('kecamatan') ? $request->input('kecamatan') : null;
        $kabupaten = $request->input('kabupaten') ? $request->input('kabupaten') : null;
        $propinsi = $request->input('propinsi') ? $request->input('propinsi') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;
        $status_sekolah = $request->input('status_sekolah') ? $request->input('status_sekolah') : null;
        $start = $request->input('start') ? $request->input('start') : 0;
        $limit = $request->input('limit') ? $request->input('limit') : 20;
        $rapor = $request->input('rapor') ? $request->input('rapor') : 'yes';

        $fetch = DB::connection('sqlsrv')
        ->table(DB::raw('sekolah with(nolock)'))
        ->select(
            'sekolah.sekolah_id', 
            'sekolah.nama', 
            'sekolah.npsn', 
            'sekolah.bentuk_pendidikan_id', 
            'sekolah.status_sekolah', 
            'sekolah.alamat_jalan', 
            'sekolah.kode_pos', 
            'sekolah.desa_kelurahan', 
            'sekolah.lintang', 
            'sekolah.bujur', 
            'kec.nama AS kecamatan', 
            'kab.nama AS kabupaten', 
            'prop.nama AS provinsi',
            'bp.nama as bentuk',
            DB::raw("(CASE WHEN sekolah.status_sekolah = 1 then 'Negeri' else 'Swasta' END) as status")
        )
        ->leftJoin(DB::raw('ref.mst_wilayah AS kec with(nolock)'), DB::raw('left(sekolah.kode_wilayah,6)'), '=', 'kec.kode_wilayah')
        ->leftJoin(DB::raw('ref.mst_wilayah AS kab with(nolock)'), 'kec.mst_kode_wilayah', '=', 'kab.kode_wilayah')
        ->leftJoin(DB::raw('ref.mst_wilayah AS prop with(nolock)'), 'kab.mst_kode_wilayah', '=', 'prop.kode_wilayah')
        ->leftJoin(DB::raw('ref.bentuk_pendidikan AS bp with(nolock)'), 'bp.bentuk_pendidikan_id', '=', 'sekolah.bentuk_pendidikan_id')
        ->where('sekolah.soft_delete','=',0)
        // ->where('sekolah.bentuk_pendidikan_id','=',13)
        ;

        if($keyword){
            // $fetch->where('sekolah.nama', 'like', DB::raw('\'%'.$keyword.'%\''))
            // ->orWhere('sekolah.npsn', 'like', DB::raw('\'%'.$keyword.'%\''));
            $fetch->where(function ($query) use ($keyword){
                $query->where('sekolah.nama', 'like', '%'.$keyword.'%')
                ->orWhere('sekolah.npsn', 'like', '%'.$keyword.'%');
            });
        }
        
        if($sekolah_id){
            $fetch->where('sekolah.sekolah_id', '=', $sekolah_id)
            ->select(
                'sekolah.*', 
                'kec.nama AS kecamatan', 
                'kab.nama AS kabupaten', 
                'prop.nama AS provinsi',
                'bp.nama as bentuk',
                DB::raw("(CASE WHEN sekolah.status_sekolah = 1 then 'Negeri' else 'Swasta' END) as status")
            );
        }
        
        if($kecamatan && (int)$kecamatan != 99){
            $fetch->where('kec.kode_wilayah', '=', trim($kecamatan));
        }
        
        if($kabupaten && (int)$kabupaten != 99){
            $fetch->where('kab.kode_wilayah', '=', trim($kabupaten));
        }
        
        if($propinsi && (int)$propinsi != 99){
            $fetch->where('prop.kode_wilayah', '=', trim($propinsi));
        }
        
        // if($bentuk_pendidikan_id && (int)$bentuk_pendidikan_id != 99){
        //     $fetch->where('sekolah.bentuk_pendidikan_id', '=', trim($bentuk_pendidikan_id));
        // }
        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,55,";
                    array_push($arrBentuk, 55);
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,53,";
                    array_push($arrBentuk, 53);
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,54,";
                    array_push($arrBentuk, 54);
                }else{
                    $strBentuk .= $arrBentuk[$iBentuk].",";
                }
            }

            $strBentuk = substr($strBentuk, 0, (strlen($strBentuk)-1));
            $strBentuk .= ")";

            // return $strBentuk;
            // $param_bentuk = "AND rekap.rekap_rapor_dapodik_sekolah.bentuk_pendidikan_id IN ".$strBentuk;
            $fetch->whereIn('sekolah.bentuk_pendidikan_id', $arrBentuk);

            // return $param_bentuk;die;
        }else{
            // $param_bentuk = "";
            // do nothing
        }
        
        if($status_sekolah && (int)$status_sekolah != 99){
            $fetch->where('sekolah.status_sekolah', '=', trim($status_sekolah));
        }
        
        if($id_level_wilayah){
            switch ($id_level_wilayah) {
                case 2:
                    $fetch->where('kab.kode_wilayah', '=', $kode_wilayah);
                    break;
                case 1:
                    $fetch->where('prop.kode_wilayah', '=', $kode_wilayah);
                    break;
                default:
                    # no dothing
                    break;
            }
        }

        // return $rapor;die;

        // return $fetch->skip($start)->take($limit)->toSql();die;

        $return = array();

        $return['total'] = $fetch->count();
        $return['rows'] = $fetch->skip($start)->take($limit)->get();

        // return $return;die;

        // if((int)$rapor == 1){

        for ($iRow=0; $iRow < sizeof($return['rows']); $iRow++) { 

                $return['rows'][$iRow]->rapor_dapodik = 0;
                $return['rows'][$iRow]->rapor_mutu = 0;
            
    //         try {

                if($rapor == 'yes'){

                    $sql_rapor_dapodik = "select * from rekap.rekap_rapor_dapodik_sekolah where semester_id = 20191 and sekolah_id = '".$return['rows'][$iRow]->sekolah_id."'";
                    
                    $fetch = DB::connection('sqlsrv_3')
                    ->select(DB::raw($sql_rapor_dapodik));

                    if($fetch){
                        $return['rows'][$iRow]->rapor_dapodik = (($fetch[0]->rapor_akhir+$fetch[0]->rapor_berkelanjutan+$fetch[0]->rapor_mutakhir)/3);
                    }
        //         } catch (\Throwable $th) {
        //             $return['rows'][$iRow]->rapor_dapodik = 0;
        //         }

        //         try {
                    $sql_rapor_mutu = "select sekolah_id, avg(r18) as rapor_mutu from master_pmp where sekolah_id = '".$return['rows'][$iRow]->sekolah_id."' and level = 'parent' group by sekolah_id";

                    $fetch = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_rapor_mutu));

                    if($fetch){
                        $return['rows'][$iRow]->rapor_mutu = $fetch[0]->rapor_mutu;
                    }

                }
    //         } catch (\Throwable $th) {
    //             $return['rows'][$iRow]->rapor_mutu = 0;
    //         }

        }
        // }

        return $return;
    }

    public function getRekapSekolah(Request $request){
        // return "oke";
        $keyword = $request->input('keyword') ? $request->input('keyword') : null;
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $semester_id = $request->input('semester_id') ? $request->input('semester_id') : 20201;
        $start = $request->input('start') ? $request->input('start') : 0;
        $limit = $request->input('limit') ? $request->input('limit') : 20;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;

        $fetch = DB::connection('sqlsrv_2')
        ->table(DB::raw('rekap_sekolah with(nolock)'))
        ->select(
            'rekap_sekolah.*'
        )
        ->where('rekap_sekolah.soft_delete','=',0)
        ->where('rekap_sekolah.semester_id','=',$semester_id)
        ;

        if($keyword){
            $fetch->where('rekap_sekolah.nama', 'like', DB::raw('\'%'.$keyword.'%\''))
            ->orWhere('rekap_sekolah.npsn', 'like', DB::raw('\'%'.$keyword.'%\''));
        }
        
        if($sekolah_id){
            $fetch->where('rekap_sekolah.sekolah_id', '=', $sekolah_id);
        }

        $return = array();

        $return['total'] = $fetch->count();
        $return['rows'] = $fetch->skip($start)->take($limit)->get();

        return $return;
    }

}

?>
