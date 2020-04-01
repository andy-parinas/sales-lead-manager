<?php

namespace Tests;

use App\User;

trait TestHelper 
{

    protected function createHeadOfficeUser()
    {
        return factory(User::class)->create(['user_type' => User::HEAD_OFFICE]);
    }


}