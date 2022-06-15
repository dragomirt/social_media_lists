<?php

namespace App\Http\Controllers;

use App\Enum\NetworksEnum;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{

    /**
     * This is the method responsible for the homepage.
     * It showcases all posts with optional filters, which come from the get parameters.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {

        $groups = Group::all();
        $networks = NetworksEnum::cases();
        $posts = Post::with('account', 'account.person', 'account.person.groups')->limit(30)->get();

        return view('home', compact('posts', 'groups', 'networks'));
    }
}
