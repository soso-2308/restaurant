<?php
namespace App\Core;

class Validator
{
    public function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $rulesList = explode('|', $ruleSet);
            $value = $data[$field] ?? null;
            foreach ($rulesList as $rule) {
                if ($rule === 'required' && empty($value) && $value !== '0') {
                    $errors[] = "Le champ '$field' est obligatoire";
                }
                if (strpos($rule, 'min:') === 0 && strlen($value) < substr($rule, 4)) {
                    $errors[] = "Le champ '$field' doit contenir au moins " . substr($rule, 4) . " caractères";
                }
                if (strpos($rule, 'max:') === 0 && strlen($value) > substr($rule, 4)) {
                    $errors[] = "Le champ '$field' doit contenir au maximum " . substr($rule, 4) . " caractères";
                }
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Le champ '$field' doit être un email valide";
                }
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[] = "Le champ '$field' doit être un nombre";
                }
                if ($rule === 'phone') {
                    // Simple validation pour numéro de téléphone (exemple)
                    if (!preg_match('/^[0-9]{8,15}$/', $value)) {
                        $errors[] = "Le numéro de téléphone est invalide";
                    }
                }
            }
        }
        return $errors;
    }
}