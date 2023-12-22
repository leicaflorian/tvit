<?php

namespace App\Classes;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProxiedHttp {
  private array $availableProxies = [
    "it" => "",
    "ro" => ""
  ];
  private Client $client;
  
  function __construct(string $country) {
    $this->availableProxies["it"] = env("PROXY_IT");
    $this->availableProxies["ro"] = env("PROXY_ro");
    
    $this->client = new Client([
      "proxy"  => [
        'https' => $this->availableProxies[$country],
      ],
      'verify' => false
    ]);
  }
  
  function call($url, $method, $options) {
    $resp = $this->client->request($method, $url, $options);
    
    Log::info("ProxiedHttp: " . $resp->getStatusCode() . " " . $url);
    
    $body = $resp->getBody()->getContents();
    
    // se header application/json allora ritorna json
    if (Str::contains($resp->getHeader("Content-Type")[0], "application/json")) {
      return json_decode($body, true);
    }
    
    return $body;
  }
  
  static function get($country, $url, $options = []) {
    $client = new ProxiedHttp($country);
    
    return $client->call($url, "GET", $options);
  }
  
  
}
