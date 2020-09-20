<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransacaoRequest;
use App\Model\Transacao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransacaoController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransacaoRequest $request)
    {
        return response()->json([], 201);
    }

    /**
     * @param  \App\Transacao  $Transacao
     * @return \Illuminate\Http\Response
     */
    public function show(Transacao $Transacao)
    {
        //
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transacao  $Transacao
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transacao $Transacao)
    {
        //
    }

    /**
     * @param  \App\Transacao  $Transacao
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transacao $Transacao)
    {
        //
    }
}
