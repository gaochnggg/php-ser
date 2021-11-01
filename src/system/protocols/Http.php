<?php


namespace gc\ser\system\protocols;


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
        return rtrim($data,"\n");
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

    public static function parseHeader($data)
    {
        $headerSources = explode("\r\n",$data);
        list($method,$uri,$schema) = explode(" ", $headerSources[0]);
        $uri = parse_url($uri)['path'];
        parse_str(parse_url($uri,PHP_URL_QUERY),$gets);

        // $method, $uri, $schema, $gets
        $headers = [];
        unset($headerSources[0]);
        foreach ($headerSources as $headerSource){
            $headerSourceArr = explode(": ", $headerSource,2);
            $key = str_replace("-","_", $headerSourceArr[0]);
            $headers[$key] = rtrim($headerSourceArr[1]);
        }
    }
}