<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core\Auth;

/**
 * Class for validating data during registration.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
class Validation
{
    /**
     * Match a string using regular expression.
     * @param string $string
     * The string to be checked.
     * @return bool
     */
    public function regularExpression(string $string): bool
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
    public function stringLength(string $value, int $min, int $max): bool
    {
        if (strlen($value) < $min)
            return false;

        if (strlen($value) > $max)
            return false;

        return true;
    }

    /**
     * Validates whether the value is a valid e-mail address.
     * In general, this validates e-mail addresses against the addr-specsyntax in » RFC 822,
     * with the exceptions that comments and whitespace folding and dotless domain names are not supported.
     * `https://www.php.net/manual/en/filter.filters.validate.php`
     * @param string $value
     * The string to be checked.
     * @return bool
     */
    public function email(string $value): bool
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL))
            return true;
        return false;
    }

}

?>