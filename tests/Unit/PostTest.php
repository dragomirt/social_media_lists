<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Person;
use App\Models\Post;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function test_can_have_account()
    {
        $person = Person::factory()->create(['name' => 'Dragomir']);
        $account = Account::factory()->create(['person_id' => $person->id]);
        
        $post = Post::factory()->create(['account_id' => $account->id]);
        
        $this->assertEquals($account->id, $post->account->id);
    }
}
