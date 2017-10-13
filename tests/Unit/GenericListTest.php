<?php

use PHPUnit\Framework\TestCase;
use PHPCollections\Collections\GenericList;

class GenericListTest extends TestCase
{
    private $list;

    public function setUp()
    {
        $this->list = new GenericList(ArrayObject::class);
        $this->list->add(new ArrayObject(['name' => 'John']));
        $this->list->add(new ArrayObject(['name' => 'Finch']));
        $this->list->add(new ArrayObject(['name' => 'Shaw']));
        $this->list->add(new ArrayObject(['name' => 'Carter']));
        $this->list->add(new ArrayObject(['name' => 'Kara']));
        $this->list->add(new ArrayObject(['name' => 'Snow']));
        $this->list->add(new ArrayObject(['name' => 'Zoey']));
        $this->list->add(new ArrayObject(['name' => 'Cal']));
        $this->list->add(new ArrayObject(['name' => 'Lionel']));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddToList()
    {
        $this->assertCount(9, $this->list);
        $this->list->add(new Exception()); // Here an InvalidArgumentException is thrown!
    }

    public function testClearList()
    {
        $this->list->clear();
        $this->assertCount(0, $this->list);
        $this->setUp();
    }

    public function testFindIntoList()
    {
        $arrayObject = $this->list->find(function ($value) {
            return $value['name'] === 'Finch';
        });

        $this->assertEquals('Finch', $arrayObject->offsetGet('name'));
        $this->assertNotEquals('Lionel', $arrayObject->offsetGet('name'));
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testGetFromList()
    {
        $arrayObject = $this->list->get(2);
        $this->assertEquals('Shaw', $arrayObject->offsetGet('name'));
        $this->list->get(9); // Here an OutOfRangeException is thrown!
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testRemoveFromList()
    {
        $this->list->remove(0);
        $this->assertCount(8, $this->list);
        $arrayObject = $this->list->get(0);
        $this->assertNotEquals('John', $arrayObject->offsetGet('name'));
        $this->list->remove(9); // Here an OutOfRangeException is thrown!
    }

    /**
     * @expectedException ArgumentCountError
     */
    public function testFilterList()
    {
        $newList = $this->list->filter(function ($value, $key) {
            return strlen($value['name']) <= 4;
        });

        $this->assertEquals('John', $newList->get(0)->offsetGet('name'));
        $this->assertEquals('Shaw', $newList->get(1)->offsetGet('name'));

        $anotherList = $this->list->filter(function ($value, $key) {
            return strlen($value['name']) > 10;
        });

        $this->assertNull($anotherList);

        $oneMoreList = $this->list->filter(function ($value, $key) {
            return strlen($value['name']) <= 4;
        }, false); // Here an ArgumentCountError is thrown!
    }

    public function testSearchInList()
    {
        $newList = $this->list->search(function ($value) {
            return strlen($value['name']) > 4;
        });
        $this->assertCount(3, $newList);
        $this->assertEquals('Finch', $newList->get(0)->offsetGet('name'));

        $anotherList = $this->list->search(function ($value) {
            return strlen($value['name']) > 10;
        });
        $this->assertNull($anotherList);
    }

    public function testMapList()
    {
        $newList = $this->list->map(function ($value) {
            $value['name'] = sprintf('%s %s', 'Sr.', $value['name']);
            return $value;
        });
        $this->assertEquals('Sr. John', $newList->get(0)->offsetGet('name'));
    }

    public function testSortList()
    {
        $isSorted = $this->list->sort(function ($a, $b) {
            return $a->offsetGet('name') <=> $b->offsetGet('name');
        });
        $this->assertTrue($isSorted);
        $this->assertEquals('Cal', $this->list->get(0)->offsetGet('name'));
        $this->setUp();
    }

    /**
     * @expectedException PHPCollections\Exceptions\InvalidOperationException
     */
    public function testReverseList()
    {
        $reversedList = $this->list->reverse();
        $this->assertEquals('Lionel', $reversedList->get(0)->offsetGet('name'));

        $newList = new GenericList(ArrayObject::class);
        $newReversedList = $newList->reverse(); // Here an InvalidOperationException is thrown!
    }

    /**
     * @expectedException PHPCollections\Exceptions\InvalidOperationException
     */
    public function testRandElementFromList()
    {
        $randElement = $this->list->rand();
        $this->assertArrayHasKey('name', $randElement);

        $newList = new GenericList(ArrayObject::class);
        $newList->rand(); // Here an InvalidOperationException is thrown!
    }

    public function testIndexExistsInList()
    {
        $this->assertTrue($this->list->exists(0));
        $this->assertFalse($this->list->exists(20));
    }

    public function testCombineSomeLists()
    {
        $newList = $this->list->merge(
            [new ArrayObject(['name' => 'Max']), new ArrayObject(['name' => 'Alex'])]
        );
        $this->assertCount(11, $newList);
        $this->assertEquals('Max', $newList->get(9)->offsetGet('name'));
    }

    public function testGetFirstElementOfList()
    {
        $arrayObject = $this->list->first();
        $this->assertEquals('John', $arrayObject->offsetGet('name'));
    }

    public function testGetLastElementOfList()
    {
        $arrayObject = $this->list->last();
        $this->assertEquals('Lionel', $arrayObject->offsetGet('name'));
    }
}