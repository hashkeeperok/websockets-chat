<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\models\ChatMessage;

$this->title = 'Chat';
$this->params['breadcrumbs'][] = $this->title;
$messages = ChatMessage::find();
?>
<?= $messages->count() ?>
<?php foreach ($messages as $message): ?>
    <?= $message->text; ?>
<?php endforeach; ?>

<div class="chat-login">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Введите Имя">
        <span class="input-group-btn">
            <button class="btn btn-primary" type="button">Отправить</button>
          </span>
    </div>
</div>
<div class="main-chat bootstrap snippets bootdeys">
    <div class="col-md-7 col-xs-12 col-md-offset-2">
        <!-- Panel Chat -->
        <div class="panel" id="chat">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="icon wb-chat-text" aria-hidden="true"></i> Chat
                </h3>
            </div>
            <div class="panel-body">
                <div class="chats">
                    <div class="chat">
                        <div class="chat-avatar">

                        </div>
                        <div class="chat-body">
                            <div class="chat-content">
                                <p>
                                    Good morning, sir.  What can I do for you? Good morning, sir.  What can I do for you? Good morning, sir.  What can I do for you?
                                </p>
                                <time class="chat-time" datetime="2015-07-01T11:37"></time>
                            </div>
                        </div>
                    </div>
                    <div class="chat chat-left">
                        <div class="chat-avatar">
                            <a class="avatar avatar-online" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="Edward Fletcher">
                                <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="...">
                                <i></i>
                            </a>
                        </div>
                        <div class="chat-body">
                            <div class="chat-content">
                                <p>Well, I am just looking around.</p>
                                <time class="chat-time" datetime="2015-07-01T11:39">11:39:57 am</time>
                            </div>
                        </div>
                    </div>
                    <div class="chat">
                        <div class="chat-avatar">
                            <a class="avatar avatar-online" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="June Lane">
                                <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="...">
                                <i></i>
                            </a>
                        </div>
                        <div class="chat-body">
                            <div class="chat-content">
                                <p>
                                    If necessary, please ask me.
                                </p>
                                <time class="chat-time" datetime="2015-07-01T11:40">11:40:10 am</time>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <form>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Введите сообщение">
                        <span class="input-group-btn">
            <button class="btn btn-primary" type="button">Отправить</button>
          </span>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Panel Chat -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script>
    $(function() {


        const chat = new WebSocket('ws://localhost:1025');

        const $usernameInput = $('.chat-login input');
        const $usernameButton = $('.chat-login button');

        const $messageInput = $('#chat .panel-footer input');
        const $messageButton = $('#chat .panel-footer button');

        chat.onmessage = function(e) {
            $('#response').text('');

            var response = JSON.parse(e.data);
            if (response.type && response.type == 'chat') {
                const mess = '<div class="chat"><div class="chat-avatar">'+ response.from +'</div>' +
                    '<div class="chat-body"><div class="chat-content">' + response.message +
                    '<time class="chat-time">' + response.time + '</time></div></div></div>';
                $('.chats').append(mess);
                //$('#chat').scrollTop = $('#chat').height;
            } else if (response.message) {
                alert(response.message);
            }
        };
        chat.onopen = function(e) {
            $('#response').text("Connection established! Please, set your username.");
        };

        $usernameButton.on('click', function() {
            if ($usernameInput.val()) {
                chat.send( JSON.stringify({'action' : 'setName', 'name' : $usernameInput.val()}) );
            } else {
                alert('Введите имя');
            }
        });

        $messageButton.click(function() {
            if ($messageInput.val()) {
                chat.send( JSON.stringify({'action' : 'chat', 'message' : $messageInput.val()}) );
            } else {
                alert('Введите сообщение');
            }
        })

    })
</script>
<style>

    body {
        background:#ddd;
        margin-top:10px;
    }

    .chat-login {

    }

    .chat-box {
        height: 100%;
        width: 100%;
        background-color: #fff;
        overflow: hidden
    }

    .chats {
        padding: 30px 15px
    }

    .chat-avatar {
        float: right
    }

    .chat-avatar .avatar {
        width: 30px
        -webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,0.2),0 6px 10px 0 rgba(0,0,0,0.3);
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.2),0 6px 10px 0 rgba(0,0,0,0.3);
    }

    .chat-body {
        display: block;
        margin: 10px 30px 0 0;
        overflow: hidden
    }

    .chat-body:first-child {
        margin-top: 0
    }

    .chat-content {
        position: relative;
        display: block;
        float: right;
        padding: 8px 15px;
        margin: 0 20px 10px 0;
        clear: both;
        color: #fff;
        background-color: #62a8ea;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 4px 0 rgba(0,0,0,0.37);
        box-shadow: 0 1px 4px 0 rgba(0,0,0,0.37);
    }

    .chat-content:before {
        position: absolute;
        top: 10px;
        right: -10px;
        width: 0;
        height: 0;
        content: '';
        border: 5px solid transparent;
        border-left-color: #62a8ea
    }

    .chat-content>p:last-child {
        margin-bottom: 0
    }

    .chat-content+.chat-content:before {
        border-color: transparent
    }

    .chat-time {
        display: block;
        margin-top: 8px;
        color: rgba(255, 255, 255, .6)
    }

    .chat-left .chat-avatar {
        float: left
    }

    .chat-left .chat-body {
        margin-right: 0;
        margin-left: 30px
    }

    .chat-left .chat-content {
        float: left;
        margin: 0 0 10px 20px;
        color: #76838f;
        background-color: #dfe9ef
    }

    .chat-left .chat-content:before {
        right: auto;
        left: -10px;
        border-right-color: #dfe9ef;
        border-left-color: transparent
    }

    .chat-left .chat-content+.chat-content:before {
        border-color: transparent
    }

    .chat-left .chat-time {
        color: #a3afb7
    }

    .panel-footer {
        padding: 0 30px 15px;
        background-color: transparent;
        border-top: 1px solid transparent;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
    }
    .avatar img {
        width: 100%;
        max-width: 100%;
        height: auto;
        border: 0 none;
        border-radius: 1000px;
    }
    .chat-avatar .avatar {
        width: 30px;
    }
    .avatar {
        position: relative;
        display: inline-block;
        width: 40px;
        white-space: nowrap;
        border-radius: 1000px;
        vertical-align: bottom;
    }
</style>