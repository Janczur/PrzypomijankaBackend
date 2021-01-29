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
    public const QUARTER_TYPE = 'quarter-type';
    public const YEAR_TYPE = 'year-type';

    public function load(ObjectManager $manager): void
    {
        $dayType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::DAY]);
        $weekType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::MONTH]);
        $monthType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::MONTH]);
        $quarterType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::QUARTER]);
        $yearType = (new CyclicType())->setName(CyclicTypes::$display[CyclicTypes::YEAR]);

        $manager->persist($dayType);
        $manager->persist($weekType);
        $manager->persist($monthType);
        $manager->persist($quarterType);
        $manager->persist($yearType);

        $this->addReference(self::DAY_TYPE, $dayType);
        $this->addReference(self::WEEK_TYPE, $weekType);
        $this->addReference(self::MONTH_TYPE, $monthType);
        $this->addReference(self::QUARTER_TYPE, $quarterType);
        $this->addReference(self::YEAR_TYPE, $yearType);

        $manager->flush();
    }
}