<?php


namespace gc\ser\system\protocols;


use gc\ser\system\http\Request;

class Http implements Protocol
{

//用来检测一条消息是否完整
    public function Len($data)
    {
        if(strpos($data,"\r\n\r\n")){
            $headerLen = strpos($data,"\r\n\r\n") + 4;
            $bodyLen = 0;
            if (preg_match("/\r\nContent-Length: ?(\d+)/i",$data,$matches)){
                $bodyLen = $matches[1];
            }

            $totalLen = $headerLen + $bodyLen;
            if(strlen($data) >= $totalLen){
                return true;
            }
            return false;
        }
        return false;
    }

    public function encode($data='')
    {
        $data = $data."\n";
        return [strlen($data), $data];
    }

    public function decode($data='')
    {
        return $data;
    }

    //返回一条消息的总长度
    public function msgLen($data='')
    {
        $headerLen = strpos($data,"\r\n\r\n") + 4;
        $bodyLen = 0;
        if (preg_match("/\r\nContent-Length: ?(\d+)/i",$data,$matches)){
            $bodyLen = $matches[1];
        }
        return $headerLen + $bodyLen;
    }

    public static function parseData($data)
    {
        $headerLen = strpos($data,"\r\n\r\n");
        $headerStrWithQuery = substr($data, 0, $headerLen);

        $reqHeadStr = substr($headerStrWithQuery, 0, strpos($headerStrWithQuery, "\r\n"));
        list($method, $uriWithParam, $schema) = explode(" ", $reqHeadStr);
        $schema = substr($schema, strpos($schema, 'HTTP/')+5);

        $headerStr = substr($headerStrWithQuery, strpos($headerStrWithQuery, "\r\n")+2);
        $headers = Request::parseRawHeader($headerStr);
        $bodyStr = substr($data, $headerLen + 4);

        return new Request(
            $method,
            $uriWithParam,
            $bodyStr,
            $headers,
            $schema
        );
    }
}