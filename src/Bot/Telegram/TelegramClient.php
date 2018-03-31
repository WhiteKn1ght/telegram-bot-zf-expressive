<?php
namespace Bot\Telegram;

use Zend\Http\Client;
use Zend\Http\Response;

/**
 *
 * @author KainLegend
 *
 */
class TelegramClient extends Client
{
    /**
     * @const string Telegram Bot API URL.
     */
    const BASE_BOT_URL = 'https://api.telegram.org/bot';
    /**
     * @const int The timeout in seconds for a request that contains file uploads.
     */
    const DEFAULT_FILE_UPLOAD_REQUEST_TIMEOUT = 3600;
    /**
     * @const int The timeout in seconds for a request that contains video uploads.
     */
    const DEFAULT_VIDEO_UPLOAD_REQUEST_TIMEOUT = 7200;


   /**
     * Returns the base Bot URL.
     *
     * @return string
     */
    public function getBaseBotUrl()
    {
        return static::BASE_BOT_URL;
    }
    /**
     * Prepares the API request for sending to the client handler.
     *
     * @param TelegramRequest $request
     *
     * @return array
     */
    public function prepareRequest(TelegramRequest $request)
    {
        $url = $this->getBaseBotUrl().$request->getAccessToken().'/'.$request->getEndpoint();
        return [
            $url,
            $request->getMethod(),
            $request->getHeaders(),
            $request->isAsyncRequest(),
        ];
    }
    /**
     * Send an API request and process the result.
     *
     * @param TelegramRequest $request
     *
     * @throws TelegramSDKException
     *
     * @return TelegramResponse
     */
    public function sendRequest(TelegramRequest $request)
    {
        list($url, $method, $headers, $isAsyncRequest) = $this->prepareRequest($request);
        $this->setUri($url/* . '?text=test&chat_id=99339886' */)->setOptions([
            'timeout' => $request->getTimeOut(),
            'connecttimeout' => $request->getConnectTimeOut(),
            'useragent' => 'TelegramBot'
        ])->setMethod($request->getMethod());
        $this->setParameterPost($request->getParams());

        $rawResponse = $this->send();
        $returnResponse = $this->getTelegramResponse($request, $rawResponse);
        if ($returnResponse->isError()) {
            throw $returnResponse->getThrownException();
        }
        return $returnResponse;
    }
    /**
     * Creates response object.
     *
     * @param TelegramRequest                    $request
     * @param ResponseInterface|Response $response
     *
     * @return TelegramResponse
     */
    protected function getTelegramResponse(TelegramRequest $request, $response)
    {
        return new TelegramResponse($request, $response);
    }
    /**
     * @param \Telegram\Bot\TelegramRequest $request
     * @param $method
     * @return array
     */
    private function getOptions(TelegramRequest $request, $method)
    {
        if ($method === 'POST') {
            return $request->getPostParams();
        }
        return ['query' => $request->getParams()];
    }
}

