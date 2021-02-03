<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $whitelist = [
            'http://localhost:3000',
            'http://localhost:3001',
            'http://localhost:8081',
            'http://localhost:8082',
            'http://localhost:8000',
            'http://localhost',
            'http://pmp.dikdasmen.kemdikbud.go.id:2019',
            'http://pmp.dikdasmen.kemdikbud.go.id:8882',
            'http://pmp.dikdasmen.kemdikbud.go.id:8881',
            'http://validasi.dikdasmen.kemdikbud.go.id',
            "http://192.168.0.104:3000",
            "http://manajemen.pmp.kemdikbud.go.id",
            "http://supervisi.pmp.kemdikbud.go.id",
            "http://manajemendata.sma.kemdikbud.go.id:8033",
            "http://test.one",
            "http://sioman.nufaza.co.id",
            "http://36.89.83.197",
            "http://sadetik.bandungkab.go.id",
            "http://spm.bandungkab.go.id",
            null
        ];
        
        $origin = $request->header('Origin');

        if(in_array($origin, $whitelist))
        return $next($request)->withHeaders(['Access-Control-Allow-Origin'=>$origin, 'Access-Control-Allow-Methods'=>'GET, POST, PATCH, PUT, DELETE, OPTIONS', 'Access-Control-Allow-Headers'=>'Content-Type, Accept, Authorization, X-Requested-With, Application', 'Access-Control-Max-Age'=>86400]);
        return Response()->json(['status'=>'Domain Tidak terdaftar']);

    }
}
