<?php
$url = 'http://localhost:8888/SeconCal/Main.php';
$data = array('key1' => '1234');
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
var_dump($result);?>