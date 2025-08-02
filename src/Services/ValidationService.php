<?php
// src/Services/ValidationService.php

namespace App\Services;

class ValidationService
{
    public static function validate(array $data, array $rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $ruleSet);

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && is_null($value)) {
                    $errors[$field][] = 'Field is required';
                }

                if (str_starts_with($rule, 'max:')) {
                    $limit = (int) substr($rule, 4);
                    if (strlen($value) > $limit) {
                        $errors[$field][] = "Maximum length is {$limit}";
                    }
                }

                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = 'Must be numeric';
                }
            }
        }

        return $errors;
    }
}
