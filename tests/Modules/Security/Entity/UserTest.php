<?php

namespace App\Tests\Modules\Security\Entity;

use App\Modules\Security\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function every_user_has_default_user_role(): void
    {
        $user = new User();
        self::assertEquals(['ROLE_USER'], $user->getRoles());
    }
}
