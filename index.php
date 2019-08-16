<?php

header('Content-Type: text/html; charset=UTF-8');

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_regex_encoding('UTF-8');
mb_internal_encoding("UTF-8");

$text = "Шифрование Xor в PHP. Я новичок в шифровании Xor и у меня возникают некоторые проблемы со следующим кодом ";

require_once("SymCoder2.php");
$message = $_POST["m"] ?? "это информация для кодирования";
// not for real project, requires constant string key
$code = md5("hash");
$symCoder = new SymCoder2($code);
$coded_message = $symCoder->code($message, $code);

?>
<html>
<head>
    <style>
        body, html {
            font-family: sans-serif;
            color: #666;
            font-size: 14px;
        }

        h1 {
            font-size: 24px;
        }

        h4 {
            font-size: 16px;
        }

        .container {
            margin: auto;
            width: 640px;
            background: #ccffff;
            padding: 10px;
            text-align: center;
        }

        textarea {
            min-width: 550px;
            min-height: 150px;
            border: 1px solid #888;
        }

        .code_it {
            display: block;
            margin: 10px auto;
            background: #eee;
            padding: 6px;
            font-size: 18px;
            border-radius: 6px;
            min-width: 200px;
        }

        textarea {
            font-size: 16px;
            color: #444;
            padding: 10px;
        }

        .content {
            text-align: left;
        }

        a {
            text-decoration: none;
            background: #eee;
            padding: 5px;
            color: #000;
            margin: 10px;
            font-size: 18px;
        }

        .ava {
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            border-radius: 3px;
            display: inline-block;
            height: 50px;
            width: 50px;
            background-color: transparent;
            background-image: url('https://bitbucket.org/account/astricus/avatar/');
        }

        .symcoder {
            color: #20bd00;
            font-size: 36px;
        }
        ul {
            text-align: left;
            color: #444;
            font-size: 14px;
            line-height: 22px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Open source библиотека обратимого шифрования на PHP <span class="symcoder">SymCoder</span>
        от <a href="https://bitbucket.org/astricus" title="https://bitbucket.org/astricus"><span class="ava"></span></a>
    </h1>
    <br><br>
    <hr>
    <h2>Что это и для чего?</h2>
    <ul>
        <li>Библиотека дает возможность обратимо кодировать любую информацию для своих нужд в приложениях
        <li>Поддержка кириллицы, латиницы, а также типичных клавиатурных символов русско-английской клавиатуры
        <li>Легка в использовании
        <li>Расширяема (например, можно выбрать массив с другими символами для кодирования)
        <li>Бесплатна
    </ul>
    <br>
    <hr>
    <h2>Онлайн-тестирование:</h2>

    <form name="code" action="?" method="post">
        <textarea name="m" placeholder="исходная информация"><?= $message ?></textarea>
        <input type="submit" value="кодировать" class="code_it">
    </form>

    <h2>Закодировано в:</h2>
    <textarea placeholder="информация для кодирования" readonly><?= $coded_message ?></textarea>
    <h2>Обратное кодирование:</h2>
    <textarea readonly><?= $symCoder->decode($coded_message); ?></textarea>
    <br>
    <br>
    <hr>

    <h2>Как использовать библиотеку:</h2>
    <div class="content">
        1. Cкачать класс SymCoder.php <br>
        <a href="https://bitbucket.org/astricus/symcoder"><img src="bitbucket.png" width="20px">Класс SymCoder</a>
        <br><br>

        2. Подключить класс SymCoder (например
        <code>require_once ("SymCoder.php") </code>)

        <br><br>
        3. Cгенерировать уникальный ключ для канала шифрования-дешифрования. Это может быть обычный md5-хеш от пары id
        пользователей/приложений вида
        <code>md5($user_id_1 . $user_id_2)</code>)
        либо какой-то любой другой уникальный ключ. Размер и тип символов не существенны, но настоятельно рекомендуется
        использовать
        непустой и желательно уникальный ключ.
        <textarea>
require_once ("SymCoder.php")
$symCoder = new SymCoder($code);
//закодированное сообщение
$coded_message = $symCoder->code($message);

        </textarea>
        Полученное закодированное сообщение не требует защиты и может храниться и пересылаться открыто.
        Для раскодирования необходимо зашифрованное сообщение и публичный ключ (дайджест, подставляемый уникальным образом в каждое сообщение), уникальный для каждого сообщения.

        <br><br>
        4. Обратное декодирование:
        <textarea readonly> $symCoder->decode($coded_message); </textarea>
    </div>


    <br>
    <br>
    <hr>
    <h2>Обратная связь:</h2>
    <div class="content">
        Разработчик - Артур Матарин <a href="https://bitbucket.org/astricus"> https://bitbucket.org/astricus</a>
        <br>
        <br>
        Отзывы, пожелания и предложения принимаются сюда: homoastricus2011@gmail.com
    </div>
</div>


</body>
</html>