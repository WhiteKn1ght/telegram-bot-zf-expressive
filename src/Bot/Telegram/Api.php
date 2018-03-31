<?php
namespace Bot\Telegram;

use Bot\Exceptions\BotCommonException;
use Bot\Telegram\Objects\Update;
use Bot\Telegram\Keyboard\Keyboard;
use Bot\Telegram\Objects\Message;

/**
 *
 * @author rusakov.vv
 *
 */

/**
 * Class Api.
 */
class Api
{

    /**
     *
     * @var string Version number of the Telegram Bot PHP SDK.
     */
    const VERSION = '0.0.1';

    /**
     *
     * @var string The name of the environment variable that contains the Telegram Bot API Access Token.
     */
    const BOT_TOKEN_ENV_NAME = 'TELEGRAM_BOT_TOKEN';

    /**
     *
     * @var TelegramClient The Telegram client service.
     */
    protected $client;

    /**
     *
     * @var string Telegram Bot API Access Token.
     */
    protected $accessToken = null;

    /**
     *
     * @var TelegramResponse|null Stores the last request made to Telegram Bot API.
     */
    protected $lastResponse;

    /**
     *
     * @var bool Indicates if the request to Telegram will be asynchronous (non-blocking).
     */
    protected $isAsyncRequest = false;

    /**
     *
     * @var CommandBus|null Telegram Command Bus.
     */
    protected $commandBus = null;

    /**
     *
     * @var Container IoC Container
     */
    protected static $container = null;

    /**
     * Timeout of the request in seconds.
     *
     * @var int
     */
    protected $timeOut = 60;

    /**
     * Connection timeout of the request in seconds.
     *
     * @var int
     */
    protected $connectTimeOut = 10;

    /**
     * Instantiates a new Telegram super-class object.
     *
     *
     * @param string $token
     *            The Telegram Bot API Access Token.
     * @param bool $async
     *            (Optional) Indicates if the request to Telegram
     *            will be asynchronous (non-blocking).
     * @param HttpClientInterface $httpClientHandler
     *            (Optional) Custom HTTP Client Handler.
     *
     * @throws TelegramSDKException
     */
    public function __construct($token = null, $async = false, $httpClientHandler = null)
    {
        $this->accessToken = isset($token) ? $token : getenv(static::BOT_TOKEN_ENV_NAME);
        if (! $this->accessToken) {
            throw new BotCommonException('Required "token" not supplied in config and could not find fallback environment variable "' . static::BOT_TOKEN_ENV_NAME . '"');
        }
        if (isset($async)) {
           # $this->setAsyncRequest($async);
        }
        $this->client = new TelegramClient();

       # $this->commandBus = new CommandBus($this);
    }

    /**
     * Returns a webhook update sent by Telegram.
     * Works only if you set a webhook.
     *
     * @see setWebhook
     *
     * @return Update
     */
    public function getWebhookUpdate()
    {
        $body = json_decode(file_get_contents('php://input'), true);
        #$update = new Update($body);
        return $body;
    }

    /**
     * Returns Telegram Bot API Access Token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Returns the TelegramClient service.
     *
     * @return TelegramClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the last response returned from API request.
     *
     * @return TelegramResponse
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Sets the bot access token to use with API requests.
     *
     * @param string $accessToken The bot access token to save.
     *
     * @throws \InvalidArgumentException
     *
     * @return Api
     */
    public function setAccessToken($accessToken)
    {
        if (is_string($accessToken)) {
            $this->accessToken = $accessToken;
            return $this;
        }
        throw new \InvalidArgumentException('The Telegram bot access token must be of type "string"');
    }

    /**
     * Make this request asynchronous (non-blocking).
     *
     * @param bool $isAsyncRequest
     *
     * @return Api
     */
    public function setAsyncRequest($isAsyncRequest)
    {
        $this->isAsyncRequest = $isAsyncRequest;
        return $this;
    }

    /**
     * Check if this is an asynchronous request (non-blocking).
     *
     * @return bool
     */
    public function isAsyncRequest()
    {
        return $this->isAsyncRequest;
    }

    /**
     * @return int
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }
    /**
     * @param int $timeOut
     *
     * @return $this
     */
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;
        return $this;
    }
    /**
     * @return int
     */
    public function getConnectTimeOut()
    {
        return $this->connectTimeOut;
    }
    /**
     * @param int $connectTimeOut
     *
     * @return $this
     */
    public function setConnectTimeOut($connectTimeOut)
    {
        $this->connectTimeOut = $connectTimeOut;
        return $this;
    }

    /**
     * Builds a custom keyboard markup.
     *
     * <code>
     * $params = [
     *   'keyboard'          => '',
     *   'resize_keyboard'   => '',
     *   'one_time_keyboard' => '',
     *   'selective'         => '',
     * ];
     * </code>
     *
     * @link       https://core.telegram.org/bots/api#replykeyboardmarkup
     *
     * @param array $params
     *
     * @var array   $params ['keyboard']
     * @var bool    $params ['resize_keyboard']
     * @var bool    $params ['one_time_keyboard']
     * @var bool    $params ['selective']
     *
     * @return string
     */
    public function replyKeyboardMarkup(array $params)
    {
        return Keyboard::create($params);
    }

    /**
     * Send text messages.
     *
     * <code>
     * $params = [
     * 'chat_id' => '',
     * 'text' => '',
     * 'parse_mode' => '',
     * 'disable_web_page_preview' => '',
     * 'disable_notification' => '',
     * 'reply_to_message_id' => '',
     * 'reply_markup' => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#sendmessage
     *
     * @param array $params
     *
     * @var int|string $params ['chat_id']
     * @var string $params ['text']
     * @var string $params ['parse_mode']
     * @var bool $params ['disable_web_page_preview']
     * @var bool $params ['disable_notification']
     * @var int $params ['reply_to_message_id']
     * @var string $params ['reply_markup']
     *
     * @throws TelegramSDKException
     *
     * @return Message
     */
    public function sendMessage(array $params)
    {
        $response = $this->post('sendMessage', $params);
        return new Message($response->getDecodedBody());
    }

    /**
     * Sends a POST request to Telegram Bot API and returns the result.
     *
     * @param string $endpoint
     * @param array $params
     * @param bool $fileUpload
     *            Set true if a file is being uploaded.
     *
     * @return TelegramResponse
     */
    protected function post($endpoint, array $params = [], $fileUpload = false)
    {
        if ($fileUpload) {
            $params = [
                'multipart' => $params
            ];
        } else {
            if (array_key_exists('reply_markup', $params)) {
                $params['reply_markup'] = json_encode($params['reply_markup']->toArray());
            }
            /* $params = [
                'form_params' => $params
            ]; */
        }
        return $this->sendRequest('POST', $endpoint, $params);
    }

    /**
     * Sends a request to Telegram Bot API and returns the result.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     *
     * @throws BotCommonException
     *
     * @return TelegramResponse
     */
    protected function sendRequest($method, $endpoint, array $params = [])
    {
        $request = $this->request($method, $endpoint, $params);
        return $this->lastResponse = $this->client->sendRequest($request);
    }

    /**
     * Instantiates a new TelegramRequest entity.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     *
     * @return TelegramRequest
     */
    protected function request($method, $endpoint, array $params = [])
    {
        return new TelegramRequest($this->getAccessToken(), $method, $endpoint, $params, $this->isAsyncRequest(), $this->getTimeOut(), $this->getConnectTimeOut());
    }

}

