<?php

namespace InnovationSandbox\Sterling\Common;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InnovationSandbox\Common\Utils\ErrorHandler;
use InnovationSandbox\Common\HttpRequest;

class ModuleRequest
{
    private $client,
        $response,
        $http;

    public function __construct(Client $client = null)
    {
        $this->client = $client ? $client : new Client();
        $this->http = new HttpRequest($this->client);
    }


    public function trigger($host = '', $method = 'POST', $payload = '', $params = '', $credentials)
    {
        try {
            $Appid = isset($credentials['Appid']) ? $credentials['Appid'] : '';
            $ipval = isset($credentials['ipval']) ? $credentials['ipval'] : '';

            $headers = [
                'Sandbox-Key' => $credentials['sandbox_key'],
                'Ocp-Apim-Subscription-Key' => $credentials['subscription_key'],
                'Ocp-Apim-Trace' => 'true',
                'Appid' => $Appid,
                'Content-Type' => 'application/json',
                'ipval' => $ipval
            ];

            $requestData = [
                'headers' => $headers,
                'body' => json_encode($payload),
                'query' => $params
            ];

            $this->response = $this->http->request([
                'host' => $host,
                'path' => $credentials['path'],
                'method' => $method,
                'requestData' => $requestData
            ]);

            if (gettype($this->response) === 'array') {
                return $this->response;
            }

            return $this->response->getBody()->getContents();
        } catch (RequestException $error) {
            return ErrorHandler::apiError($error);
        } catch (\Exception $error) {
            return ErrorHandler::moduleError($error);
        }
    }
}
