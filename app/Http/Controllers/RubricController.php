<?php

namespace App\Http\Controllers;

use App\Models\Rubric;
use App\Models\RubricCriteria;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RubricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rubrics = Rubric::with(['assignment', 'criteria'])
            ->whereHas('assignment', function ($query) {
                $query->whereHas('course', function ($q) {
                    $q->where('lecturer_id', Auth::id());
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('rubrics.index', compact('rubrics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Assignment $assignment)
    {
        $assignment->load('course');
        
        // Check if user is the lecturer of this course
        if ($assignment->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if rubric already exists for this assignment
        if ($assignment->rubric) {
            return redirect()->route('rubrics.show', $assignment->rubric)
                ->with('info', 'A rubric already exists for this assignment.');
        }

        return view('rubrics.create', compact('assignment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Assignment $assignment)
    {
        $assignment->load('course');
        
        // Check if user is the lecturer of this course
        if ($assignment->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if rubric already exists
        if ($assignment->rubric) {
            return redirect()->route('rubrics.show', $assignment->rubric)
                ->with('error', 'A rubric already exists for this assignment.');
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'total_points' => 'required|numeric|min:1',
                'criteria' => 'required|array|min:1',
                'criteria.*.title' => 'required|string|max:255',
                'criteria.*.description' => 'nullable|string',
                'criteria.*.max_score' => 'required|numeric|min:0', // Changed min from 1 to 0
            ]);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error validating rubric: ' . $e->getMessage()]);
        }

        // Create the rubric
        $rubric = Rubric::create([
            'assignment_id' => $assignment->id,
            'name' => $request->name,
            'description' => $request->description,
            'total_points' => $request->total_points,
        ]);

        // Create criteria
        try {
            $totalCriteriaPointsCheck = 0;
            
            foreach ($request->criteria as $criteriaData) {
                // Make sure max_score is a valid number
                $maxScore = is_numeric($criteriaData['max_score']) ? (float) $criteriaData['max_score'] : 0;
                $totalCriteriaPointsCheck += $maxScore;
                
                RubricCriteria::create([
                    'rubric_id' => $rubric->id,
                    'title' => $criteriaData['title'],
                    'description' => $criteriaData['description'] ?? null,
                    'max_score' => $maxScore,
                ]);
            }
            
            // Optional: Add a log to check if criteria points match total_points
            if (abs($totalCriteriaPointsCheck - $request->total_points) > 0.01) {
                // Just log this, don't stop execution - could be intentional
                \Illuminate\Support\Facades\Log::warning("Rubric total points ({$request->total_points}) doesn't match sum of criteria points ({$totalCriteriaPointsCheck})");
            }
        } catch (\Exception $e) {
            // Log error and redirect back with message
            \Illuminate\Support\Facades\Log::error("Error creating rubric criteria: " . $e->getMessage());
            
            // Roll back rubric creation
            $rubric->delete();
            
            return back()
                ->withInput()
                ->withErrors(['criteria_error' => 'Error creating criteria: ' . $e->getMessage()]);
        }

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Rubric created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rubric $rubric)
    {
        $rubric->load(['assignment.course', 'criteria']);
        
        // Check if user is the lecturer of this course
        if ($rubric->assignment->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('rubrics.show', compact('rubric'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rubric $rubric)
    {
        $rubric->load(['assignment.course', 'criteria']);
        
        // Check if user is the lecturer of this course
        if ($rubric->assignment->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('rubrics.edit', compact('rubric'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rubric $rubric)
    {
        $rubric->load('assignment.course');
        
        // Check if user is the lecturer of this course
        if ($rubric->assignment->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_points' => 'required|numeric|min:1',
            'criteria' => 'required|array|min:1',
            'criteria.*.title' => 'required|string|max:255',
            'criteria.*.description' => 'nullable|string',
            'criteria.*.max_score' => 'required|numeric|min:1',
        ]);

        // Update rubric
        $rubric->update([
            'name' => $request->name,
            'description' => $request->description,
            'total_points' => $request->total_points,
        ]);

        // Delete existing criteria and create new ones
        $rubric->criteria()->delete();
        
        foreach ($request->criteria as $criteriaData) {
            RubricCriteria::create([
                'rubric_id' => $rubric->id,
                'title' => $criteriaData['title'],
                'description' => $criteriaData['description'] ?? null,
                'max_score' => $criteriaData['max_score'],
            ]);
        }

        return redirect()->route('rubrics.show', $rubric)
            ->with('success', 'Rubric updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rubric $rubric)
    {
        $rubric->load('assignment.course');
        
        // Check if user is the lecturer of this course
        if ($rubric->assignment->course->lecturer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment = $rubric->assignment;
        $rubric->delete();

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Rubric deleted successfully!');
    }
}
