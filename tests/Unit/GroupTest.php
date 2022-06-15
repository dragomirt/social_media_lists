<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\Person;
use Tests\TestCase;

class GroupTest extends TestCase
{
    public function test_can_have_people()
    {
        $person1 = Person::factory()->create();
        $person2 = Person::factory()->create();
        $group = Group::factory()->create();

        $group->people()->attach($person1);
        $group->people()->attach($person2);

        $this->assertCount(2, $group->people);
    }
}
