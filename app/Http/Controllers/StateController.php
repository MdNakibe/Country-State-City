<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
      // Show all states
      public function index()
      {
        $countries = Country::get();
        $states = State::with('country')->get();
        return view('state.index', compact('states','countries'));

      }
  
      // Store a new state
      public function store(Request $request)
      {
          // Validate incoming request data
          $request->validate([
              'name' => 'required|string|max:255',
              'country_id' => 'required|exists:countries,id',
          ]);
  
          try {
              $state = State::create([
                  'name' => $request->name,
                  'country_id' => $request->country_id,
              ]);
              $state->load('country');

              return response()->json([
                  'message' => 'State created successfully!',
                  'data' => $state,
              ], 201);
  
          } catch (\Exception $e) {
              // Handle any errors
              return response()->json([
                  'message' => 'Error creating state. Please try again.',
              ], 500);
          }
      }
  
      public function update(Request $request, $id)
      {
          $request->validate([
              'name' => 'required|string|max:255',
              'country_id' => 'required|exists:countries,id',
          ]);
  
          try {
              $state = State::findOrFail($id);
              $state->update([
                  'name' => $request->name,
                  'country_id' => $request->country_id,
              ]);
                $state->load('country');
              return response()->json([
                  'message' => 'State updated successfully!',
                  'data' => $state,
              ], 200);
  
          } catch (\Exception $e) {
              // Handle any errors
              return response()->json([
                  'message' => 'Error updating state. Please try again.',
              ], 500);
          }
      }
  
      // Delete a state
      public function destroy($id)
      {
          try {
              // Find the state by ID
              $state = State::findOrFail($id);
  
              // Delete the state
              $state->delete();
  
              return response()->json([
                  'message' => 'State deleted successfully!',
              ], 200);
  
          } catch (\Exception $e) {
              return response()->json([
                  'message' => 'Failed to delete the state. Please try again.',
              ], 500);
          }
      }
}
