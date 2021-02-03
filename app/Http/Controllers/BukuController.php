<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;

class BukuController extends Controller
{
    public function index($value='')
    {
    	# code...
    }

    public function getMapel(Request $request){
        $random = $request->input('random') ? $request->input('random') : 0; 
        $start = $request->input('start') ? $request->input('start') : 0; 
        $limit = $request->input('limit') ? $request->input('limit') : 10; 

        $fetch = DB::connection('sqlsrv_2')
        ->table('buku.buku')
        ->where('soft_delete','=',0)
        ->select(
            'mapel',
            DB::raw('max(gambar_cover) as gambar_cover'),
            DB::raw('SUM(1) as total')
        )
        ->groupBy('mapel')
        ;

        if($random == 1){
            $fetch->orderBy(DB::raw('NEWID()'), 'desc');
        }

        $return = array();

        $return['total'] = $fetch->count();
        $return['rows'] = $fetch->skip($start)->take($limit)->get();

        return $return;
    }

    public function getBuku(Request $request){

        $random = $request->input('random') ? $request->input('random') : 0; 
        $start = $request->input('start') ? $request->input('start') : 0; 
        $limit = $request->input('limit') ? $request->input('limit') : 10; 
        $buku_id = $request->input('buku_id') ? $request->input('buku_id') : null; 
        $mapel = $request->input('mapel') ? $request->input('mapel') : null; 
        
        $fetch = DB::connection('sqlsrv_2')
        ->table('buku.buku')
        ->where('soft_delete','=',0)
        ;

        if($random == 1){
            $fetch->orderBy(DB::raw('NEWID()'), 'desc');
        }

        if($buku_id){
            $fetch->where('buku_id','=', $buku_id);
        }

        if($mapel){
            $fetch->where('mapel','=', $mapel);
        }

        $return = array();

        $return['total'] = $fetch->count();
        $return['rows'] = $fetch->skip($start)->take($limit)->get();

        return $return;

    }

}