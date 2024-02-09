<?php

namespace App;

final class Alert extends \Exception
{
    private string $hexcode;

    private string $type;

    private array $alertList;

    public function __construct(int $hexcode, string $type = '', string $redirect = '', \Throwable $previous = null)
    {
        parent::__construct('', 0, $previous);

        if (defined('__CONFIG_LANGUAGE_SET') && defined('__ROOT_APP_JSON_LANG')) {
            $this->loadAlertFile();
        }

        $this->hexcode = dechex($hexcode);
        $this->type = $type;
        if ($redirect)
            header('Refresh:3; url=' . $redirect);
    }

    private function loadAlertFile(): void
    {
        $languageCode = isset($_COOKIE['language_code']) ? $_COOKIE['language_code'] : __CONFIG_LANGUAGE_SET;
        $pathFile = __ROOT_APP_JSON_LANG . $languageCode . DIRECTORY_SEPARATOR . 'Alert.json';
        if (file_exists($pathFile)) {
            $alertFile = json_decode(file_get_contents($pathFile), true);
            if ($alertFile != null) {
                $this->alertList = $alertFile;
            }
        }
    }

    public function getCalloutTemplate(): string
    {
        $type = match ($this->type) {
            'info' => 'color: #055160; background-color: #cff4fc; border-left: 0.25rem solid #9eeaf9;',
            'success' => 'color: #0a3622; background-color: #d1e7dd; border-left: 0.25rem solid #a3cfbb;',
            'warning' => 'color: #664d03; background-color: #fff3cd; border-left: 0.25rem solid #ffe69c;',
            'danger' => 'color: #58151c; background-color: #f8d7da; border-left: 0.25rem solid #f1aeb5;',
            default => 'color: inherit; background-color: #f8f9fa; border-left: 0.25rem solid #dee2e6;'
        };

        $callout = '<div style="padding: 1.25rem; margin-top: 1.25rem; margin-bottom: 1.25rem; ' . $type . '">';
        $callout .= $this->getRawMessage();
        $callout .= '</div>';

        return $callout;
    }

    public function getRawMessage(): string
    {
        if ($this->alertList && isset($this->alertList[$this->hexcode]))
            return $this->alertList[$this->hexcode];
        return $this->hexcode;
    }

    public function getRawCode(): string
    {
        return $this->hexcode;
    }

    public function getRawType(): string
    {
        return $this->type;
    }
}