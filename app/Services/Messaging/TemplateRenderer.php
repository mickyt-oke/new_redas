<?php

namespace App\Services\Messaging;

class TemplateRenderer
{
    /**
     * Fast placeholder renderer:
     * {{ key }} or {{key}} -> variables[key]
     */
    public function render(string $template, array $variables): string
    {
        $result = $template;

        foreach ($variables as $key => $value) {
            $safeKey = (string) $key;

            $search1 = '{{ ' . $safeKey . ' }}';
            $search2 = '{{' . $safeKey . '}}';
            $search3 = '{{' . $safeKey . ' }}';
            $search4 = '{{ ' . $safeKey . '}}';

            $valueStr = is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE);

            $result = str_replace([$search1, $search2, $search3, $search4], $valueStr, $result);
        }

        return (string) $result;
    }
}
