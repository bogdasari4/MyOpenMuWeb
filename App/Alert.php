<?php

/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App;

/**
 * A notification class that uses an extending exception class.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Alert extends \Exception
{

    use Assistant;

    private array $alertList;

    /**
     * Summary of __construct
     * @param int $hexcode
     * Alert code.
     * @param string $type
     * Message type.
     * `info`, `success`, `warning`, `danger`.
     * @param string $redirect
     * Redirect to page.
     * @param \Throwable|null $previous
     */
    public function __construct(private int $hexcode, private string $type = '', string $redirect = '', \Throwable $previous = null)
    {
        parent::__construct('', 0, $previous);

        if (defined('__CONFIG_LANGUAGE_SET') && defined('__ROOT_APP_JSON_LANG'))
            $this->loadAlertFile();


        if ($redirect)
            header('Refresh:3; url=' . $redirect);
    }

    /**
     * loading alert sheet.
     */
    private function loadAlertFile(): void
    {
        $pathFile = __ROOT_APP_JSON_LANG . $this->getLanguageCode() . DIRECTORY_SEPARATOR . 'Alert.json';
        if (file_exists($pathFile)) {
            $alertFile = json_decode(file_get_contents($pathFile), true);
            if ($alertFile != null) {
                $this->alertList = $alertFile;
            }
        }
    }

    /**
     * We display a ready-made HTML/CSS alert template with a message.
     * @return string
     */
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

    /**
     * Return the message string. 
     * If there is no starkey in the array, return `hexcode`.
     * @return string
     */
    public function getRawMessage(): string
    {
        $hexcode = $this->getRawCode();
        if ($this->alertList && isset($this->alertList[$hexcode]))
            return $this->alertList[$hexcode];
        return $hexcode;
    }

    /**
     * Return `hexcode`.
     * @return string
     */
    public function getRawCode(): string
    {
        return dechex($this->hexcode);
    }

    /**
     * Return the message type.
     * `info`, `success`, `warning`, `danger`.
     * @return string
     */
    public function getRawType(): string
    {
        return $this->type;
    }
}
