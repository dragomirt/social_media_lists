<?php

namespace Tests\Feature\Http\Controllers;

use App\Enum\NetworksEnum;
use App\Http\Controllers\HomeController;
use App\Models\Account;
use App\Models\Group;
use App\Models\Person;
use App\Models\Post;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class HomeControllerTest extends TestCase
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

    public function test_index_returns_all_records_on_empty_filters()
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('posts', Post::with('account', 'account.person', 'account.person.groups')->paginate(100));
    }

    public function test_index_can_filter_via_get_parameters()
    {
        $post3 = Post::factory()->create(['account_id' => 1, 'content' => 'post3', 'posted_at' => "10-06-2022"]);
        $post4 = Post::factory()->create(['account_id' => 2, 'content' => 'post4', 'posted_at' => "18-06-2022"]);

        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('posts', Post::with('account', 'account.person', 'account.person.groups')->paginate(100));


        $this->get(route('home') . "?filter_content=post")
            ->assertOk()
            ->assertViewHas('posts', Post::with('account', 'account.person', 'account.person.groups')->whereIn('id', [3, 4])->paginate(100));


        $this->get(route('home') . "?filter_content=post&filter_networks%5B%5D=facebook")
            ->assertOk()
            ->assertViewHas('posts', Post::with('account', 'account.person', 'account.person.groups')->whereIn('id', [3])->paginate(100));
    }

    public function test_getFilterNetworks()
    {
        $reflection = new ReflectionClass(HomeControllerTest::class);
        $method = new ReflectionMethod(HomeController::class . '::getFilterNetworks');
        $method->setAccessible(true);

        $request = new Request();

        $request->replace(['filter_networks' => [NetworksEnum::FACEBOOK->value]]);
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals(collect([NetworksEnum::FACEBOOK->value]), $collection);

        $request->replace(['filter_networks' => [null, NetworksEnum::FACEBOOK->value]]);
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals(collect([1 => NetworksEnum::FACEBOOK->value]), $collection); // because the first value was null and filtered

        $request->replace(['filter_networks' => [ NetworksEnum::TWITTER->value, NetworksEnum::FACEBOOK->value]]);
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals(collect([ NetworksEnum::TWITTER->value, NetworksEnum::FACEBOOK->value]), $collection); // because the first value was null and filtered

        $request = new Request();
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertNull($collection);
    }

    public function test_getFilterGroups()
    {
        $reflection = new ReflectionClass(HomeControllerTest::class);
        $method = new ReflectionMethod(HomeController::class . '::getFilterGroups');
        $method->setAccessible(true);

        $request = new Request();

        $request->replace(['filter_groups' => [1]]);
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals(collect([1]), $collection);

        $request->replace(['filter_groups' => [null, 1]]);
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals(collect([1 => 1]), $collection);

        $request->replace(['filter_groups' => [null]]);
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertNull(null, $collection); // because the first value was null and filtered

        $request = new Request();
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertNull($collection);
    }

    public function test_getFilterContent()
    {
        $reflection = new ReflectionClass(HomeControllerTest::class);
        $method = new ReflectionMethod(HomeController::class . '::getFilterContent');
        $method->setAccessible(true);

        $request = new Request();

        $request->replace(['filter_content' => "a test"]);
        $string = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals("a test", $string);

        $request->replace(['filter_content' => "a"]);
        $string = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals("a", $string);

        $request->replace(['filter_content' => "joke"]);
        $string = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals("joke", $string);

        $request->replace(['filter_content' => ""]);
        $string = $method->invokeArgs($reflection, [$request]);
        $this->assertNull($string);

        $request = new Request();
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertNull($collection);
    }

    public function test_getFilterFromDate()
    {
        $reflection = new ReflectionClass(HomeControllerTest::class);
        $method = new ReflectionMethod(HomeController::class . '::getFilterFromDate');
        $method->setAccessible(true);

        $request = new Request();

        $request->replace(['filter_from' => "10-06-2022"]);
        $string = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals("10-06-2022", $string);

        $request->replace(['filter_from' => null]);
        $string = $method->invokeArgs($reflection, [$request]);
        $this->assertNull($string);

        $request = new Request();
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertNull($collection);
    }

    public function test_getFilterToDate()
    {
        $reflection = new ReflectionClass(HomeControllerTest::class);
        $method = new ReflectionMethod(HomeController::class . '::getFilterToDate');
        $method->setAccessible(true);

        $request = new Request();

        $request->replace(['filter_to' => "10-06-2022"]);
        $string = $method->invokeArgs($reflection, [$request]);
        $this->assertEquals("10-06-2022", $string);

        $request->replace(['filter_to' => null]);
        $string = $method->invokeArgs($reflection, [$request]);
        $this->assertNull($string);

        $request = new Request();
        $collection = $method->invokeArgs($reflection, [$request]);
        $this->assertNull($collection);
    }

    public function test_index_prefill_filter_values()
    {
        $this->get(route('home') . "?filter_content=post")
            ->assertOk()
            ->assertSee("post</textarea>", false);

        $this->get(route('home') . "?filter_networks%5B%5D=facebook")
            ->assertOk()
            ->assertSee('name="filter_networks[]" value="facebook"', false);
    }
}
