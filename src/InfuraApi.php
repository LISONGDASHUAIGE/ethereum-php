<?php

/**
 * author: NanQi
 * datetime: 2019/7/3 17:53
 */

namespace Ethereum;

class InfuraApi implements ProxyApi
{
    protected $apiKey;

    function __construct(string $apiKey, string $network = 'mainnet')
    {
        $this->apiKey = $apiKey;
        $this->network = $network;
    }

    public function send($method, $params = [])
    {
        $url = "https://mainnet.infura.io/v3/{$this->apiKey}";

        $arr = array_map(function ($item) {
            if (is_array($item)) {
                return json_encode($item);
            } else {
                return '"' . $item . '"';
            }
        }, $params);
        $strParams = implode(",", $arr);
        $data_string = <<<data
{"jsonrpc":"2.0","method":"{$method}","params": [$strParams],"id":1}
data;
        $res = Utils::httpRequest('POST', $url, [
            'body' => $data_string
        ]);

        if (isset($res['result'])) {
            return $res['result'];
        } else {
            return false;
        }
    }

    function gasPrice()
    {
        return $this->send('eth_gasPrice');
    }

    function ethBalance(string $address, int $decimals = 16)
    {
        $balance = $this->send('eth_getBalance', ['address' => $address, 'latest']);
        return Utils::toDisplayAmount($balance, $decimals);
    }

    function receiptStatus(string $txHash): bool
    {
        // TODO: Implement receiptStatus() method.
    }

    function sendRawTransaction($raw)
    {
        return $this->send('eth_sendRawTransaction', ['hex' => $raw]);
    }

    function getNonce(string $address)
    {
        return $this->send('eth_getTransactionCount', ['address' => $address, 'latest']);
    }

    function getTransactionReceipt(string $txHash)
    {
        // TODO: Implement getTransactionReceipt() method.
    }

    function getNetwork(): string
    {
        return $this->network;
    }

    function ethCall($params): string
    {
        return $this->send('eth_call', ['params' => $params, 'latest']);
    }
}
