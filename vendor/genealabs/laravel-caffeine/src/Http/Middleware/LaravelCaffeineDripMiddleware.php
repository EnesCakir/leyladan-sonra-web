<?php namespace GeneaLabs\LaravelCaffeine\Http\Middleware;

use Closure;

class LaravelCaffeineDripMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $content = null;
        $response = $next($request);

        if (! method_exists($response, 'getOriginalContent')) {
            return $response;
        }

        $content = $response->getOriginalContent();

        if (method_exists($content, 'render')) {
            $content = $content->render();
        }

        if (is_string($content)
            && (strpos($content, '_token')
                || (preg_match("/\<meta name=[\"\']csrf[_-]token[\"\']/", $content)))) {
            $newContent = "<script>setInterval(function(){";
            $newContent .= "var e=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject('Microsoft.XMLHTTP');";
            $newContent .= "e.open('GET','" . url('/genealabs/laravel-caffeine/drip') . "',!0);";
            $newContent .= "e.setRequestHeader('X-Requested-With','XMLHttpRequest');";
            $newContent .= "e.send();},";
            $newContent .= config('genealabs-laravel-caffeine.dripIntervalInMilliSeconds', 300000);
            $newContent .= ");</script></body>";
            $content = str_replace('</body>', $newContent, $content);
            $response->setContent($content);
        }

        return $response;
    }
}
