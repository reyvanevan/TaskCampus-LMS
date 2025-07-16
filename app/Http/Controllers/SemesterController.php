<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $semesters = Semester::orderBy('end_date', 'desc')->paginate(10);
        return view('semesters.index', compact('semesters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('semesters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        // If this semester is set as active, deactivate all other semesters
        if (isset($validatedData['is_active']) && $validatedData['is_active']) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }

        Semester::create($validatedData);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Semester $semester): View
    {
        $courses = $semester->courses()->with('lecturer')->paginate(10);
        return view('semesters.show', compact('semester', 'courses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semester $semester): View
    {
        return view('semesters.edit', compact('semester'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        // If this semester is set as active, deactivate all other semesters
        if (isset($validatedData['is_active']) && $validatedData['is_active']) {
            Semester::where('id', '!=', $semester->id)
                  ->where('is_active', true)
                  ->update(['is_active' => false]);
        }

        $semester->update($validatedData);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester): RedirectResponse
    {
        // Check if semester has associated courses
        if ($semester->courses()->count() > 0) {
            return back()->with('error', 'Cannot delete semester with associated courses.');
        }

        $semester->delete();

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester deleted successfully.');
    }
}