<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function get(Request $request)
    {
        $companies = \App\Models\Company::latest()->paginate(10);

        $response = [
            'success' => true,
            'message' => 'Successfully All retevied data!',
            'data' => $companies,
        ];

        return response()->json($response);
    }

    public function post(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'logo' => 'nullable|image'
        ]);

        $requestAll = $request->only('name', 'email', 'website');
        if ($request->file('logo')) {
            $requestAll['logo'] = $request->file('logo')->store('public');
        }

        $company = \App\Models\Company::create($requestAll);

        $response = [
            'success' => true,
            'message' => 'Successfully created data!',
            'data' => $company,
        ];

        return response()->json($response);
    }

    public function delete($id)
    {
        \App\Models\Company::findOrFail($id)->delete();

        $response = [
            'success' => true,
            'message' => 'Successfully delete data!',
        ];

        return response()->json($response);
    }
}
