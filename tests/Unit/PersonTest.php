<?php

namespace Tests\Unit;

use App\Enum\NetworksEnum;
use App\Models\Account;
use App\Models\PeopleList;
use App\Models\Person;
use Tests\TestCase;

class PersonTest extends TestCase
{
    public function test_can_have_accounts()
    {
        $person = Person::factory()->create(['name' => 'Dragomir']);

        $account1 = Account::factory()->create([
            'person_id' => $person->id,
            'network' => NetworksEnum::TWITTER,
            'handle' => 'dragomir_twitter'
        ]);
        $account2 = Account::factory()->create([
            'person_id' => $person->id,
            'network' => NetworksEnum::FACEBOOK,
            'handle' => 'dragomir_facebook'
        ]);

        $this->assertCount(2, $person->accounts);
        $this->assertEquals("dragomir_twitter", $person->accounts->first()->handle);
    }

    public function test_can_have_lists()
    {
        $person = Person::factory()->create(['name' => 'Dragomir']);

        $list = PeopleList::factory()->create([
            'name' => 'test list'
        ]);

        $list->people()->attach($person);

        $this->assertCount(1, $person->lists);
        $this->assertEquals("test list", $person->lists->first()->name);
    }
}
