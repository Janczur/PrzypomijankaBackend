<?php

namespace App\DataFixtures;

use App\Modules\Security\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Test');
        $user->setEmail('test@test.pl');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'secret'
        ));
        $manager->persist($user);
        $manager->flush();
    }
}
