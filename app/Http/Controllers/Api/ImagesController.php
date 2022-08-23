<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    public function get(Request $request)
    {
        $file = Storage::path($request->path);
        return response()->file($file);
    }
}
