<?php


namespace Desafio\Client;

use GuzzleHttp\Client as guzzClient;

class AuthorizationCliente {     

    public function verificarAutorizadorExterno($json){
        $url = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        $client = new guzzClient(array('base_uri' => $url));
        $response = $client->request('GET', '', ['body' => $json, 'http_errors' => false]);
        return json_decode($response->getBody()->getContents(),true);
    }



}
