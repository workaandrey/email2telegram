<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">МОИ ЯЩИКИ</a>
            @else
                <a href="{{ route('login') }}">Авторизация</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Регистрация</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="content">
        <div class="m-b-md">
            <div class="container">
                <h1>Как работать с email2telegram?</h1>
                <div style="text-align: left; width: 50%; margin: 0 auto;">
                    <ol>
                        <li>
                            Прежде чем начинать работу с email2telegram, необходимо зарегистрировать бота для Telegram и получить его уникальный id, являющийся одновременно и токеном.
                            <br>Для этого в Telegram существует специальный бот — <a href="https://telegram.me/botfather">@BotFather.</a>

                            <br>Пишем ему /start и получаем список всех его команд.
                            <br>Первая и главная — /newbot — отправляем ему и бот просит придумать имя нашему новому боту. Единственное ограничение на имя — оно должно оканчиваться на «bot». В случае успеха BotFather возвращает токен бота и ссылку для быстрого добавления бота в контакты, иначе придется поломать голову над именем.

                            <br>Для начала работы этого уже достаточно. Особо педантичные могут уже здесь присвоить боту аватар, описание и приветственное сообщение.

                            <br>Не забудьте проверить полученный токен с помощью ссылки <a href="https://api.telegram.org/bot%3CTOKEN%3E/getMe">api.telegram.org/bot&lt;TOKEN&gt;/getMe</a>, говорят, не всегда работает с первого раза.
                        </li>
                        <li>Перейдите в <a href="{{route('settings.index')}}">настройки</a> и укажите токена бота для Telegram</li>
                        <li><a href="{{route('mailbox.create')}}">Добавьте новый почтовый ящик, указав данные для IMAP сервера</a></li>
                        <li>Как только Вы создадите активный почтовый ящик наш сервис будет проверять его каждые несколько минут и присылать уведомления в Telegram</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
