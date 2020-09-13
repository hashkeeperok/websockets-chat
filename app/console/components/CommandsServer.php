<?php
namespace console\components;

use consik\yii2websocket\events\WSClientEvent;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;
use frontend\models\ChatMessage;

class CommandsServer extends WebSocketServer
{

    /**
     * @var array
     */
    private $clientsMessages = [];

    /**
     * @var CommandsServer
     */
    private static $_instance = null;

    /**
     * @var bool
     */
    private $connected;

    public function init()
    {
        parent::init();

        $this->port = 1024;

        $this->on(self::EVENT_CLIENT_CONNECTED, function(WSClientEvent $e) {
            $e->client->name = null;
        });

        $this->on(WebSocketServer::EVENT_WEBSOCKET_OPEN_ERROR, function($e) {
            echo "Error opening port " . $this->port . "\n";
            $this->port += 1;
            $this->start();
        });

        $this->on(WebSocketServer::EVENT_WEBSOCKET_OPEN, function($e) {
            echo "Server started at port " . $this->port . "\n";
            $this->connected = true;
        });

        $this->on(WebSocketServer::EVENT_WEBSOCKET_CLOSE, function($e) {
            echo "Server stopped"  . "\n";
            $this->connected = false;
        });
    }

    /**
     * Gets instance of CommandsServer.
     *
     * @return CommandsServer
     */
    static public function getInstance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Gets connection status.
     *
     * @return bool
     */
    public function isConnected() {
        return $this->connected;
    }

    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
    }

    /**
     * Add message to database.
     *
     * @param string $text
     *   The message text.
     * @param string $username
     *   The client username.
     */
    protected function addMessage($text, $username) {
        $message = new ChatMessage();
        $message->text = $text;
        $message->created_at = time();
        $message->username = $username;
        $message->save();
    }

    public function commandChat(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);
        $result = ['message' => ''];

        if (!$client->name) {
            $result['message'] = 'Напишите имя!';
        } elseif (!empty($request['message']) && $message = trim($request['message']) ) {
            try {
                $message_array = [
                    'type' => 'chat',
                    'from' => $client->name,
                    'message' => $message,
                    'time' => gmdate("H:i:s", time())
                ];
                $this->clientsMessages[] = $message_array;
                foreach ($this->clients as $chatClient) {
                    $chatClient->send( json_encode($message_array) );
                }
            } catch (\Exception $e) {
                $result['message'] = $e->getMessage();
            }
        } else {
            $result['message'] = 'Введите нормальное сообщение!';
        }

        $client->send( json_encode($result) );
    }

    public function commandSetName(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);

        $result = [
            'message' => 'Имя обновлено!',
        ];

        if (!empty($request['name']) && $name = trim($request['name'])) {
            $usernameFree = true;
            foreach ($this->clients as $chatClient) {
                if ($chatClient != $client && $chatClient->name == $name) {
                    $result['message'] = 'Это имя уже занято!';
                    $usernameFree = false;
                    break;
                }
            }

            if ($usernameFree) {
                $client->name = $name;
                $result['from'] = $name;
                $result['messages'] = $this->clientsMessages;
            }
        } else {
            $result['message'] = 'Введите имя!';
        }

        $client->send( json_encode($result) );
    }

}