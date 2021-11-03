<?php

use gc\ser\system\protocols\Http;

require "../vendor/autoload.php";

$data = <<<EOF
POST /user/personal-homepage?uid=585864 HTTP/1.1
Cookie: a=123123;
User-Agent: PostmanRuntime/7.28.1
Accept: */*
Postman-Token: f57e4870-5b5a-4ec8-baa1-a8825da1c32f
Host: 172.22.231.228:9502
Accept-Encoding: gzip, deflate, br
Connection: keep-alive
Content-Type: multipart/form-data; boundary=--------------------------772380080543707694808873
Content-Length: 595

----------------------------772380080543707694808873
Content-Disposition: form-data; name="aaaa"

a
----------------------------772380080543707694808873
Content-Disposition: form-data; name="bbbb"

bb
----------------------------772380080543707694808873
Content-Disposition: form-data; name="aafile"; filename="a2.txt"
Content-Type: text/plain

aaaaaaaaaa22222222
----------------------------772380080543707694808873
Content-Disposition: form-data; name=""; filename="a1.txt"
Content-Type: text/plain

aaaaaaaaa111111111
----------------------------772380080543707694808873--

EOF;

require "../src/app.php";

$http = new Http();
var_dump($http->Len($data));

$decode = $http->decode($data);

try {
    $req = Http::parseData($decode);

}catch (Exception $exception){
    var_dump($exception);
}
var_dump(111111111111111);
var_dump($req);
die();
$msgLen = $http->msgLen($data);
var_dump($msgLen);
//var_dump(strlen($data));