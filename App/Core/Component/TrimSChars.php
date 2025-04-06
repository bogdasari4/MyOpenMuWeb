<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

 namespace App\Core\Component;

 /** 
  * Static function, converts special characters to HTML entities and preserves spaces (or other characters) at the beginning and end of the string.
  *
  * @author Bogdan Reva <tip-bodya@yandex.com>
  */

  trait TrimSChars
  {
    /**
     * Static function, converts special characters to HTML entities and preserves spaces (or other characters) at the beginning and end of the string.
     * @param string $string
     * String to convert.
     * @param int $flag
     * A bitmask of one or more of the following flags, which specify how to handle quotes, 
     * invalid code unit sequences and the used document type. The default is ENT_QUOTES | ENT_HTML5.
     * @return string
     * Returns the converted string.
     * @see https://www.php.net/manual/en/function.trim.php, https://www.php.net/manual/en/function.htmlspecialchars.php
     */
    private function trimSChars(?string $string, int $flag = ENT_QUOTES | ENT_HTML5): ?string
    {
        if ($string === null)
            return null;
        
        $string = htmlspecialchars($string, $flag);
        $string = trim($string);

        return $string;
    }
  }