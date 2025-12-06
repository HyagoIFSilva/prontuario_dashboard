<?php

namespace App\Http\Controllers\Unidade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnidadeController extends Controller
{
    public function dashboard()
    {
        return view('unidade.dashboardUnidade');
    }

    public function ajuda()
    {
        return view('unidade.ajuda');
    }

    public function perfilUnidade()
    {
        return view('unidade.perfilUnidade');
    }

    public function seguranca()
    {
        return view('unidade.seguranca');
    }

}