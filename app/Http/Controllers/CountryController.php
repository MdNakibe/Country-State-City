<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(){
        $countries = Country::get();
        return view('country.index', compact('countries'));
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        try {
            $country = Country::create([
                'name' => $request->input('name')
            ]);

            return response()->json([
                'message' => 'Country saved successfully!',
                'data' => $country,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving the location.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request,$id){
        $request->validate(['name' => 'required|string|max:255']);
        $request->validate([
            'name' => 'required|string|max:50',
        ]);
        try {
            $country = Country::findOrFail($id);
            $country->update($request->only('name'));

            return response()->json([
                'message' => 'Country saved successfully!',
                'data' => $country,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving the location.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id){
        try {

            $country = Country::findOrFail($id);
            $country->delete();

            return response()->json([
                'message' => 'Country deleted successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete the country. Please try again.',
            ], 500);
        }
    }
}
