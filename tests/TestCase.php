<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function signIn($user = null)
    {
        
        // $user = $user ?: factory('App\User')->create();
        // $this->actingAs($user);
        
        // Below is the shorthand for the above code
        $this->actingAs($user = $user?:factory('App\User')->create());
        return $user;
    }
}
