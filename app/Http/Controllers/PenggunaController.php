<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTFactory;

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenggunaController extends Controller
{
    static public function generateUUID()
    {
        $uuid = DB::connection('sqlsrv_2')
        ->table(DB::raw('pengguna with(nolock)'))
        ->select(DB::raw('newid() as uuid'))
        ->first();

        return $uuid->{'uuid'};
    }

    public function buatPengguna(Request $request){
        $data = $request->input('data') ? $request->input('data') : null;

        // return $data['username'];die;
        $uuid = self::generateUUID();
        // return $uuid;die;

        try {

            $execute = DB::connection('sqlsrv_2')->table('pengguna')->insert([
                'pengguna_id' => $uuid,
                'username' => $data['username'],
                'nama' => $data['nama'],
                'gambar' => $data['gambar'],
                'create_date' => date('Y-m-d H:i:s'),
                'last_update' => date('Y-m-d H:i:s'),
                'last_sync' => '1990-01-01 01:01:01',
                'soft_delete' => '0',
                'verified' => '0',
                'aktif' => '1',
                'akun_google' => $data['username']
            ]);

            if($execute){
                // return array("status" => "berhasil");
                $user = DB::connection('sqlsrv_2')->table('pengguna')->where('pengguna_id','=',$uuid)->get();

                $return = array();
                $return['total'] = sizeof($user);
                $return['rows'] = $user;

                return $return;
            }else{
                // return array("status" => "gagal query");

                $return = array();
                $return['total'] = 0;
                $return['rows'] = [];

                return $return;
            }
            
        } catch (\Throwable $th) {
            // return array("status" => "gagal exception", "exception" => $th);
            $return = array();
            $return['total'] = 0;
            $return['rows'] = [];

            return $return;
        }
    }

    public function simpanPengguna(Request $request) { 
        $pengguna_id = $request->input('pengguna_id') ? $request->input('pengguna_id') : null;
        $username = $request->input('username') ? $request->input('username') : null;
        $data = $request->input('data') ? $request->input('data') : null;

        if(array_key_exists('password', $data)){
            $data['password'] = md5($data['password']);
            unset($data['password_ulang']);
        }

        // return var_dump($data);die;

        try {
            //code...
            if($pengguna_id){
                $execute = DB::connection('sqlsrv_2')->table('pengguna')->where('pengguna_id', '=', DB::raw("'".$pengguna_id."'"))->update($data);
            }
    
            if($username){
                $execute = DB::connection('sqlsrv_2')->table('pengguna')->where('username', '=', DB::raw("'".$username."'"))->update($data);
            }
            
            if($execute){
                return array("status" => "berhasil");
            }else{
                return array("status" => "gagal");
            }

        } catch (\Throwable $th) {
            return array("status" => "gagal");
        }


    }

    public function getPengguna(Request $request) { 
        $pengguna_id = $request->input('pengguna_id') ? $request->input('pengguna_id') : null;
        $username = $request->input('username') ? $request->input('username') : null;
        $start = $request->input('start') ? $request->input('start') : 0;
        $limit = $request->input('limit') ? $request->input('limit') : 20;

        if($pengguna_id){
            $user = DB::connection('sqlsrv_2')
            ->table(DB::raw('pengguna with(nolock)'))
            ->leftJoin('ref.peran as peran','peran.peran_id','=','pengguna.peran_id')
            ->leftJoin('ref.mst_wilayah as wilayah', 'wilayah.kode_wilayah','=','pengguna.kode_wilayah')
            ->where('pengguna_id', '=', DB::raw("'".$pengguna_id."'"))
            ->where('soft_delete', '=', 0)
            ->select(
                'pengguna.*',
                'peran.nama as peran',
                'wilayah.nama as wilayah'
            )
            ->get();
        }

        if($username){
            $user = DB::connection('sqlsrv_2')
            ->table(DB::raw('pengguna with(nolock)'))
            ->leftJoin('ref.peran as peran','peran.peran_id','=','pengguna.peran_id')
            ->leftJoin('ref.mst_wilayah as wilayah', 'wilayah.kode_wilayah','=','pengguna.kode_wilayah')
            ->where('username', '=', DB::raw("'".$username."'"))
            ->where('soft_delete', '=', 0)
            ->select(
                'pengguna.*',
                'peran.nama as peran',
                'wilayah.nama as wilayah'
            )
            ->get();
        }

        if(!$pengguna_id && !$username){
            $builder = DB::connection('sqlsrv_2')
            ->table(DB::raw('pengguna with(nolock)'))
            ->leftJoin('ref.peran as peran','peran.peran_id','=','pengguna.peran_id')
            ->leftJoin('ref.mst_wilayah as wilayah', 'wilayah.kode_wilayah','=','pengguna.kode_wilayah')
            ->where('soft_delete', '=', 0);

            if($request->input('peran_id') && $request->input('peran_id') != 99){
                $builder->where('peran.peran_id','=',$request->input('peran_id'));
            }

            if($request->input('verified') != null && $request->input('verified') != 99){
                $builder->where('pengguna.verified','=',$request->input('verified'));
            }
            if($request->input('keyword') != null){
                $builder->where('pengguna.nama','like', '%'.$request->input('keyword').'%');
            }
            
            $count = $builder->select(DB::raw('sum(1) as total'))->first();
            $user = $builder->select(
                'pengguna.*',
                'peran.nama as peran',
                'wilayah.nama as wilayah'
            )
            ->skip($start)
            ->take($limit)->get();
        }

        $return = array();
        $return['total'] = ($pengguna_id || $username ? sizeof($user) : $count->total);
        $return['rows'] = $user;
        
        return $return;
    }

    public function authenticate(Request $request) { 
        $username = $request->input('username');
        $password = $request->input('password');
        $passCode = md5($password);

        $user = DB::connection('sqlsrv_2')
        ->table(DB::raw('pengguna with(nolock)'))
        ->where('username', '=', DB::raw("'".$username."'"))
        ->where('soft_delete', '=', 0)
        ->first();

        if($user){
            if($passCode == $user->password){
                try { 
                    // verify the credentials and create a token for the user
                    // if ( !$token = JWTAuth::encode($payload) ) {
                    
                    $factory = JWTFactory::customClaims([
                        'sub'   => env('API_ID'),
                        'email' => $user->{'username'},
                        'password' => $user->{'password'}
                    ]);
                    $payload = $factory->make();

                    if ( !$token = JWTAuth::encode($payload)) { 
                        return response()->json(['error' => 'invalid_credentials'], 401);
                    } 

                } catch (JWTException $e) { 
                    // something went wrong 
                    return response()->json(['error' => 'could_not_create_token'], 500); 
                
                }

                $return = array();
                $return['token'] = (string) $token;
                $return['user'] = $user;
                
                return $return;

            }else{
                return response()->json(['error' => 'Password yang Anda gunakan salah. Silakan mencoba kembali menggunakan password lain'], 200);
            }
        }else{
            return response()->json(['error' => 'Pengguna yang Anda gunakan tidak ditemukan. Silakan mencoba kembali menggunakan username lain'], 200);
        }
    }

    public function upload(Request $request)
    {
        $data = $request->all();
        $file = $data['image'];
        $pengguna_id = $data['pengguna_id'];
        $jenis = $data['jenis'];

        if(($file == 'undefined') OR ($file == '')){
            return response()->json(['msg' => 'tidak_ada_file']);
        }

        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();

        $destinationPath = base_path('/public/assets/berkas');
        $upload = $file->move($destinationPath, $name);

        $msg = $upload ? 'sukses' : 'gagal';

        if($upload){
            $execute = DB::connection('sqlsrv_2')->table('pengguna')->where('pengguna_id','=',$pengguna_id)->update([
                $jenis => "/assets/berkas/".$name
            ]);

            if($execute){
                return response(['msg' => $msg, 'filename' => "/assets/berkas/".$name, 'jenis' => $jenis]);
            }
        }

    }
}

?>