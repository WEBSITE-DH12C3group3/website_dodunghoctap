<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ETagMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Chỉ GET/HEAD 200 + có content
        if (!$request->isMethodSafe() || $response->getStatusCode() !== 200) return $response;
        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) return $response;
        if (!method_exists($response, 'getContent')) return $response;

        $etag = '"' . md5($response->getContent()) . '"';
        $response->headers->set('ETag', $etag);
        // Không cache HTML, chỉ revalidate để có thể trả 304
        $response->headers->set('Cache-Control', 'no-cache, private');

        $ifNoneMatch = $request->headers->get('If-None-Match');
        if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
            $response->setNotModified();
        }
        return $response;
    }
}
