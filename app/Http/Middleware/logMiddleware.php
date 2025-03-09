<?php



namespace App\Http\Middleware;



use Closure;

use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;

use App\Models\log;

use Illuminate\Support\Facades\Auth;

use URL;



class logMiddleware

{

    /**

     * Handle an incoming request.

     *

     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next

     */

    public function handle(Request $request, Closure $next): Response
    {
        $data=new log();
        $data->user_id=Auth::user()->id;
        $data->user_name=Auth::user()->name;
        $data->IP_address=getHostByName(getHostName());
        $data->section= basename(URL::current());
        $url=explode("/",URL::current());
        $data->url=URL::current();
        $data->save();
        return $next($request);

    }

}

