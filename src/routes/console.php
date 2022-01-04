<?php

use Boot\Support\Console;

Console::command('inspire', function () {
    echo '';
});
Console::command('tinker', function () {
    $team = \App\Models\User::first();

    $this->info("$team->name belongs to ");
});
