<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of active projects.
     */
    public function index()
    {
        $pageTitle = 'Investment Projects';

        // Get active projects with their active plans
        $projects = Project::where('status', 1)
            ->where('testing', 0)
            ->with(['activePlans' => function($query) {
                $query->where('status', 1);
            }])
            ->withCount(['activePlans' => function($query) {
                $query->where('status', 1);
            }])
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $featuredProjects = Project::where('status', 1)
            ->where('testing', 0)
            ->where('featured', 1)
            ->with(['activePlans' => function($query) {
                $query->where('status', 1);
            }])
            ->withCount(['activePlans' => function($query) {
                $query->where('status', 1);
            }])
            ->limit(6)
            ->get();

        return view(activeTemplate() . 'projects.index', compact('pageTitle', 'projects', 'featuredProjects'));
    }

    /**
     * Display the specified project with its plans.
     */
    public function show($id)
    {
        $project = Project::where('status', 1)
            ->where('testing', 0)
            ->with(['activePlans.timeSetting' => function($query) {
                $query->where('status', 1);
            }])
            ->findOrFail($id);

        $pageTitle = $project->name;

        // Get other projects for suggestions
        $otherProjects = Project::where('status', 1)
            ->where('testing', 0)
            ->where('id', '!=', $id)
            ->withCount(['activePlans' => function($query) {
                $query->where('status', 1);
            }])
            ->limit(3)
            ->get();

        return view(activeTemplate() . 'projects.show', compact('pageTitle', 'project', 'otherProjects'));
    }
}
