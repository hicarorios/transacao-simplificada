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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransacaoRequest $request)
    {
        try {

            $transacao = $this->transacaoService->iniciarTransacao($request);
            $this->transacaoService->efetuarTransacao($transacao, $request);

            return response()->json([
                'TransactionID' => $transacao->id,
                'message' => 'The amount has been transferred with success',
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'TransactionID' => $transacao->id,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'TransactionID' => $transacao->id,
                'message' => 'The transference cannot be completed',
            ], 400);
        }
    }
}
