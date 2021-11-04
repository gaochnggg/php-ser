<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/12/6 0006
 * Time: 下午 4:46
 */
namespace gc\ser\system\http;

use gc\ser\system\TcpConnect;

class Response
{
    public $_statusReason = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    public $_connection;
    public $request;
    public $_headers = [];
    public $_status_code = 200;
    public $_status_info = "OK";
    public $_start_chunked=1;

    public function __construct(TcpConnect $connection, Request $request)
    {
        $this->_connection = $connection;
        $this->request = $request;
    }

    public function header($key,$value)
    {
        $this->_headers[$key] = $value;
        return $this;
    }

    public function status($code)
    {
        $this->_status_code = $code;
        if (isset($this->_statusReason[$code])){
            $this->_status_info = $this->_statusReason[$code];
        }
        return $this;
    }

    public function write($data="")
    {
        $len = strlen($data);
        $text =sprintf("HTTP/1.1 %d %s\r\n",$this->_status_code,$this->_status_info);
        $text.=sprintf("Date: %s\r\n",date("Y-m-d H:i:s"));
        $text.=sprintf("OS: %s\r\n",PHP_OS);
        $text.=sprintf("Content-Language: %s\r\n","zh-CN,zh;q=0.9");

        $text.=sprintf("Connection: %s\r\n",$this->request->getHeaderLine('Connection'));//keep-alive close
        $text.=sprintf("Access-Control-Allow-Origin: *\r\n");


        foreach ($this->_headers as $k=>$v){

            $text.=sprintf("%s: %s\r\n",$k,$v);
        }


        if (!isset($this->_headers['Content-Type'])){
            $text.=sprintf("Content-Type: %s\r\n","text/html;charset=utf-8");
        }
        if ($this->request->hasHeader('Accept_Encoding')){
            $encoding = $this->request->getHeaderLine('Accept_Encoding');
            if (preg_match("/gzip/",$encoding)){
                //启用内容压缩
                $data = gzencode($data);
                $len = strlen($data);
                $text.=sprintf("Content-Encoding: %s\r\n","gzip");
            }
        }
        $text.=sprintf("Content-Length: %d\r\n",$len);

        $text.="\r\n";
        $text.=$data;

        $this->_connection->sendMessage($text);

        //http 1.1 1.0 0.9 GET Connection: keep-alive
        if (strtolower($this->request->getHeaderLine('Connection'))=="close"){
            $this->_connection->Close();
        }
    }

    public function chunked($data="")
    {
        //$len = strlen($data);
        $text = "";
        if ($this->_start_chunked==1){
            $this->_start_chunked = 0;
            $text =sprintf("HTTP/1.1 %d %s\r\n",$this->_status_code,$this->_status_info);
            $text.=sprintf("Date: %s\r\n",date("Y-m-d H:i:s"));
            $text.=sprintf("OS: %s\r\n",PHP_OS);
            $text.=sprintf("Content-Language: %s\r\n","zh-CN,zh;q=0.9");

            $text.=sprintf("Connection: %s\r\n", $this->request->getHeaderLine('Connection'));//keep-alive close
            $text.=sprintf("Access-Control-Allow-Origin: *\r\n");


            foreach ($this->_headers as $k=>$v){
                $text.=sprintf("%s: %s\r\n",$k,$v);
            }

            if (!isset($this->_headers['Content-Type'])){
                $text.=sprintf("Content-Type: %s\r\n","text/html;charset=utf-8");
            }
            $text.=sprintf("Transfer-Encoding: chunked\r\n");
            $text.="\r\n";
        }

        $dataLen = dechex(strlen($data));
        $text.=$dataLen."\r\n";
        $text.=$data."\r\n";


        $this->_connection->sendMessage($text);

        //http 1.1 1.0 0.9 GET Connection: keep-alive

    }

    public function end()
    {
        $text="0\r\n";//用它来结束，表示响应实体结束了
        $text.="\r\n";

        $this->_connection->sendMessage($text);
        $this->_start_chunked = 1;
        if ($this->request->getHeaderLine('Connection')=="close"){
            $this->_connection->Close();
        }
    }
    public function sendFile($file)
    {

        if (!file_exists($file)){
            $this->status(404);
            $this->write("Not found file");
            return ;
        }
        $data = file_get_contents($file);
        if (!class_exists("finfo",false)){
            return ;
        }
        $fi = new \finfo(FILEINFO_MIME_TYPE);
        $len = strlen($data);
        $text =sprintf("HTTP/1.1 %d %s\r\n",$this->_status_code,$this->_status_info);
        $text.=sprintf("Date: %s\r\n",date("Y-m-d H:i:s"));
        $text.=sprintf("OS: %s\r\n",PHP_OS);
        $text.=sprintf("Content-Language: %s\r\n","zh-CN,zh;q=0.9");

        $text.=sprintf("Connection: %s\r\n", $this->request->getHeaderLine('Connection'));//keep-alive close
        $text.=sprintf("Access-Control-Allow-Origin: *\r\n");


        foreach ($this->_headers as $k=>$v){

            $text.=sprintf("%s: %s\r\n",$k,$v);
        }
        $text.=sprintf("Content-Type: %s\r\n",$fi->file($file));
        if ($this->request->hasHeader('Accept_Encoding')){
            $encoding = $this->request->getHeaderLine('Accept_Encoding');
            if (preg_match("/gzip/",$encoding)){

                //启用内容压缩
                $data = gzencode($data);
                $len = strlen($data);
                $text.=sprintf("Content-Encoding: %s\r\n","gzip");
            }
        }
        $text.=sprintf("Content-Length: %d\r\n",$len);
        $text.="\r\n";
        $text.=$data;

        $this->_connection->sendMessage($text);

        if (strtolower($this->request->getHeaderLine('Connection'))=="close"){
            $this->_connection->Close();
        }
    }

    public function sendMethods()
    {
        $text = "HTTP/1.1 200 OK\r\n";
        $text.=sprintf("Server: %s\r\n","te");
        $text.=sprintf("Date: %s\r\n",date("Y-m-d H:i:s"));
        $text.=sprintf("Content-Length: 0\r\n");
        $text.=sprintf("Connection: keep-alive\r\n");
        $text.=sprintf("Access-Control-Allow-Origin: *\r\n");
        $text.=sprintf("Access-Control-Allow-Method:POST,GET\r\n");
        $text.=sprintf("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept\r\n\r\n");

        $this->_connection->sendMessage($text);
    }
}