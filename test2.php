<?php


$post_data = array(
"name" => "홍길동",
"birthday" => "1980-08-20"
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://hiprint.biz");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_exec($ch);
?>
