<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TaskResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create($validated);

        return (new CategoryResource($category))
            ->additional([
                'message' => 'Category created successfully'
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id)
    {
        return new CategoryResource(
            Category::findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'
                . $category->id,
        ]);

        $category->update($validated);

        return new CategoryResource($category);
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
