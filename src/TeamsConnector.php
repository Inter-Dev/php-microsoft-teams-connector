<?php

namespace Sebbmyr\Teams;

use Sebbmyr\Teams\Exception\UnexpectedTeamsResult;

/**
 * Teams connector
 */
class TeamsConnector
{
    private $webhookUrl;
    private ?string $lastResult;

    public function __construct($webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * Sends card message as POST request
     *
     * @param  TeamsConnectorInterface $card
     * @param  int $curlOptTimeout by default = 10
     * @param  int $curlOptConnectTimeout by default = 3
     * @throws \Exception|UnexpectedTeamsResult
     */
    public function send(TeamsConnectorInterface $card, $curlOptTimeout = 10, $curlOptConnectTimeout = 3)
    {
        $json = json_encode($card->getMessage());

        $ch = curl_init($this->webhookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $curlOptTimeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $curlOptConnectTimeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);

        $this->lastResult = curl_exec($ch);

        if (curl_error($ch)) {
            throw new \Exception(curl_error($ch), curl_errno($ch));
        }
        if ($this->lastResult !== "1") {
            throw new UnexpectedTeamsResult('Error response: ' . $this->lastResult);
        }
    }

    /**
     * @return string|null
     */
    public function getLastResult(): ?string
    {
        return $this->lastResult;
    }
}
