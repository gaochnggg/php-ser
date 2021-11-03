<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/12/6 0006
 * Time: 下午 4:45
 */
namespace gc\ser\system\http;


use gc\ser\facades\App;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Shieldon\Psr7\ServerRequest;

class Request extends ServerRequest
{
    /**
     * ServerRequest constructor.
     *
     * @param string                 $method       Request HTTP method
     * @param string|UriInterface    $uri          Request URI object URI or URL
     * @param string|StreamInterface $body         Request body
     * @param array                  $headers      Request headers
     * @param string                 $version      Request protocol version
     */
    public function __construct(
        string $method  = 'GET',
        $uri     = ''   ,
        $body    = ''   ,
        array  $headers = []   ,
        string $version = '1.1'
    ){
        $query = parse_url($uri,PHP_URL_QUERY);
        parse_str($query,$getParams);
        $cookieParams = $this->parseCookie($headers);
        $serverParams = getenv();
        list($postParams, $filesParams) = $this->parseBody($headers, $body);

        parent::__construct(
            $method,
            $uri,
            $body,
            $headers,
            $version,
            $serverParams,
            $cookieParams,
            $postParams  ,
            $getParams,
            $filesParams
        );
    }

    public function parseCookie($headers)
    {
        $cookies = [];
        $cookieStr = $headers['Cookie'] ?? '';
        if (empty($cookieStr)){
            return $cookies;
        }
        $cookieItemStrArr = explode(";", $cookieStr);
        foreach ($cookieItemStrArr as $cookieItemStr){
            if (empty($cookieItemStr)){
                continue;
            }
            $cookieKv = explode("=", $cookieItemStr);
            $cookies[$cookieKv[0]] = $cookieKv[1];
        }
        return $cookies;
    }

    /**
     * @param $body
     * @return array[]
     */
    public function parseBody($headers, $body)
    {
        $postParams = $filesParams = [];

        $contentType = $headers['Content-Type'];
        $boundary= "";
        if (preg_match("/boundary=(\S+)/i",$contentType,$matches)){
            $boundary = "--".$matches[1];
            $contentType = "multipart/form-data";
        }

        switch ($contentType){
            case 'multipart/form-data':
                list($postParams, $filesParams) = $this->parseFormData($boundary, $body);
                break;
            case 'application/x-www-form-urlencoded':
                parse_str($body,$postParams);
                break;
            case 'application/json':
                $postParams = json_decode($body,true);
                break;
        }
        return [$postParams, $filesParams];
    }

    public function parseFormData($boundary, $data)
    {
        $tempPath = App::tmpPath();
        $data = substr($data,0,-4);
        $formData = explode($boundary,$data);
        $postParams = $filesParams = [];
        $key = 0;
        foreach ($formData as $field){
            if ($field){
                $kv = explode("\r\n\r\n",$field,2);
                $value = rtrim($kv[1],"\r\n");
                if (preg_match('/name="(.*)"; filename="(.*)"/',$kv[0],$matches)){
                    file_put_contents($tempPath.DIRECTORY_SEPARATOR.$matches[2], $value);
                    $fileItem = [];
                    $fileItem['name'] = $matches[1] ?? $matches[2];
                    $fileItem['tmp_name'] = $matches[2];
                    $fileItem['size'] = strlen($value);
                    $fileType = explode("\r\n",$kv[0],2);
                    $fileType = explode(": ",$fileType[1]);
                    $fileItem['type'] = $fileType[2];
                    $fileItem['error'] = UPLOAD_ERR_OK;
                    $filesParams[$fileItem['name']] = $fileItem;
                    ++$key;
                }else if (preg_match('/name="(.*)"/',$kv[0],$matches)){
                    $postParams[$matches[1]] = $value;
                }
            }
        }
        return [$postParams, $filesParams];
    }

//    public function __destruct()
//    {
//        if (is_resource($this->getBody())){
//            $this->getBody()->close();
//        }
//    }
}