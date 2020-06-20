<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

/* $db = pg_connect("host=ec2-3-216-129-140.compute-1.amazonaws.com port=5432 
                  dbname=vk-bot-food 
                  user=vygetfgegbfnef
                  password=0fcc4db3e52cd342c944f379e803e52dbff287bac895707eebfbab4ff8f7d3ff"
                );
*/

/* $url = parse_url(getenv("DATABASE_URL"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"],1);

pg_connect($server,$username,$password);
*/


// $db["path"] = ltrim($db["path"], "/");

use DigitalStar\vk_api\vk_api;


// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

$app->get('/', function() use($app) {
  //return "Hello World!";
  //$dbconn = pg_connect("dbname=vk-bot-food");

  $db_conn = pg_connect("host=ec2-3-216-129-140.compute-1.amazonaws.com port=5432 dbname=dc26ecc0mmsvu6 user=vygetfgegbfnef password=0fcc4db3e52cd342c944f379e803e52dbff287bac895707eebfbab4ff8f7d3ff");
  if (!$db_conn) {
    echo "415 База не подключена.\n";
    exit;
  }
  $result = pg_query($db_conn, "SELECT description_text FROM food_giveout_db WHERE index='61216'");
  //var_dump("testing");
  if (!$result) {
    echo "Произошла ошибка.\n";
    exit;
  }
  while ($row = pg_fetch_row($result)){
    echo "Имя: $row[0]";

  }

  
  
});

