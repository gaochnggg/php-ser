<?php


namespace gc\ser\commands;

use gc\ser\facades\ServerAttr;
use SimpleCli\Options\Help;
use SimpleCli\Command;
use SimpleCli\SimpleCli;

class Start implements Command
{
    use Help;

    /**
     * @option daemon
     */
    public $daemon = false;

    public function run(SimpleCli $cli): bool
    {
        // TODO: Implement run() method.
        var_dump($this->daemon);
        var_dump(ServerAttr::getIp());
        return true;
    }
}