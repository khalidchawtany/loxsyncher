<?php

namespace App\Http\Middleware;

use App\Exceptions\AjaxException;
use Closure;
use Illuminate\Support\Facades\Auth;

class AddUserIdAttribute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (
            config('app.archived')
            && !$request->isMethod('get')
            && !$request->is('login')
        ) {
            throw new AjaxException('System is archived. No modifications can be made!');
        }

        $publicIp = config('app.public_ip');

        if (!empty($publicIp)) {
            if (!starts_with($request->ip(), '192.168') && $request->ip() != $publicIp) {
                if (!Auth::user()->external_view) {
                    echo '<div style="display: flex;  min-height: 60vh;align-items:center; justify-content:center; ">
                                <h3 style="flex:1; color:red; font-weight: bold; text-align: center;" >
                                        You must be on-site to use this system!
                                    </h3>
                    </div>';
                    echo '"' . $request->ip() . '"';
                    echo '"' . $request->ip() != $publicIp . '"';
                    exit();
                }

                if (
                    !Auth::user()->external_update
                    && !$request->isMethod('get')
                    && !$request->is('login')
                ) {
                    echo '<div style="display: flex;  min-height: 60vh;align-items:center; justify-content:center; ">
                                <h3 style="flex:1; color:red; font-weight: bold; text-align: center;" >
                                        Modifications can be made only when on-site!
                                    </h3>
                    </div>';
                    echo '"' . $request->ip() . '"';
                    exit();
                }
            }
        }

        $user_id = Auth::user()->id;
        $request->merge(['user_id' => $user_id]);

        return $next($request);
    }
}
