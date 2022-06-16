<?php

namespace Tests\Feature;

use App\Enum\NetworksEnum;
use App\Models\Account;
use App\Models\Group;
use App\Models\Person;
use App\Models\Post;
use App\Services\PostFilterService;
use Tests\TestCase;

class PostFilterServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // People
        $person1 = Person::factory()->create();
        $person2 = Person::factory()->create();

        // Accounts
        $account1 = Account::factory()->create(['person_id' => $person1->id, 'network' => NetworksEnum::FACEBOOK]);
        $account2 = Account::factory()->create(['person_id' => $person2->id, 'network' => NetworksEnum::TWITTER]);

        // Posts
        Post::factory()->create(['account_id' => $account1->id, 'content' => 'is this a test?', 'posted_at' => "10-06-2022"]);
        Post::factory()->create(['account_id' => $account2->id, 'content' => 'just a joke', 'posted_at' => "18-06-2022"]);

        // Groups
        $group1 = Group::factory()->create(['name' => 'test group1']);
        $group1->people()->attach($person1);

        $group2 = Group::factory()->create(['name' => 'test group2']);
        $group2->people()->attach($person1);

        $group3 = Group::factory()->create(['name' => 'test group3']);
        $group3->people()->attach($person2);
    }

    public function test_byGroup()
    {
        $group = Group::find(3);
        $post = Post::find(2);

        $this->assertCount(2, Post::all());
        $ids = collect([$group->id]);

        $service = new PostFilterService(Post::query());
        $this->assertCount(1, $service->byGroup($ids)->getQuery()->get());
        $this->assertEquals($post->id, $service->getQuery()->first()->id);

        // ignore if null
        $service = new PostFilterService(Post::query());
        $this->assertCount(2, $service->byGroup(null)->getQuery()->get());
    }

    public function test_byContent()
    {
        $post = Post::find(1);

        $this->assertCount(2, Post::all());

        $service = new PostFilterService(Post::query());
        $this->assertCount(1, $service->byContent("a test")->getQuery()->get());
        $this->assertEquals($post->id, $service->getQuery()->first()->id);

        // ignore if null
        $service = new PostFilterService(Post::query());
        $this->assertCount(2, $service->byContent(null)->getQuery()->get());
    }

    public function test_byNetwork()
    {
        $account3 = Account::factory()->create(['person_id' => 1, 'network' => NetworksEnum::FACEBOOK]);
        $post3 = Post::factory()->create(['account_id' => $account3->id, 'content' => 'post3']);

        $account = Post::find(2);
        $post = Post::find(2);

        $this->assertCount(3, Post::all());

        // can filter
        $service = new PostFilterService(Post::query());
        $this->assertCount(2, $service->byNetwork(collect([NetworksEnum::FACEBOOK]))->getQuery()->get());

        // can select multiple
        $service = new PostFilterService(Post::query());
        $this->assertCount(3, $service->byNetwork(collect([NetworksEnum::FACEBOOK, NetworksEnum::TWITTER]))->getQuery()->get());

        $service = new PostFilterService(Post::query());
        $this->assertEquals($post->id, $service->byNetwork(collect([NetworksEnum::TWITTER]))->getQuery()->first()->id);

        // ignore if null
        $service = new PostFilterService(Post::query());
        $this->assertCount(3, $service->byNetwork(null)->getQuery()->get());
    }

    public function test_byDates()
    {
        $this->assertCount(2, Post::all());

        // can filter from
        $service = new PostFilterService(Post::query());
        $this->assertCount(1, $service->byDates('15-06-2022')->getQuery()->get());
        $this->assertEquals([2], $service->getQuery()->pluck('id')->toArray()); // post 2

        // can select multiple
        $service = new PostFilterService(Post::query());
        $this->assertCount(2, $service->byDates('05-06-2022')->getQuery()->get());
        $this->assertEquals([1, 2], $service->getQuery()->pluck('id')->toArray()); // post 1 and 2

        // can select to
        $service = new PostFilterService(Post::query());
        $this->assertCount(1, $service->byDates(null, '16-06-2022')->getQuery()->get());
        $this->assertEquals([1], $service->getQuery()->pluck('id')->toArray()); // post 1

        $post3 = Post::factory()->create(['account_id' => 2, 'content' => 'post3', 'posted_at' => "16-06-2022"]);

        // select from and to
        $service = new PostFilterService(Post::query());
        $this->assertCount(2, $service->byDates('07-06-2022', '17-06-2022')->getQuery()->get());
        $this->assertEquals([1, 3], $service->getQuery()->pluck('id')->toArray()); // post 1

        // ignore if null
        $service = new PostFilterService(Post::query());
        $this->assertCount(3, $service->byDates()->getQuery()->get());
    }

    public function test_can_chain_rules()
    {
        $post3 = Post::factory()->create(['account_id' => 2, 'content' => 'post3', 'posted_at' => "16-06-2022"]);
        $this->assertCount(3, Post::all());

        $service = new PostFilterService(Post::query());
        $result = $service
            ->byNetwork(collect([NetworksEnum::TWITTER]))
            ->byContent('post')
            ->getQuery()
            ->get();

        $this->assertCount(1, $result);
        $this->assertEquals($post3->id, $result->first()->id);
    }
}
