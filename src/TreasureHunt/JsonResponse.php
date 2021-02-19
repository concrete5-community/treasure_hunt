<?php  
namespace TreasureHunt;

use Cookie;
use Core;
use Config;

/**
 * Class JsonResponse
 * @package Concrete\Package\TreasureHunt\Src\TreasureHunt
 *
 * We want to use the JsonResponse class, but the cookies are only stored in the normal Response class.
 * See also: https://github.com/concrete5/concrete5/issues/1644
 */
class JsonResponse extends \Symfony\Component\HttpFoundation\JsonResponse {
    public function send() {
        $cleared = Cookie::getClearedCookies();
        foreach($cleared as $cookie) {
            $this->headers->clearCookie($cookie);
        }
        $cookies = Cookie::getCookies();
        foreach($cookies as $cookie) {
            $this->headers->setCookie($cookie);
        }

        if ($this->headers->has('X-Frame-Options') === false) {
            $x_frame_options = Config::get('concrete.security.misc.x_frame_options');
            if (Core::make('helper/validation/strings')->notempty($x_frame_options)) {
                $this->headers->set('X-Frame-Options', $x_frame_options);
            }
        }

        parent::send();
    }
}