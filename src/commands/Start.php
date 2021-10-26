<?php


namespace gc\ser\commands;

use gc\ser\facades\Safe;
use SimpleCli\Options\Help;
use SimpleCli\Command;
use SimpleCli\SimpleCli;

class Start implements Command
{
    use Help;

    public function run(SimpleCli $cli): bool
    {


        return true;
    }
}