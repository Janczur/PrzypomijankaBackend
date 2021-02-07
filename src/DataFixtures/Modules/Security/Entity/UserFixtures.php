<?php

namespace App\DataFixtures\Modules\Security\Entity;

use App\Modules\Security\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';

    public const USER_REFERENCE = 'user';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName('Normal User');
        $user->setEmail('test@test.pl');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'secret'));
        $manager->persist($user);

        $adminUser = new User();
        $adminUser->setName('Admin User');
        $adminUser->setEmail('test2@test.pl');
        $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, 'secret'));
        $adminUser->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminUser);

        $manager->flush();

        $this->addReference(self::USER_REFERENCE, $user);
        $this->addReference(self::ADMIN_USER_REFERENCE, $adminUser);
    }
}
