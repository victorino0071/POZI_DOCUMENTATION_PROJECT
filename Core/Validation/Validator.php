<?php


namespace Core\Validation;


class Validator{

    protected array $errors = [];

    public function validate(array $data, array $rules):void{
        foreach ($rules as $field => $ruleString){
            $ruleSet = explode('|', $ruleString);


            foreach($ruleSet as $rule){
                $value = $data[$field] ?? null;

                $this->appluRule($field, $value, $rule);
            }
        }
    }


    protected function appluRule(string $field, mixed $value, string $rule):void{
        $params = [];

        if (str_contains($rule, ':')){
            [$rule, $paramStr] = explode(':', $rule);
            $params = explode(',', $paramStr);
        }

        switch ($rule){
            case 'required':
                if (is_null($value) || $value === '') {
                    $this->addError($field, "O campo '{$field}' é obrigatório.");
                }
                break;
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "O campo '{$field}' deve ser um e-mail válido.");
                }
                break;

            case 'min':
                $min = (int) $params[0];
                if (!empty($value) && strlen((string)$value) < $min) {
                    $this->addError($field, "O campo '{$field}' deve ter pelo menos {$min} caracteres.");
                }
                break;

            case 'max':
                $max = (int) $params[0];
                if (!empty($value) && strlen((string)$value) > $max) {
                    $this->addError($field, "O campo '{$field}' não pode exceder {$max} caracteres.");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "O campo '{$field}' deve ser um número.");
                }
                break;
        }
    }

    protected function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }
}