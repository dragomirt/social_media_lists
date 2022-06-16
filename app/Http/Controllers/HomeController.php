<?php

namespace App\Http\Controllers;

use App\Enum\NetworksEnum;
use App\Models\Group;
use App\Models\Post;
use App\Services\PostFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $query = Post::with('account', 'account.person', 'account.person.groups')
            ->limit(30);

        $filter_service = new PostFilterService($query);
        $filter_service
            ->byNetwork(self::getFilterNetworks($request));

        $posts = $filter_service->getQuery()->get();
        return view('home', compact('posts', 'groups', 'networks'));
    }

    /**
     * Returns the networks from the get parameters
     *
     * @param Request $request
     * @return Collection
     */
    protected static function getFilterNetworks(Request $request): ?Collection
    {
        return $request->get('filter_networks') ? // is the parameter set?
            collect($request->get('filter_networks'))->filter(function($x) {
                return null !== $x;
            }) : null;
    }
}
