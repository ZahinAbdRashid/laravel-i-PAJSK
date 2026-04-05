<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuggestionRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuggestionController extends Controller
{
    // Display a listing of suggestion rules.
    public function index()
    {
        // Delete expired rules automatically
        SuggestionRule::whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->toDateString())
            ->delete();

        $rules = SuggestionRule::orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.suggestions', compact('rules'));
    }

    // Store a newly created suggestion rule.
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'priority' => 'required|integer|min:1',
            'active' => 'boolean',
            'expiry_date' => 'nullable|date',
            'conditions' => 'required|array|min:1',
            'conditions.*.type' => 'required|string',
            'conditions.*.operator' => 'required|string',
            'conditions.*.value' => 'required',
            'actions' => 'required|array|min:1',
            'actions.*.activityName' => 'required|string',
            'actions.*.level' => 'required|string',
            'actions.*.achievement' => 'required|string',
            'actions.*.points' => 'required|integer|min:0',
            'actions.*.message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $rule = SuggestionRule::create([
            'name' => $request->name,
            'priority' => $request->priority,
            'active' => $request->has('active') ? $request->active : true,
            'expiry_date' => $request->expiry_date,
            'conditions' => $request->conditions,
            'actions' => $request->actions,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rule Suggestion Created successfully!',
            'rule' => $rule
        ]);
    }

    // Update the specified suggestion rule.
    public function update(Request $request, $id)
    {
        $rule = SuggestionRule::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'priority' => 'required|integer|min:1',
            'active' => 'boolean',
            'expiry_date' => 'nullable|date',
            'conditions' => 'required|array|min:1',
            'conditions.*.type' => 'required|string',
            'conditions.*.operator' => 'required|string',
            'conditions.*.value' => 'required',
            'actions' => 'required|array|min:1',
            'actions.*.activityName' => 'required|string',
            'actions.*.level' => 'required|string',
            'actions.*.achievement' => 'required|string',
            'actions.*.points' => 'required|integer|min:0',
            'actions.*.message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $rule->update([
            'name' => $request->name,
            'priority' => $request->priority,
            'active' => $request->has('active') ? $request->active : true,
            'expiry_date' => $request->expiry_date,
            'conditions' => $request->conditions,
            'actions' => $request->actions,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rule updated successfully!',
            'rule' => $rule->fresh()
        ]);
    }

    // Remove the specified suggestion rule.
    public function destroy($id)
    {
        $rule = SuggestionRule::findOrFail($id);
        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rule deleted successfully!'
        ]);
    }

    // Get a single rule (for editing)
    public function show($id)
    {
        $rule = SuggestionRule::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'rule' => $rule
        ]);
    }
}

