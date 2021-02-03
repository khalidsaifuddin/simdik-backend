<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ValidasiDataController extends Controller
{
    static function init(){
        return true;
    }

    static function getRekapValidasiBeranda(Request $request){
        $sql = "SELECT 
            SUM ( 1 ) AS total,
            SUM ( CASE WHEN status_validasi_id = 1 THEN 1 ELSE 0 END ) AS valid
            -- round(SUM ( CASE WHEN status_validasi_id = 1 THEN 1 ELSE 0 END ) / CAST(SUM(1) as float), 2) as desimal,
            -- SUM ( CASE WHEN status_validasi_id = 1 THEN 1 ELSE 0 END ) / CAST(SUM(1) as float) * 100 as persen
        FROM
            admin_sa.dbo.rekap_sekolah AS rekap_sekolah
            LEFT JOIN validasi_data ON validasi_data.sekolah_id = rekap_sekolah.sekolah_id 
            AND validasi_data.semester_id = rekap_sekolah.semester_id 
        WHERE
            rekap_sekolah.semester_id = '20201' 
            AND rekap_sekolah.bentuk_pendidikan_id IN ( 13, 55 ) 
            AND rekap_sekolah.soft_delete = 0";

        $fetch = DB::connection('sqlsrv_3')->select($sql);

        return $fetch;
    }

    static function simpanValidasiData(Request $request){
        $sekolah_id = $request->sekolah_id ? $request->sekolah_id : null;
        $pengguna_id = $request->pengguna_id ? $request->pengguna_id : null;
        $semester_id = $request->semester_id ? $request->semester_id : null;
        $peran_id = $request->peran_id ? $request->peran_id : null;
        $soft_delete = $request->soft_delete ? $request->soft_delete : 0;

        $fetch_cek = DB::connection('sqlsrv_3')->table('validasi_data')
        ->where('validasi_data.semester_id','=',$semester_id)
        ->where('validasi_data.sekolah_id','=',$sekolah_id)
        ->where('validasi_data.soft_delete','=',$soft_delete)
        ->where('validasi_data.level_validasi_id','=',($peran_id == 54 ? 'lpmp' : ($peran_id == 6 ? 'dinas' : ($peran_id == 8 ? 'dinas' : 'pusat'))))
        ->get();

        if(sizeof($fetch_cek) > 0){
            //sudah ada, update
            $exe = DB::connection('sqlsrv_3')->table('validasi_data')
            ->where('validasi_data.semester_id','=',$semester_id)
            ->where('validasi_data.sekolah_id','=',$sekolah_id)
            ->where('validasi_data.soft_delete','=',$soft_delete)
            ->where('validasi_data.level_validasi_id','=',($peran_id == 54 ? 'lpmp' : ($peran_id == 6 ? 'dinas' : ($peran_id == 8 ? 'dinas' : 'pusat'))))
            ->update([
                'sekolah_id' => $request->sekolah_id,
                'pengguna_id' => $request->pengguna_id,
                'level_validasi_id' => ($peran_id == 54 ? 'lpmp' : ($peran_id == 6 ? 'dinas' : ($peran_id == 8 ? 'dinas' : 'pusat'))),
                'status_validasi_id' => $request->status_validasi_id,
                'semester_id' => $request->semester_id,
                'keterangan' => $request->keterangan,
                'peran_id' => $request->peran_id,
                'last_update' => DB::raw("getdate()"),
                'soft_delete' => $soft_delete
            ]);
        }else{
            //belum ada, insert
            $exe = DB::connection('sqlsrv_3')->table('validasi_data')
            ->insert([
                'validasi_data_id' => DB::raw("newid()"),
                'sekolah_id' => $request->sekolah_id,
                'pengguna_id' => $request->pengguna_id,
                'level_validasi_id' => ($peran_id == 54 ? 'lpmp' : ($peran_id == 6 ? 'dinas' : ($peran_id == 8 ? 'dinas' : 'pusat'))),
                'status_validasi_id' => $request->status_validasi_id,
                'semester_id' => $request->semester_id,
                'keterangan' => $request->keterangan,
                'peran_id' => $request->peran_id,
                'create_date' => DB::raw("getdate()"),
                'last_update' => DB::raw("getdate()"),
                'soft_delete' => $soft_delete
            ]);
        }

        // return $fetch_cek;
        return response(
            [
                'sukses' => ($exe ? true : false),
                'rows' => DB::connection('sqlsrv_3')->table('validasi_data')
                ->where('validasi_data.semester_id','=',$semester_id)
                ->where('validasi_data.sekolah_id','=',$sekolah_id)
                ->where('validasi_data.soft_delete','=',$soft_delete)
                ->where('validasi_data.level_validasi_id','=',($peran_id == 54 ? 'lpmp' : ($peran_id == 6 ? 'dinas' : ($peran_id == 8 ? 'dinas' : 'pusat'))))
                ->get()
            ],
            200
        );
        
    }

    static function getValidasiDataRecord(Request $request){
        $sekolah_id = $request->sekolah_id ? $request->sekolah_id : null;
        $start = $request->start ? $request->start : 0;
        $limit = $request->limit ? $request->limit : 20;

        $fetch = DB::connection('sqlsrv_3')->table('validasi_data')
        ->join('admin_sa.dbo.pengguna as pengguna','pengguna.pengguna_id','=','validasi_data.pengguna_id')
        ->join('admin_sa.dbo.sekolah as sekolah','sekolah.sekolah_id','=','validasi_data.sekolah_id')
        ->join('admin_sa.ref.peran as peran','peran.peran_id','=','pengguna.peran_id')
        ->join('admin_sa.ref.mst_wilayah as wilayah','wilayah.kode_wilayah','=','pengguna.kode_wilayah')
        ->join('admin_sa.ref.mst_wilayah as kec','kec.kode_wilayah','=',DB::raw('LEFT(sekolah.kode_wilayah,6)'))
        ->join('admin_sa.ref.mst_wilayah as kab','kab.kode_wilayah','=','kec.mst_kode_wilayah')
        ->join('admin_sa.ref.mst_wilayah as prov','prov.kode_wilayah','=','kab.mst_kode_wilayah')
        ->where('validasi_data.soft_delete','=',0)
        ->select(
            'validasi_data.*',
            'pengguna.nama as nama_pengguna',
            'peran.nama as peran',
            'wilayah.nama as wilayah',
            'sekolah.nama as nama_sekolah',
            'sekolah.npsn as npsn',
            'sekolah.alamat_jalan as alamat_jalan',
            'kec.nama as kecamatan',
            'kab.nama as kabupaten',
            'prov.nama as provinsi'
        )
        ;

        if($sekolah_id){
            $fetch->where('validasi_data.sekolah_id','=', $sekolah_id);
        }

        return response(
            [
                'total' => $fetch->count(),
                'rows' => $fetch->skip($start)->take($limit)->orderBy('validasi_data.last_update', 'DESC')->get()
            ],
            200
        );
    }

    static function getValidasiData(Request $request){
        // return "oke";
        $kode_wilayah = $request->kode_wilayah ? $request->kode_wilayah : null;
        $id_level_wilayah = $request->id_level_wilayah ? $request->id_level_wilayah : null;
        $keyword = $request->keyword ? $request->keyword : null;
        $semester_id = $request->semester_id ? $request->semester_id : 20201;
        $bentuk_pendidikan_id = $request->bentuk_pendidikan_id ? $request->bentuk_pendidikan_id : null;
        $kode_wilayah = $request->kode_wilayah ? $request->kode_wilayah : '000000';
        $status_sekolah = $request->status_sekolah ? $request->status_sekolah : null;
        $id_level_wilayah = $request->id_level_wilayah ? $request->id_level_wilayah : '0';
        $start = $request->start ? $request->start : 0;
        $limit = $request->limit ? $request->limit : 20;

        $fetch = DB::connection('sqlsrv_3')
        ->table('admin_sa.dbo.rekap_sekolah as rekap_sekolah')
        ->leftJoin(DB::raw("(SELECT
            validasi_data.sekolah_id,
            validasi_data.semester_id,
            max(last_update) as last_update,
            MAX(case when level_validasi_id = 'pusat' then status_validasi_id else 0 end) as validasi_pusat,
            MAX(case when level_validasi_id = 'lpmp' then status_validasi_id else 0 end) as validasi_lpmp,
            MAX(case when level_validasi_id = 'dinas' then status_validasi_id else 0 end) as validasi_dinas,
            MAX(case when level_validasi_id = 'pusat' then validasi_data.create_date else null end) as tanggal_validasi_pusat,
            MAX(case when level_validasi_id = 'lpmp' then validasi_data.create_date else null end) as tanggal_validasi_lpmp,
            MAX(case when level_validasi_id = 'dinas' then validasi_data.create_date else null end) as tanggal_validasi_dinas
        FROM
            validasi_data 
        WHERE
            validasi_data.soft_delete = 0 
        GROUP BY
            validasi_data.sekolah_id,
            validasi_data.semester_id) as validasi_data"),function($join){
            $join->on('validasi_data.sekolah_id','=', 'rekap_sekolah.sekolah_id');
            $join->on('validasi_data.semester_id','=', 'rekap_sekolah.semester_id');
            // $join->on('validasi_data.soft_delete','=', DB::raw('0'));
        })
        ->where('rekap_sekolah.soft_delete','=',0)
        ->where('rekap_sekolah.semester_id','=',$semester_id)
        ->select(
            'validasi_data.semester_id',
            'validasi_data.last_update',
            'validasi_data.validasi_pusat',
            'validasi_data.validasi_lpmp',
            'validasi_data.validasi_dinas',
            'validasi_data.tanggal_validasi_pusat',
            'validasi_data.tanggal_validasi_lpmp',
            'validasi_data.tanggal_validasi_dinas',
            'rekap_sekolah.sekolah_id',
            'rekap_sekolah.nama',
            'rekap_sekolah.npsn',
            'rekap_sekolah.kecamatan',
            'rekap_sekolah.kabupaten',
            'rekap_sekolah.propinsi',
            'rekap_sekolah.kode_wilayah_kecamatan',
            'rekap_sekolah.kode_wilayah_kabupaten',
            'rekap_sekolah.kode_wilayah_propinsi',
            'rekap_sekolah.bentuk_pendidikan_id',
            'rekap_sekolah.status_sekolah'
            // DB::raw("'2020-08-01' as tanggal_validasi_pusat"),
            // DB::raw("'2020-08-01' as tanggal_validasi_lpmp"),
            // DB::raw("'2020-08-01' as tanggal_validasi_dinas"),
            // DB::raw("null as validasi_pusat"),
            // DB::raw("null as validasi_lpmp"),
            // DB::raw("null as validasi_dinas")
        )
        // ->leftJoin('validasi_data','validasi_data.sekolah_id')
        ;

        if($keyword){
            // $fetch->where('rekap_sekolah.nama', 'ilike', DB::raw("'%".$keyword."%'"));
            $fetch->where(function($query) use ($keyword){
                $query->where('rekap_sekolah.nama', 'like', DB::raw("'%".$keyword."%'"))
                      ->orWhere('rekap_sekolah.npsn', 'like', DB::raw("'%".$keyword."%'"));
            });
            // $param_keyword = "AND {$kolom_nama} like '%".$keyword."%'";
        }else{
            // $param_keyword = "";
        }

        if($status_sekolah){
            $fetch->where(function($query) use ($status_sekolah){
                $query->where('rekap_sekolah.status_sekolah', '=', $status_sekolah);
            });
            
        }

        switch ($id_level_wilayah) {
            case '0':
                # no dothing
                break;
            case '1':
                $fetch->where('rekap_sekolah.kode_wilayah_propinsi','=',$kode_wilayah);
                break;
            case '2':
                $fetch->where('rekap_sekolah.kode_wilayah_kabupaten','=',$kode_wilayah);
                break;
            case '3':
                $fetch->where('rekap_sekolah.kode_wilayah_kecamatan','=',$kode_wilayah);
                break;
            default:
                # no dothing
                break;
        }

        if($bentuk_pendidikan_id){
            $arrBentuk = explode("-", $bentuk_pendidikan_id);
            // $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    // $strBentuk .= "13,55,";
                    // $fetch->whereIn('rekap_sekolah.bentuk_pendidikan_id',array('13','55'));
                    array_push($arrBentuk,'55');
                }else if($arrBentuk[$iBentuk] == '5'){
                    // $strBentuk .= "5,53,";
                    // $fetch->whereIn('rekap_sekolah.bentuk_pendidikan_id',array('5','53'));
                    array_push($arrBentuk,'53');
                }else if($arrBentuk[$iBentuk] == '6'){
                    // $strBentuk .= "6,54,";
                    // $fetch->whereIn('rekap_sekolah.bentuk_pendidikan_id',array('6','54'));
                    array_push($arrBentuk,'54');
                }else{
                    // $strBentuk .= $arrBentuk[$iBentuk].",";
                }
            }

            $fetch->whereIn('rekap_sekolah.bentuk_pendidikan_id', $arrBentuk);

            // $strBentuk = substr($strBentuk, 0, (strlen($strBentuk)-1));
            // $strBentuk .= ")";

            // return $strBentuk;
            // $param_bentuk = "AND rekap.rekap_rapor_dapodik_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }

        // return $fetch->toSql();die;

        return response(
            [
                'total' => $fetch->count(),
                'rows' => $fetch->skip($start)->take($limit)->orderBy('validasi_data.last_update', 'DESC')->orderBy('rekap_sekolah.nama', 'ASC')->get()
            ],
            200
        );
    }

}