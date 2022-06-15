<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Group;
use App\Models\Person;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    const PEOPLE_QTY = 100;
    const ACCOUNT_PER_PERSON = 2;
    const POSTS_PER_ACCOUNT = 100;

    const LISTS_QTY = 5;
    const PEOPLE_PER_LIST = 15;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // People

        print_r("Creating " . self::PEOPLE_QTY . " people...\n");
        $person = Person::factory()->count(self::PEOPLE_QTY)->create();

        // Accounts

        print_r("Creating " . self::ACCOUNT_PER_PERSON * self::PEOPLE_QTY . " accounts...\n");
        $people = Person::all();
        $people->each(function (Person $model) {
            Account::factory()->count(self::ACCOUNT_PER_PERSON)->create(['person_id' => $model->id]);
        });

        // Posts

        print_r("Creating " . self::ACCOUNT_PER_PERSON * self::PEOPLE_QTY * self::POSTS_PER_ACCOUNT . " posts...\n");
        $accounts = Account::all();
        $accounts->each(function (Account $model) {
            Post::factory()->count(self::POSTS_PER_ACCOUNT)->create(['account_id' => $model->id]);
        });

        // Lists

        print_r("Creating " . self::LISTS_QTY . " lists...\n");
        for ($i = 0; $i < self::LISTS_QTY; $i++) {
            $group = Group::factory()->create();
            $group->people()->attach(Person::inRandomOrder()->limit(self::PEOPLE_PER_LIST)->get());
        }
    }
}
