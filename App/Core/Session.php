<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

namespace App\Core;

/**
 * Session management class.
 * 
 * @author Bogdan Reva <tip-bodya@yandex.com>
 */
final class Session 
{
    /**
     * Reading session variable data.
     * @param string $key
     * Variable key.
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return @$_SESSION[$key];
    }

    /**
     * Writing data to a session variable.
     * @param string $key
     * Variable key.
     * @param mixed $value
     * Data to be recorded.
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Determine if a variable is declared.
     * @param mixed $key
     * Variable key.
     * @return bool
     */
    public function __isset($key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Start new or resume existing session.
     * @see https://www.php.net/manual/en/function.session-start.php
     * @return bool
     */
    public function start(): bool
    {
        if (!$this->status()) {
            session_start();
            return true;
        }

        return false;
    }
    /**
     * Destroys all of the data associated with the current session. 
     * It does not unset any of the global variables associated with the session, 
     * or unset the session cookie. To use the session variables again, start() has to be called.
     * @see https://www.php.net/manual/en/function.session-destroy.php
     * @return void
     */
    public function destruct(): bool
    {
        if($this->status())
            return session_destroy();
        return false;
    }

    /**
     * Returns the current session status.
     * @see https://www.php.net/manual/ru/function.session-status.php
     * @return bool
     */
    public function status(): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE)
            return true;
        return false;
    }
}

?>