<?php

namespace App\Console\Commands\Log;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class guidGenerator
{
    static function index(){
        $uuid = DB::connection('sqlsrv_admin_sa_nufaza')
        ->table(DB::raw('rekap_sekolah with(nolock)'))
        ->select(DB::raw('newid() as uuid'))
        ->first();

        return $uuid->{'uuid'};
    }
}