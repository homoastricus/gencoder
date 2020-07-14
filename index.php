<?php

header('Content-Type: text/html; charset=UTF-8');

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_regex_encoding('UTF-8');
mb_internal_encoding("UTF-8");

$text = "Шифрование Xor в PHP. Я новичок в шифровании Xor и у меня возникают некоторые проблемы со следующим кодом.";


require_once("GenCoder.php");
$message = $_POST["m"] ?? $text;
// not for real project, requires constant string key

$gen_coder = new GenCoder(md5("GenCode"));
session_start();
if (isset($_GET['reset']) OR !isset($_SESSION['key'])) {
    $gen_params = $gen_coder->init();
    $key = $gen_params['key'];
    $pass1 = $gen_params['pass1'];
    $pass2 = $gen_params['pass2'];
    $_SESSION['pass1'] = $pass1;
    $_SESSION['pass2'] = $pass2;
    $_SESSION['key'] = $key;
} else {
    $pass1 = $_SESSION['pass1'];
    $pass2 = $_SESSION['pass2'];
    $key = $_SESSION['key'];
}

$sender_hashcode = $gen_coder->sender_hashcode($pass1);
$receiver_hashcode = $gen_coder->receiver_hashcode($pass2);

$coded_message = $gen_coder->codeMessage($message, $key, $pass1, $receiver_hashcode);

