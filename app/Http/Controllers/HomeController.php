<?php

namespace App\Http\Controllers;

use App\Enum\NetworksEnum;
use App\Models\Group;
use App\Models\Post;
use App\Services\PostFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

        $begin = microtime(true); // log the duration of the query
        $query = Post::with('account', 'account.person', 'account.person.groups');

        $filter_service = new PostFilterService($query);
        $filter_service
            ->byNetwork(self::getFilterNetworks($request))
            ->byGroup(self::getFilterGroups($request))
            ->byContent(self::getFilterContent($request))
            ->byDates(self::getFilterFromDate($request), self::getFilterToDate($request));

        $posts = $filter_service->getQuery()->paginate(100);
        $query_time = (microtime(true) - $begin) * 1000; // convert seconds to ms

        return view('home', compact('posts', 'groups', 'networks', 'query_time'));
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

    /**
     * Returns the groups from the get parameters
     *
     * @param Request $request
     * @return Collection
     */
    protected static function getFilterGroups(Request $request): ?Collection
    {
        return $request->get('filter_groups') ? // is the parameter set?
            collect($request->get('filter_groups'))->filter(function($x) {
                return null !== $x;
            }) : null;
    }

    /**
     * Returns the content from the get parameters
     *
     * @param Request $request
     * @return Collection
     */
    protected static function getFilterContent(Request $request): ?string
    {
        $content = $request->get('filter_content');

        if (null === $content) {
            return null;
        }

        $content = Str::limit(htmlspecialchars(trim($content)), 500, '');
        if ("" === $content) {
            return null;
        }

        return $content;
    }

    /**
     * Returns the from date from the get parameters
     *
     * @param Request $request
     * @return Collection
     */
    protected static function getFilterFromDate(Request $request): ?string
    {
        return $request->get('filter_from');
    }

    /**
     * Returns the from date from the get parameters
     *
     * @param Request $request
     * @return Collection
     */
    protected static function getFilterToDate(Request $request): ?string
    {
        return $request->get('filter_to');
    }
}
