<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransacaoRequest;
use App\Model\Transacao;
use App\Service\TransacaoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class TransacaoController extends Controller
{
    private TransacaoService $transacaoService;

    public function __construct(TransacaoService $transacaoService)
    {
        $this->transacaoService = $transacaoService;
    }

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
        // remover o dinheiro informado da carteira do usuario pagador
        // autorizar transacao
        // depositar o dinheiro informado na carteira do usuario beneficiario
        // enviar notificação para usuario beneficiario *
        // responder com os dados da transacao

        try {

            $this->transacaoService->efetuarTransacao($request);

        }
        catch(InvalidArgumentException $e) {
            return response()->json(['menssage' => $e->getMessage()], 400);
        }

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
