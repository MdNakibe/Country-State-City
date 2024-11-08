<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::with('state')->get();
        $states = State::all();

        return view('city.index', compact('cities', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $city = City::create([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        return response()->json([
            'message' => 'City created successfully.',
            'data' => $city->load('state'),
        ]);
    }

    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $city = City::findOrFail($id);
        $city->update([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        return response()->json([
            'message' => 'City updated successfully.',
            'data' => $city->load('state'),
        ]);
    }
    public function destroy($id){
        $city = City::findOrFail($id);
        $city->delete();
        return response()->json(['message' => 'City deleted successfully.']);
    }
}
