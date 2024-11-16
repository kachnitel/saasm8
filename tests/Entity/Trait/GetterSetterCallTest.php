<?php

namespace App\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\Trait\GetterSetterCall::__call
 */
class GetterSetterCallTest extends TestCase
{
    public function testCallableMethods(): void
    {
        $billingCategory = new \App\Entity\BillingCategory();
        $billingCategory->setName('Test');
        $billingCategory->setRate(1.23);
        $billingCategory->setColor('123456');

        $this->assertEquals('Test', $billingCategory->getName());
        $this->assertEquals(1.23, $billingCategory->getRate());
        $this->assertEquals('123456', $billingCategory->getColor());
        $this->assertEquals(null, $billingCategory->getId());

        $timeEntry = new \App\Entity\TimeEntry();
        $billingCategory->addTimeEntry($timeEntry);
        $this->assertEquals($billingCategory, $timeEntry->getBillingCategory());

    }

    public function testSetIdThrowsError(): void
    {
        $billingCategory = new \App\Entity\BillingCategory();
        $this->expectException(\Error::class);
        $billingCategory->setId(1);
    }

    public function testAddRemoveGetEntries(): void
    {
        $billingCategory = new \App\Entity\BillingCategory();
        $timeEntry = new \App\Entity\TimeEntry();
        $billingCategory->addTimeEntry($timeEntry);
        $this->assertEquals($timeEntry, $billingCategory->getTimeEntries()->first());
        $billingCategory->removeTimeEntry($timeEntry);
        $this->assertEquals(null, $billingCategory->getTimeEntries()->first());
    }

    public function testSetCollectionThrowsError(): void
    {
        $billingCategory = new \App\Entity\BillingCategory();
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method setTimeEntries does not exist');
        $billingCategory->setTimeEntries(new ArrayCollection());
    }

    public function testInvalidMethodThrowsError(): void
    {
        $billingCategory = new \App\Entity\BillingCategory();
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method addInvalidMethod does not exist');
        $billingCategory->addInvalidMethod();
    }

    public function testGetter(): void
    {
        $billingCategory = new \App\Entity\BillingCategory();
        $billingCategory->setName('Test');
        $this->assertEquals('Test', $billingCategory->name);

        $this->expectException(\Error::class);
        $billingCategory->invalidProperty;
    }

    public function testIsset(): void
    {
        $billingCategory = new \App\Entity\BillingCategory();
        $billingCategory->setName('Test');
        $this->assertTrue(isset($billingCategory->name));
        $this->assertFalse(isset($billingCategory->invalidProperty));
    }
}
