<?php


namespace gc\ser\commands;

use SimpleCli\Options\Help;
use SimpleCli\Command;
use SimpleCli\SimpleCli;

class Stop implements Command
{
    use Help;

    /**
     * @option daemon
     */
    public $daemon = false;

    public function run(SimpleCli $cli): bool
    {
        // TODO: Implement run() method.

        return true;
    }
}