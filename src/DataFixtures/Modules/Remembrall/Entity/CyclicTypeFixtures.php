<?php


namespace App\DataFixtures\Modules\Remembrall\Entity;


use App\Modules\Remembrall\Abstracts\CyclicTypes;
use App\Modules\Remembrall\Entity\CyclicType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CyclicTypeFixtures extends Fixture
{

    public const DAY_TYPE = 'day-type';
    public const WEEK_TYPE = 'week-type';
    public const MONTH_TYPE = 'month-type';
    public const YEAR_TYPE = 'year-type';

    public function load(ObjectManager $manager): void
    {
        $dayType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::DAY]);
        $weekType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::WEEK]);
        $monthType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::MONTH]);
        $yearType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::YEAR]);

        $manager->persist($dayType);
        $manager->persist($weekType);
        $manager->persist($monthType);
        $manager->persist($yearType);

        $this->addReference(self::DAY_TYPE, $dayType);
        $this->addReference(self::WEEK_TYPE, $weekType);
        $this->addReference(self::MONTH_TYPE, $monthType);
        $this->addReference(self::YEAR_TYPE, $yearType);

        $manager->flush();
    }
}