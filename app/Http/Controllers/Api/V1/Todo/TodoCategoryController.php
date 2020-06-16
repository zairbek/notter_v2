<?php

namespace App\Http\Controllers\Api\V1\Todo;

use App\Http\Controllers\Controller;
use App\Http\Resources\Todo\TodoCategoryResource;
use App\Model\Todo\TodoCategory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TodoCategoryController extends Controller
{
    public function index()
    {
        $todoCategory = TodoCategory::all();

        return response([
            'data' => $todoCategory
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        /** @var $user User */
        $user = auth()->user();

        $category = new TodoCategory();
        $category->title = $request->title;
        $category->slug = Str::slug($request->title);
        $category->description = $request->description;
        $category->parent_id = $request->parent_id;
        $category->user_id = $user->id;
        $category->save();

        return (new TodoCategoryResource($category));
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
