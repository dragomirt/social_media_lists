<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::all();

        return view('groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $people = Person::all();

        return view('groups.create', compact('people'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'group-name' => 'string|max:255',
            'attached-people' => 'array'
        ]);

        $group = new Group();
        $attached_people = $request->input('attached-people');
        $group->name = $request->input('group-name');
        $group->save();

        array_walk($attached_people, function($id) use ($group) {
            $group->people()->attach($id);
        });

        return redirect(route('dashboard.group.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::whereId($id)->with('people')->first();

        if (null === $group) {
            return abort(JsonResponse::HTTP_NOT_FOUND);
        }

        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = Group::whereId($id)->with('people')->first();

        if (null === $group) {
            return abort(JsonResponse::HTTP_NOT_FOUND);
        }

        $group->delete();

        return redirect(route('dashboard.group.index'));
    }
}
