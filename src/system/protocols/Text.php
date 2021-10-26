<?php


namespace gc\ser\system\protocols;


class Text implements Protocol
{

    public function Len($data)
    {
        if (strlen($data)){
            return strpos($data, "\n");
        }
        return false;
    }

    public function encode($data = '')
    {
        $data .= "\n";
        return [strlen($data), $data];
    }

    public function decode($data = '')
    {
        return rtrim($data, "\n");
    }

    public function msgLen($data = '')
    {
        return strpos($data, "\n") + 1;
    }
}