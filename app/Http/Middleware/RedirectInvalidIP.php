<?php

namespace App\Http\Middleware;

use Closure;

class RedirectInvalidIP
{
    protected $addresses;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $config          = config('apicheck.use') === 'local' ? 'local': 'heroku';
        $this->addresses = config("apicheck.{$config}");

        foreach ($request->getClientIps() as $address) {
            if ( ! $this->isValidIP($address) ) {
                return response('Forbidden.', 403);
            }
        }

        return $next($request);
    }

    protected function isValidIP($address)
    {
        return in_array($address, $this->addresses);
    }
}
