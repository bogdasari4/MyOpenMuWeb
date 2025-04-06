<?php
/**
 * MyOpenMuWeb
 * @see https://github.com/bogdasari4/MyOpenMuWeb
 */

 namespace App\Core\Component;

 /** 
  * Page redirection function with delay option.
  * @author Bogdan Reva <tip-bodya@yandex.com>
  */
trait RedirectTo
{
    /**
     * Page redirection function with delay option.
     * @param string $page
     * Redirect to the specified page.
     * @param int $delay
     * When specifying `$delay` greater than 0, redirection to the page is triggered after $time seconds.
     * @return never
     */
    private function redirectTo(string $page = '/', int $delay = 0): never
    {
        if ($delay > 0) {
            header('Refresh:' . $delay . '; url=' . $page);
            exit;
        }

        header('Location:' . $page);
        exit;
    }
}