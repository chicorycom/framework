<?php


namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\fileExists;


class BasDirTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_base_dir()
    {
        $response = file_exists(base_path('src/helpers.php'));
        //dd($response);
        $this->assertTrue($response);
    }
}