<?php


namespace App\Http\Controllers;


use Psr\Http\Message\ResponseInterface;

class DefaultController
{
    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return view( 'pages.default')->withStatus(404);
    }
}