?>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        body, html {
            font-family: 'Open Sans', sans-serif;
            color: #666;
            font-size: 14px;
        }

        h1 {
            font-size: 27px;

            color: #151010;

            text-shadow: 0px 1px 1px #605555;

            background: #4d4444;

            border-radius: 10px;

            padding: 14px;

            border-top: 1px solid #0f0608;

            border-bottom: 2px solid #131010;

            box-shadow: 0px 4px 12px 0px rgba(0, 0, 0, 0.35);
        }

        h4 {
            font-size: 16px;
        }

        hr {
            border: 0px solid #000;
            width: 95%;
            background: #000;
            height: 2px;
            box-shadow: inset 0px 1px 10px 1px rgba(50, 50, 50, 0.26);
            border-bottom: 1px solid #262626;
            border-top: 1px solid #000;
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

        a.link {
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
            background-image: url('/img/ava.png');
        }

        .gencoder {
            color: #20bd00;
            font-size: 36px;
        }

        a {
            color: #a48989;
        }
        ul {
            text-align: left;

            color: #a48989;

            font-size: 15px;

            line-height: 30px;

            letter-spacing: 0.6px;

            font-style: italic;

            text-shadow: 1px 1px 1px rgb(0, 0, 0);
        }

        .sh_php {
            background: #f0f0f0 !important;
            padding: 20px !important;
            border: 1px solid #d9d9d9;
            min-width: 700px;
        }

        .rand_sym {
            background: #000;
            color: red;
            font-weight: bold;
        }

        /* Main styles */
        @import url(https://fonts.googleapis.com/css?family=Open+Sans:800);
        .text {
            fill: none;
            stroke-width: 3;
            stroke-linejoin: round;
            stroke-dasharray: 70 330;
            stroke-dashoffset: 0;
            -webkit-animation: stroke 12s infinite linear;
            animation: stroke 12s infinite linear;
        }

        .text:nth-child(5n + 1) {
            stroke: #F2385A;
            -webkit-animation-delay: -2.4s;
            animation-delay: -2.4s;
        }

        .text:nth-child(5n + 2) {
            stroke: #F5A503;
            -webkit-animation-delay: -4.8s;
            animation-delay: -4.8s;
        }

        .text:nth-child(5n + 3) {
            stroke: #000000;
            -webkit-animation-delay: -7.2s;
            animation-delay: -7.2s;
        }

        .text:nth-child(5n + 4) {
            stroke: #56D9CD;
            -webkit-animation-delay: -9.6s;
            animation-delay: -9.6s;
        }

        .text:nth-child(5n + 5) {
            stroke: #3AA1BF;
            -webkit-animation-delay: -12s;
            animation-delay: -12s;
        }

        @-webkit-keyframes stroke {
            100% {
                stroke-dashoffset: -400;
            }
        }

        @keyframes stroke {
            100% {
                stroke-dashoffset: -400;
            }
        }

        /* Other styles */
        html, body {
            height: 100%;
        }

        body {
            background: #111;
            margin: 0;
            font-family: sans-serif;
            color: #c5caaf;
            font-size: 14px;
        }

        svg {
            position: absolute;
            width: 800px;
            height: 120px;
        }

        .main_logo {
            font-size: 100px;
            width: 900px;
            margin: auto;
            margin-top: 10px;
            height: 120px;
        }

        .container {
            margin: auto;
            width: 980px;
            margin-top: 20px;
            padding: 20px;
            text-align: center;
            border: 1px solid #151515;
            background: #171717;
            box-shadow: -6px -6px 16px 0px rgba(0, 0, 0, 0.5);
        }

        .step_element_icon {
            width: 120px;
            height: 120px;
        }

        .step_element_icon_mini {
            width: 50px;
            height: 50px;

        }

        .step_element_content {
            margin-left: 50px;
        }

        .step_element_header {
            margin-bottom: 20px;
            background: #4d4444;
            padding: 10px;
            font-size: 20px;
            text-align: left;
            color: #211010;
            line-height: 28px;
            text-shadow: 0px 1px 1px #605555;
        }

        .step_element {
            display: flex;
            margin-bottom: 30px;
        }

        .style_colored {
            color: #3083c9;
            line-height: 50px;
            margin-left: 25px;
            margin-right: 25px;
            font-size: 32px;
        }

        p {
            padding: 20px;
            line-height: 22px;
            text-indent: 20px;
        }

        .flex_item {
        }
        .flex {
            display: flex;
            margin-left: 150px;
            margin-bottom: 50px;
        }

    </style>
    <script src="http://shjs.sourceforge.net/sh_main.min.js"></script>
    <script src="http://shjs.sourceforge.net/lang/sh_php.js"></script>
    <link rel="stylesheet" href="http://shjs.sourceforge.net/sh_style.css" type="text/css">

</head>
<body onload="sh_highlightDocument();">

<div class="main_logo">
    <svg viewBox="0 0 800 120">
        <!-- Symbol-->
        <symbol id="s-text">
            <text text-anchor="middle" x="50%" y="50%" dy=".35em">GenCoder</text>
        </symbol>
        <!-- Duplicate symbols-->
        <use class="text" xlink:href="#s-text"></use>
        <use class="text" xlink:href="#s-text"></use>
        <use class="text" xlink:href="#s-text"></use>
        <use class="text" xlink:href="#s-text"></use>
        <use class="text" xlink:href="#s-text"></use>
    </svg>
</div>

<div class="container">
    <h1> Open source библиотека симметричного шифрования на PHP от <a class="link" href="https://bitbucket.org/astricus"
                                                                      title="https://bitbucket.org/astricus"><span
                    class="ava"></span></a>
    </h1>
    <br><br>

    <div class="flex">
        <img class="step_element_icon_mini flex_item" src="/img/user1.png" alt="image"/>
        <img class="step_element_icon_mini flex_item" src="/img/mail.png" alt="image"/>
        <div class="style_colored flex_item"> > GenCoder > </div>
        <img class="step_element_icon_mini flex_item" src="/img/world-wide-web.png" alt="image"/>
        <div class="style_colored flex_item">> GenCoder ></div>
        <img class="step_element_icon_mini flex_item" src="/img/mail.png" alt="image"/>
        <img class="step_element_icon_mini flex_item" src="/img/user2.svg" alt="image"/>
    </div>


    <hr>
    <h2>Что это и для чего?</h2>
    <ul>
        <li>Библиотека GenCoder дает возможность обратимо шифровать любую информацию для своих нужд в приложениях
        <li>Используется криптографически стойкий алгоритм шифрования (вариация шифра Вернама, случайная последовательность кодирования в качестве исходного ключа)
        <li>Алгоритм шифрования дополнительно усилен <a href="#randomize">рандомизацией ключа</a> для каждого уникального сообщения
        <li>Библиотека использует механизм сжатия данных для оптимизации пересылки и хранения шифрованной информации
        <li>Библиотека проста, содержит открытый алгоритм и легка в использовании
        <li>Бесплатна
    </ul>

    <br>
    <hr>
    <h2>Как это работает:</h2>


    <div class="step_element_header">1. Подключение библиотеки GenCoder</div>
    <div class="step_element">

        <img class="step_element_icon" src="/img/install.png" alt="image"/>
        <div class="step_element_content content">
             <pre class="sh_php">
//Подключается библиотека-класс GenCoder
require_once ("GenCoder.php");
$salt = "Моя соль для шифрования в приложении";
$gen_coder = new GenCoder($salt);

</pre>

        </div>
    </div>

    <div class="step_element_header">2. Генерация конфиденциальных данных
    </div>
    <div class="step_element">

        <img class="step_element_icon" src="/img/file-sharing.png" alt="image"/>
        <div class="step_element_content content">
             <pre class="sh_php">
/*
При первоначальном запуске, а также при переинициализации ключа/паролей происходит
генерация секретного ключа и паролей отправителя и адресата для данного канала связи
*/
$gen_params = $gen_coder->init();
$key = $gen_params['key'];
$pass1 = $gen_params['pass1'];
$pass2 = $gen_params['pass2'];

//закодированное сообщение
$sender_hashcode = $gen_coder->sender_hashcode($pass1);
$receiver_hashcode = $gen_coder->receiver_hashcode($pass2);

$coded_message = $gen_coder->codeMessage($message, $key, $pass1, $receiver_hashcode);
</pre>

        </div>
    </div>

    <div class="step_element_header">3. Шифрование сообщения</div>
    <div class="step_element">

        <img class="step_element_icon" src="/img/binary-code.png" alt="image"/>
        <div class="step_element_content content">
             <pre class="sh_php">
//Исходное сообщение, отправляемое отправителем адресату, шифруется
$sender_hashcode = $gen_coder->sender_hashcode($pass1);
$receiver_hashcode = $gen_coder->receiver_hashcode($pass2);

$coded_message = $gen_coder->codeMessage($message, $key, $pass1, $receiver_hashcode);
</pre>

        </div>
    </div>

    <div class="step_element_header">4. Сообщение зашифрованно и сжато.</div>
    <div class="step_element">

        <img class="step_element_icon" src="/img/data-compression.png" alt="image"/>
        <div class="step_element_content content">
             <pre class="sh_php">
//Исходное сообщение надежно шифрованно,
//а также сжато для оптимизации хранения и передачи шифра по сети адресату
$sender_hashcode = $gen_coder->sender_hashcode($pass1);
$receiver_hashcode = $gen_coder->receiver_hashcode($pass2);

$coded_message = $gen_coder->codeMessage($message, $key, $pass1, $receiver_hashcode);
</pre>

        </div>
    </div>

    <div class="step_element_header">5. Дешифрование сообщения</div>
    <div class="step_element">

        <img class="step_element_icon" src="/img/encrypt.png" alt="image"/>
        <div class="step_element_content content">
             <pre class="sh_php">
//Для дешифрования шифра необходим код получателя, а также наличие секретного кода канала.
// Таким образом, информация надежна защищена от постороннего просмотра и вмешательства
//$key берется из хранилища у адресата
//$sender_hashcode  - хеш (публичный ключ) отправителя
//$pass2 - пароль адресата на дешифрование канала с данным отправителем
$sender_hashcode = $gen_coder->sender_hashcode($pass1);
$receiver_hashcode = $gen_coder->receiver_hashcode($pass2);

$coded_message = $gen_coder->codeMessage($message, $key, $pass1, $receiver_hashcode);

$plain_message = $gen_coder->decodeMessage($coded_message, $key, $sender_hashcode, $pass2);
//$plain_message - исходная, дешифрованная информация
</pre>

        </div>
    </div>

    <br>
    <hr>
    <h2><a name="randomize">Усиление защиты ключа путем рандомизации</a></h2>
    <p class="content">Обычно при использовании метода одноразовых блокнотов или приближенных к нему алгоритмов сообщения в канале шифрованной связи
    шифруются каждый раз уникальным ключем из уникальных последовательностей случайных символов. При этом возникают следующие критические проблемы:<br>
    1. Необходимо заранее передавать уникальный случайный ключ отправита адресату для каждого шифросообщения.<br>
    2. Для больших объемов данных требуется передавать большие ключи из случайных символов, чтобы сохранить криптостойкость алгоритма, что само по себе трудозатратно и неудобно.<br>

    Рандомизация секретного ключа  позволяет избавиться сразу от этих двух проблем путем простого решения:<br>
    1. Исходный секретный ключ состоит из сгенерированных случайным образом символов создается разово и хранится у адресата и отправителя при создании шифрованного канала.
    При этом секретный ключ достаточно малого размера, по умолчанию он составляет 64кб.<br>
    2. При каждом новом шифровании сообщения отправителя на основе сложного алгоритма генерируется метод обхода исходного ключа, при этом метод зависит
    от хешей ключей адресата и отправителя, шифруемого сообщения и исходного ключа.<br>
    То есть, предметно говоря, если при шифровании методом Вернама первый символ шифросообщения получается операцией XOR первого символа исходного текста с первым символов ключа и так далее, пока не будут
    в арифметическом порядке перебраны символы всего сообщения и ключа, то данный метод сопоставляет последовательности символов исходного сообщения собственную последовательность символов ключа.
    </p>


    <br>
    <hr>
    <h2>Онлайн-тестирование:</h2>

    <form name="code" action="?" method="post">
        <textarea name="m" placeholder="исходная информация"><?= $message ?></textarea>
        <input type="submit" value="кодировать" class="code_it">
    </form>

    <h2>Закодировано в:</h2>
    <textarea placeholder="информация для шифрования" readonly><?= $coded_message ?></textarea>

    <h2>Пароли:</h2>
    <textarea placeholder="" readonly><?= $pass1 . ",  " . $pass2 ?></textarea>

    <h2>Секретный ключ:</h2>
    <textarea placeholder="" readonly><?= $key ?></textarea>


    <h2>Дешифрование:</h2>
    <textarea readonly><?= $gen_coder->decodeMessage($coded_message, $key, $sender_hashcode, $pass2) ?></textarea>
    <br>
    <br>
    <hr>


    <h2>Как использовать библиотеку:</h2>
    <div class="content">
        1. Cкачать класс GenCoder.php <br>
        <a class="link" href="/GenCoder.zip">Класс GenCoder</a>
        <br><br>

        <br><br>
        2. Минимально функциональный код использования библиотеки представлен ниже

        <pre class="sh_php">
require_once ("GenCoder.php");
$salt = "Моя соль для шифрования в приложении";
$gen_coder = new GenCoder($salt);
$gen_params = $gen_coder->init();
$key = $gen_params['key'];
$pass1 = $gen_params['pass1'];
$pass2 = $gen_params['pass2'];

//закодированное сообщение
$sender_hashcode = $gen_coder->sender_hashcode($pass1);
$receiver_hashcode = $gen_coder->receiver_hashcode($pass2);

$coded_message = $gen_coder->codeMessage($message, $key, $pass1, $receiver_hashcode);
$plain_text = $gen_coder->decodeMessage($coded_message, $key, $sender_hashcode, $pass2);
</pre>
</div>


    <br>
    <br>
    <hr>
    <h2>Обратная связь:</h2>
    <div class="content">
        Разработчик - Артур Матарин <a class="link" href="https://bitbucket.org/astricus"> https://bitbucket.org/astricus</a>
        <br>
        <br>
        Отзывы, пожелания и предложения принимаются сюда: homoastricus2011@gmail.com
    </div>
</div>
</body>
</html>