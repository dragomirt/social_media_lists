<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Person;
use Tests\TestCase;

class AccountTest extends TestCase
{
    public function test_can_have_person()
    {
        $person = Person::factory()->create([]);
        $account = Account::factory()->create(['person_id' => $person->id]);

        $this->assertEquals($person->id, $account->person->id);
    }

}