$app->post('/bot', function() use($app) {
 
  $data = json_decode(file_get_contents('php://input'));
  $db_conn = pg_connect("host=ec2-3-216-129-140.compute-1.amazonaws.com port=5432 dbname=dc26ecc0mmsvu6 user=vygetfgegbfnef password=0fcc4db3e52cd342c944f379e803e52dbff287bac895707eebfbab4ff8f7d3ff");

  $buttonstart = [
    "one_time" => false,
    "buttons" => [
      [
        ["action"=>[
        "type"=>"text",
        "payload"=>'{"button":"1"}',
        "label"=>"Начать"],
        "color"=>"positive"],
      ],

      [
        ["action"=>[
        "type"=>"text",
        "payload"=>'{"button":"2"}',
        "label"=>"Помощь"],
        "color"=>"primary"],
      ]
    ]
  ];

  $buttoncity = [
    "one_time" => false,
    "buttons" => [
      [
        ["action"=>[
        "type"=>"text",
        "payload"=>'{"button":"spb"}',
        "label"=>"СПБ"],
        "color"=>"primary"],
      ],

      [
        ["action"=>[
        "type"=>"text",
        "payload"=>'{"button":"msk"}',
        "label"=>"Москва"],
        "color"=>"primary"],
      ]
    ]
  ];

  $buttonproducts = [
    "one_time" => false,
    "buttons" => [
      [
        ["action"=>[
        "type"=>"text",
        "payload"=>'{"button":"allprods"}',
        "label"=>"Все продукты"],
        "color"=>"primary"],
      ],

      [
        ["action"=>[
        "type"=>"text",
        "payload"=>'{"button":"listprods"}',
        "label"=>"Список"],
        "color"=>"primary"],
      ]
    ]
  ];

  $buttonlistproducts = [
    "inline" => true,
    "buttons" => [
      [
        ["action"=>[
        "type"=>"text",
        "payload"=>'{"button":"bread"}',
        "label"=>"Хлебъ и булки"],
        "color"=>"primary"],
      ],

      [
        ["action"=>[
        "type"=>"text",
        "payload"=>'{"button":"vegs"}',
        "label"=>"Овощи (вы)"],
        "color"=>"primary"],
      ],

      [
        ["action"=>[
          "type"=>"text",
          "payload"=>'{"button":"fruits"}',
          "label"=>"Фрукты"],
          "color"=>"primary"],
      ],

      [
        ["action"=>[
          "type"=>"text",
          "payload"=>'{"button":"meat"}',
          "label"=>"Мясо"],
          "color"=>"primary"],
      ]


    ]
  ];




  if( !$data ){
    return 'nioh';
  }

  
  if( $data->secret !== getenv('VK_SECRET_TOKEN') && $data->type !== 'confirmation' ){
    return 'nioh';
  }


  switch( $data->type ){

    case 'confirmation':
      return getenv('VK_CONFIRMATION_CODE');
    break;

    
    case 'message_new':

      switch ( $data->object->message->text ){

        case "Начать":

          $request_params = array(
            'peer_id' => $data->object->message->from_id,
            'message' => 'Выберите ваш город',
            'keyboard' => json_encode($buttoncity, JSON_UNESCAPED_UNICODE),
            'access_token' => getenv('VK_TOKEN'),
            'v' => '5.69'
    
          );
    
          file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
          return 'ok';

        break;

        case "Помощь":
          $request_params = array(
            'peer_id' => $data->object->message->from_id,
            'message' => 'ТЕКСТ ТЕКСт',
            'keyboard' => json_encode($buttonstart, JSON_UNESCAPED_UNICODE),
            'access_token' => getenv('VK_TOKEN'),
            'v' => '5.69'
    
          );
    
          file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
          return 'ok';
        break;

        /* case "СПБ":
          $request_params = array(
            'peer_id' => $data->object->message->from_id,
            'message' => 'Как вы хотите искать еду? В предложенном списке или по собственному запросу?',
            'keyboard' => json_encode($buttonproducts, JSON_UNESCAPED_UNICODE),
            'access_token' => getenv('VK_TOKEN'),
            'v' => '5.69'
    
          );
    
          file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
          return 'ok';
        break; */

        case "СПБ":

          $test_result = pg_query($db_conn, "SELECT description_text, full_link FROM food_giveout_db WHERE index='61231'");
          if (!$test_result) {
            $test_row = 'Произошла ошибка.';
            
          } else {
            $test_array = pg_fetch_row($test_result);
            //$text_posta = $test_array[0];
            $test_row = 'Смотри, что мы нашли: ' . $test_array[0] . "\n" . $test_array[1];
          }

          $request_params = array(
            'peer_id' => $data->object->message->from_id,
            'message' => $test_row,
            'keyboard' => json_encode($buttonproducts, JSON_UNESCAPED_UNICODE),
            'access_token' => getenv('VK_TOKEN'),
            'v' => '5.69'
    
          );
    
          file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
          return 'ok';
        break;

        case "Москва":
          $request_params = array(
            'peer_id' => $data->object->message->from_id,
            'message' => 'Как вы хотите искать еду? В предложенном списке или по собственному запросу?',
            'keyboard' => json_encode($buttonproducts, JSON_UNESCAPED_UNICODE),
            'access_token' => getenv('VK_TOKEN'),
            'v' => '5.69'
    
          );
    
          file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
          return 'ok';
        break;

        case "Все продукты":
          $request_params = array(
            'peer_id' => $data->object->message->from_id,
            'message' => 'Поехали',
            'keyboard' => json_encode($buttonstart, JSON_UNESCAPED_UNICODE),
            'access_token' => getenv('VK_TOKEN'),
            'v' => '5.69'
    
          );
    
          file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
          return 'ok';
        break;

        case "Список":
          $request_params = array(
            'peer_id' => $data->object->message->from_id,
            'message' => 'Выбирай',
            'keyboard' => json_encode($buttonlistproducts, JSON_UNESCAPED_UNICODE),
            'access_token' => getenv('VK_TOKEN'),
            'v' => '5.69'
    
          );
    
          file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
          return 'ok';
        break;

        default:

          $request_params = array(
            'peer_id' => $data->object->message->from_id,
            'message' => 'Привет! Найдём покушать?',
            'keyboard' => json_encode($buttonstart, JSON_UNESCAPED_UNICODE),
            'access_token' => getenv('VK_TOKEN'),
            'v' => '5.69'
    
          );
    
          file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
          return 'ok';

        break;

      }


      $request_params = array(
        'peer_id' => $data->object->message->from_id,
        'message' => 'Привет! Найдём покушать?',
        'keyboard' => json_encode($buttonstart, JSON_UNESCAPED_UNICODE),
        'access_token' => getenv('VK_TOKEN'),
        'v' => '5.69'

      );

      file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
      return 'ok';

    break;

    
  }


  return "nioh";
});


$app->run();