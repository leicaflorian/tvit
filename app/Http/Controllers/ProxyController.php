<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ProxyController extends Controller {

    private function filterHeaders($headers): array {
        $allowedHeaders = ['accept', 'content-type'];

        return array_filter($headers, function ($key) use ($allowedHeaders) {
            return in_array(strtolower($key), $allowedHeaders);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function proxy(Request $request, $path) {

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri'    => $request->query("base_uri"), // public dummy API for example
            // You can set any number of default request options.
            'timeout'     => 60.0,
            'http_errors' => false, // disable guzzle exception on 4xx or 5xx response code
        ]);

        // create request according to our needs. we could add
        // custom logic such as auth flow, caching mechanism, etc
        $resp = $client->request($request->method(), $path, [
            'headers' => $this->filterHeaders($request->header()),
            'query'   => $request->query(),
            'body'    => $request->getContent(),
        ]);

        // recreate response object to be passed to actual caller
        // according to our needs.
        return response($resp->getBody()->getContents(), $resp->getStatusCode())
            ->withHeaders($this->filterHeaders($resp->getHeaders()));
    }
}
