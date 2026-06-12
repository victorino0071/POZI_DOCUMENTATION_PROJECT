<?php


namespace App\Channels\AdminAPI\Controllers;


use Core\Controller;
use Core\Http\Request;
use App\Modules\Finance\Models\ContractModel;


class ContractController extends Controller{
    private ContractModel $contractModel;

    public function __construct(ContractModel $contractModel){
        $this->contractModel = $contractModel;
    }

    public function show(Request $request, int $id){
        $contract = $this->contractModel->find($id);

        if (!$contract){
            return $this->json(['error'=> 'Contrato não encontrado'], 404);
        }


        $dto = [
            'id' => $contract->id,
            'titulo_publico' => $contract->title,
            'valor_formatado' => "R$ " . number_format($contract->value, 2, ',', '.'),
            'status' => strtoupper($contract->status)
        ];

        return $this->json($dto, 200);
    }
}