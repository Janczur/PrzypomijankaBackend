<?php


namespace App\DataFixtures\Modules\Remembrall\Entity;


use App\DataFixtures\Modules\Security\Entity\UserFixtures;
use App\Modules\Remembrall\Entity\Reminder;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReminderFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CyclicFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 10; $i++) {
            $reminder = new Reminder();
            $interval = new DateInterval("P{$i}D");
            $remindAt = new DateTime();
            if ($i % 2 === 0) {
                $user = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
                $remindAt->sub($interval); // x day before today
            } else {
                $user = $this->getReference(UserFixtures::USER_REFERENCE);
                $remindAt->add($interval); // x days after today
            }
            $reminder->setTitle('Test title');
            $reminder->setDescription('Test description');
            $reminder->setUser($user);
            $reminder->setRemindAt($remindAt);
            $reminder->setPreRemindAt($remindAt);
            $reminder->setChannels(['email']);
            if ($i === 0 || $i === 1 || $i === 2 || $i === 3) {
                $cyclic = $this->getReference(CyclicFixtures::getReferenceKey($i));
            } else {
                $cyclic = null;
            }
            $reminder->setCyclic($cyclic);
            $manager->persist($reminder);
            $this->addReference(self::getReferenceKey($i), $reminder);
        }
        $manager->flush();
    }

    public static function getReferenceKey(int $i): string
    {
        return sprintf('reminder_%s', $i);
    }
}