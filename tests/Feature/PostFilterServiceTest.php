<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Group;
use App\Models\Person;
use App\Models\Post;
use App\Services\PostFilterService;
use Tests\TestCase;

class PostFilterServiceTest extends TestCase
{
    public function test_byGroup()
    {
        $person1 = Person::factory()->create();
        $person2 = Person::factory()->create();
        $account1 = Account::factory()->create(['person_id' => $person1->id]);
        $account2 = Account::factory()->create(['person_id' => $person2->id]);
        $post1 = Post::factory()->create(['account_id' => $account1->id]);
        $post2 = Post::factory()->create(['account_id' => $account2->id]);

        $group1 = Group::factory()->create(['name' => 'test group1']);
        $group1->people()->attach($person1);

        $group2 = Group::factory()->create(['name' => 'test group2']);
        $group2->people()->attach($person1);

        $group3 = Group::factory()->create(['name' => 'test group3']);
        $group3->people()->attach($person2);

        $this->assertCount(2, Post::all());

        $ids = collect([$group3->id]);

        $service = new PostFilterService(Post::query());
        $this->assertCount(1, $service->byGroup($ids)->getQuery()->get());
        $this->assertEquals($post2->id, $service->byGroup($ids)->getQuery()->first()->id);
    }

    public function test_byContent()
    {
        $person1 = Person::factory()->create();
        $person2 = Person::factory()->create();
        $account1 = Account::factory()->create(['person_id' => $person1->id]);
        $account2 = Account::factory()->create(['person_id' => $person2->id]);
        $post1 = Post::factory()->create(['account_id' => $account1->id, 'content' => 'is this a test?']);
        $post2 = Post::factory()->create(['account_id' => $account2->id, 'content' => 'just a joke']);

        $group1 = Group::factory()->create(['name' => 'test group1']);
        $group1->people()->attach($person1);

        $group2 = Group::factory()->create(['name' => 'test group2']);
        $group2->people()->attach($person1);

        $group3 = Group::factory()->create(['name' => 'test group3']);
        $group3->people()->attach($person2);

        $this->assertCount(2, Post::all());

        $service = new PostFilterService(Post::query());
        $this->assertCount(1, $service->byContent("a test")->getQuery()->get());
        $this->assertEquals($post1->id, $service->byContent("a test")->getQuery()->first()->id);
    }
}
