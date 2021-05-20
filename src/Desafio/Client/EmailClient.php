<?php


namespace Desafio\Client;

use GuzzleHttp\Client as guzzClient;

class EmailClient { 
    public function enviarEmailDestino($json){
        $url = 'http://o4d9z.mocklab.io/notify';
        $client = new guzzClient(array('base_uri' => $url));
        $response = $client->request('GET', '', ['body' => $json, 'http_errors' => false]);
        return json_decode($response->getBody()->getContents(),true);
    }
}
