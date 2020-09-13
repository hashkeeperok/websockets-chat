<?php
namespace console\controllers;

use console\components\CommandsServer;
use yii\console\Controller;

class ServerController extends Controller {

    public function actionStart($port = null) {
        $server = CommandsServer::getInstance();
        if ($port) {
            $server->port = $port;
        }

        if ($server->isConnected()) {
            $server->stop();
        }

        $server->start();
    }

    public function actionStop() {
        $server = CommandsServer::getInstance();
        if ($server->isConnected()) {
            $server->stop();
        }
    }

}