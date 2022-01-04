<?php


namespace App\Support;


use function Sentry\init;

class Sentry
{
    private array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function __invoke(){
        init($this->options);
    }

}