<?php

/*
 * This file is part of OAuth 2.0 Laravel.
 *
 * (c) Luca Degasperi <packages@lucadegasperi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LucaDegasperi\OAuth2Server\Middleware;

use App\Http\Controllers\Api\BController;
use App\Libs\ErrorInfo;
use Closure;
use LucaDegasperi\OAuth2Server\Authorizer;

/**
 * This is the check auth code request middleware class.
 *
 * @author Luca Degasperi <packages@lucadegasperi.com>
 */
class CheckAuthCodeRequestMiddleware extends BController
{
    /**
     * The authorizer instance.
     *
     * @var \LucaDegasperi\OAuth2Server\Authorizer
     */
    protected $authorizer;

    /**
     * Create a new check auth code request middleware instance.
     *
     * @param \LucaDegasperi\OAuth2Server\Authorizer $authorizer
     */
    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->authorizer->setRequest($request);
            $this->authorizer->checkAuthCodeRequest();

            return $next($request);
        } catch (\Exception $e) {
            //Oauth2 系统错误格式：{"httpStatusCode":400,"errorType":"invalid_request","redirectUri":null,"parameter":"code"}
            if (isset($e) && !empty($e)) {
                return $this->responseFail("[" . $e->parameter . "]" . $e->errorType, 2500);
            } else {
                return $this->responseFail(ErrorInfo::Errors(2000), 2000);
            }
        }
    }
}
