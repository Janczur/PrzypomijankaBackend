<?php


namespace App\DataFixtures\Modules\Remembrall\Entity;


use App\Modules\Remembrall\Entity\PreReminder;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PreReminderFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 4; $i++) {
            $preReminder = new PreReminder();
            $preReminder->setDaysBefore($i + 2);
            $preReminder->setRemindAt((new DateTime())->add(new DateInterval("P{$i}D")));
            $manager->persist($preReminder);
            $this->addReference(self::getReferenceKey($i), $preReminder);
        }
        $manager->flush();
    }

    public static function getReferenceKey(int $i): string
    {
        return sprintf('pre_reminder_%s', $i);
    }
}