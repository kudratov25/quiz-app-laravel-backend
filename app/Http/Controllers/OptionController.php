<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $options=Option::all();
        return response()->json($options);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'text' => 'required|string',
            'image_url' => 'nullable|url',
            'is_correct' => 'required|boolean',
        ]);

        $option = Option::create($request->all());

        return response()->json($option, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $option = Option::findOrFail($id);
        return response()->json($option);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'text' => 'required|string',
            'image_url' => 'nullable|url',
            'is_correct' => 'required|boolean',
        ]);

        $option = Option::findOrFail($id);
        $option->update($request->all());

        return response()->json($option);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $option = Option::findOrFail($id);
        $option->delete();

        return response()->json(null, 204);
    }
}
