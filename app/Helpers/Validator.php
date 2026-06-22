<?php
namespace App\Helpers;

class Validator
{
    private array $errors = [];

    /**
     * Valider des données selon des règles
     */
    public function validate(array $data, array $rules): array
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $rulesList = explode('|', $ruleSet);
            $value = $data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return $this->errors;
    }

    /**
     * Appliquer une règle de validation
     */
    private function applyRule(string $field, $value, string $rule): void
    {
        // Required
        if ($rule === 'required' && (empty($value) && $value !== '0')) {
            $this->addError($field, "Le champ '$field' est obligatoire");
            return;
        }

        // Si le champ est vide et non requis, on arrête la validation
        if (empty($value) && $value !== '0') {
            return;
        }

        // Min length
        if (strpos($rule, 'min:') === 0) {
            $min = (int)substr($rule, 4);
            if (strlen($value) < $min) {
                $this->addError($field, "Le champ '$field' doit contenir au moins $min caractères");
            }
            return;
        }

        // Max length
        if (strpos($rule, 'max:') === 0) {
            $max = (int)substr($rule, 4);
            if (strlen($value) > $max) {
                $this->addError($field, "Le champ '$field' doit contenir au maximum $max caractères");
            }
            return;
        }

        // Email
        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Le champ '$field' doit être un email valide");
            return;
        }

        // Numeric
        if ($rule === 'numeric' && !is_numeric($value)) {
            $this->addError($field, "Le champ '$field' doit être un nombre");
            return;
        }

        // Phone (format burundais simplifié)
        if ($rule === 'phone') {
            $pattern = '/^[0-9]{8,15}$/';
            if (!preg_match($pattern, preg_replace('/[^0-9]/', '', $value))) {
                $this->addError($field, "Le numéro de téléphone est invalide");
            }
            return;
        }

        // Date
        if ($rule === 'date') {
            $d = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                $this->addError($field, "Le champ '$field' doit être une date valide (YYYY-MM-DD)");
            }
            return;
        }

        // In (valeur dans une liste)
        if (strpos($rule, 'in:') === 0) {
            $allowed = explode(',', substr($rule, 3));
            if (!in_array($value, $allowed)) {
                $this->addError($field, "Le champ '$field' doit être parmi : " . implode(', ', $allowed));
            }
            return;
        }
    }

    /**
     * Ajouter une erreur
     */
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Vérifier si la validation a échoué
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Récupérer toutes les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Nettoyer une chaîne (protection XSS)
     */
    public function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}