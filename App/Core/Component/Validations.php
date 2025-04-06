<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

 namespace App\Core\Component;

 /**
  * @author Bogdan Reva <tip-bodya@yandex.com>
  */
 trait Validations
 {
        /**
     * Match a string using regular expression.
     * @param string $string
     * The string to be checked.
     * @return bool
     */
    private function validateRegularExpression(string $string): bool
    {
        if (preg_match("/^[A-Za-z0-9]+$/", $string))
            return true;
        return false;
    }

    /**
     * Get string length.
     * @param string $value
     * The string to be checked.
     * @param int $min
     * Minimum length.
     * @param int $max
     * Maximum length.
     * @return bool
     */
    private function validateStringLength(string $value, int $min, int $max): bool
    {
        if (strlen($value) < $min)
            return false;

        if (strlen($value) > $max)
            return false;

        return true;
    }

    /**
     * Validates whether the value is a valid e-mail address.
     * In general, this validates e-mail addresses against the addr-specsyntax in » RFC 822.
     * `https://www.php.net/manual/en/filter.filters.validate.php`
     * Checking if a domain exists.
     * @param string $value
     * The string to be checked.
     * @return bool
     */
    private function validateEmail(string $email): bool
    {
        $email = trim($email);

        if (empty($email) || !is_string($email))
            return false;

        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
            return false;

        $domain = substr(strrchr($email, '@'), 1);
        if(!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) 
            return false;
        
        return true;
    }
 }