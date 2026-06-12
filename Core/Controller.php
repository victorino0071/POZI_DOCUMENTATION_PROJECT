<?php

namespace Core;


use Core\Http\Request;
use Core\Validation\Validator;

abstract class Controller{


    protected function json(mixed $data, int $status = 200): void{
        http_response_code($status);


        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void{
        header("Location: {$url}");
        exit;
    }

    protected function validate(Request $request, array $rules): array
    {
        $validator = new Validator();
        $validator->validate($request->all(), $rules);

        if ($validator->fails()) {
            $this->json([
                'mensagem' => 'Os dados fornecidos são inválidos.',
                'erros' => $validator->errors()
            ], 422);
        }

        $validatedData = [];
        foreach (array_keys($rules) as $key) {
            $validatedData[$key] = $request->input($key);
        }

        return $validatedData;
    }
}