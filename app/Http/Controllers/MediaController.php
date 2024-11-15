<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif|max:4096',
        ]);
        $path = $request->file('file')->store('uploads/media', 'public');
        return response()->json([
            'url' => asset('storage/' . $path),
            'path' => $path,
        ]);
    }
}
