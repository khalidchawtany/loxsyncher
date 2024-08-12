<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\Categories\StoreCategory;
use App\Http\Requests\Categories\UpdateCategory;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_category', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_category', ['only' => ['create']]);
        $this->middleware('permission:update_category', ['only' => ['update']]);
        $this->middleware('permission:destroy_category', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('categories.index');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(Category::class)
            ->allowedFilters('id', 'name')
            ->jsonJPaginate();
    }

    public function jsonList(Request $request)
    {
        if ($request->filled('q')) {
            return Category::selectRaw('categories.id as category_id, categories.name as category_name')
                ->whereRaw("categories.plate like '{$request->q}%'")
                ->take(10)->get();
        }

        return Category::selectRaw('categories.id as category_id, categories.name as category_name')
            ->take(10)->get();
    }

    /**
     * Create a new category instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Category
     */
    protected function create(StoreCategory $request)
    {
        $category = Category::create($request->input());

        return ezReturnSuccessMessage('Category created successfully!', $category);
    }

    public function update(UpdateCategory $request)
    {
        $category = Category::findOrFail($request->id);

        $category->update($request->input());

        return ezReturnSuccessMessage('Category updated successfully!');
    }

    public function destroy(Request $request)
    {
        $category = Category::findOrFail($request->id);

        $category->delete();

        return ezReturnSuccessMessage('Category removed successfully!');
    }
}
