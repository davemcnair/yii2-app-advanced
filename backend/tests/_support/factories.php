<?php

use League\FactoryMuffin\Faker\Facade as Faker;

$fm->define(User::class)->setDefinitions([
    'username' => Faker::name(),
    // generate email
    'email' => Faker::email(),
]);
