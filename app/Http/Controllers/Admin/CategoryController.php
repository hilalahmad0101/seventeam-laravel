<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:categories,name',
                'max:20',
                'min:1'
            ],
            'description' => [
                'nullable',
                'string'
            ]
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return to_route('admin.category.list')->with('success', 'Category Create Successfully');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.update', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                'min:1',
                Rule::unique('categories', 'name')->ignore($id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);
        Category::findOrFail($id)->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return to_route('admin.category.list')->with('success', 'Category Update Successfully');
    }

    public function delete($id)
    {
        Category::destroy($id);

        return to_route('admin.category.list')->with('success', 'Category Delete successfully');
    }
}
