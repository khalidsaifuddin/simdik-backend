<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SBBController extends Controller{

    static function simpanDonasi(Request $request){
        $pengguna_id = $request->input('pengguna_id');
        $bantuan_id = $request->input('bantuan_id');
        $nominal = $request->input('nominal');
        $tanggal = $request->input('tanggal');
        $bank_pengirim = $request->input('bank_pengirim');
        $atas_nama_pengirim = $request->input('atas_nama_pengirim');
        $nama_file = $request->input('nama_file');

        $fetch_id = DB::connection('sqlsrv_sbb')->table('donasi')->select(DB::raw('newid() as donasi_id'))
        ->skip(0)->take(1)->get();

        // return $fetch_id[0]->donasi_id;die;

        $exe = DB::connection('sqlsrv_sbb')->table('donasi')
        ->insert([
            'donasi_id' => $fetch_id[0]->donasi_id,
            'pengguna_id' => $pengguna_id,
            'tanggal' => $tanggal,
            'nominal' => $nominal,
            'jenis_donasi_id' => 1,
            'verifikasi' => 0,
            'metode_id' => 1,
            'rekening_tujuan_id' => 1,
            'bank_pengirim' => $bank_pengirim,
            'atas_nama_pengirim' => $atas_nama_pengirim,
            'nama_file' => $nama_file
        ]);

        if($exe){
            $return = array();
            $return['status'] = true;

            if($bantuan_id){
                $fetch_id2 = DB::connection('sqlsrv_sbb')->table('donasi')->select(DB::raw('newid() as donasi_id'))
                ->skip(0)->take(1)->get();

                $exe2 = DB::connection('sqlsrv_sbb')->table('donasi_bantuan')
                ->insert([
                    'donasi_bantuan_id' => $fetch_id2[0]->donasi_id,
                    'donasi_id' => $fetch_id[0]->donasi_id,
                    'bantuan_id' => $bantuan_id,
                    'pengguna_id' => $pengguna_id
                ]);
            }

        }else{
            $return = array();
            $return['status'] = true;
        }

        return $return;
    }

    static function getDonasi(Request $request){
        $sql = "SELECT
                    bantuan.bantuan_id,
                    bantuan.nama as nama_bantuan,
                    jenis.nama as jenis_donasi,
                    metode.nama as metode,
                    donasi.*,
                    cast(donasi.nominal as int) as nominal 
                FROM
                    donasi 
                    LEFT JOIN donasi_bantuan doban on doban.donasi_id = donasi.donasi_id and doban.soft_delete = 0
                    LEFT JOIN bantuan on bantuan.bantuan_id = doban.bantuan_id and bantuan.soft_delete = 0
                    JOIN ref.jenis_donasi jenis on jenis.jenis_donasi_id = donasi.jenis_donasi_id
                    JOIN ref.metode metode on metode.metode_id = donasi.metode_id
                WHERE
                    donasi.soft_delete = 0";
        
        if($request->input('pengguna_id')){
            $sql .= " AND donasi.pengguna_id = '".$request->input('pengguna_id')."'";
        }

        if($request->input('bantuan_id')){
            $sql .= " AND bantuan.bantuan_id = '".$request->input('bantuan_id')."'";
        }

        $sql .= " ORDER by donasi.tanggal desc";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_sbb')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }

    static function getBantuan(Request $request){
        $sql = "select 
                    bantuan.*,
                    gambar_bantuan.nama_file as gambar,
                    jangka.nama as satuan_jangka_waktu,
                    ISNULL(donasi.total,0) as terkumpul,
                    ROUND( ( donasi.total/cast((bantuan.nominal*bantuan.jangka_waktu) as float) * 100 ), 2 ) as persen
                from 
                    bantuan
                LEFT join gambar_bantuan on gambar_bantuan.bantuan_id = bantuan.bantuan_id 
                JOIN ref.satuan_jangka_waktu jangka on jangka.satuan_jangka_waktu_id = bantuan.satuan_jangka_waktu_id
                and gambar_bantuan.soft_delete = 0 
                and gambar_bantuan.gambar_utama = 1
                LEFT JOIN (
                    SELECT
                        bantuan_id,
                        SUM ( nominal ) AS total 
                    FROM
                        donasi
                        JOIN donasi_bantuan ON donasi.donasi_id = donasi_bantuan.donasi_id 
                    GROUP BY
                        donasi_bantuan.bantuan_id
                ) donasi on donasi.bantuan_id = bantuan.bantuan_id
                where 
                    bantuan.soft_delete = 0";
        
        if($request->input('bantuan_id')){
            $sql .= " AND bantuan.bantuan_id = '".$request->input('bantuan_id')."'";
        }
        
        $fetch = DB::connection('sqlsrv_sbb')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }

    public function upload(Request $request)
    {
        $data = $request->all();
        $file = $data['image'];
        $donasi_id = $data['donasi_id'];
        // $jenis = $data['jenis'];

        if(($file == 'undefined') OR ($file == '')){
            return response()->json(['msg' => 'tidak_ada_file']);
        }

        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();

        $destinationPath = base_path('/public/assets/berkas');
        $upload = $file->move($destinationPath, $name);

        $msg = $upload ? 'sukses' : 'gagal';

        if($upload){
            // $execute = DB::connection('sqlsrv_sbb')->table('donasi')->where('donasi_id','=',$donasi_id)->update([
            //     'nama_file' => "/assets/berkas/".$name
            // ]);

            // if($execute){
            return response(['msg' => $msg, 'filename' => "/assets/berkas/".$name]);
            // }
        }

    }
}