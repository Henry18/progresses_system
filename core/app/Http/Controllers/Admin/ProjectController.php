<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Lib\FileManager;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    public function index()
    {
        $pageTitle = "Investment Projects";
        $projects = Project::withCount('plans')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.project.index', compact('pageTitle', 'projects'));
    }

    public function create()
    {
        $pageTitle = 'New Project';
        return view('admin.project.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $project = new Project();
        $this->saveData($project, $request);

        $notify[] = ['success', 'Project created successfully'];
        return redirect()->route('admin.project.show', $project->id)->withNotify($notify);
    }

    public function show($id)
    {
        $pageTitle = 'Project Details';
        $project = Project::with('plans.timeSetting')->findOrFail($id);
        return view('admin.project.show', compact('pageTitle', 'project'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Project';
        $project = Project::findOrFail($id);
        return view('admin.project.edit', compact('pageTitle', 'project'));
    }

    public function update(Request $request, $id)
    {
        $this->validation($request, $id);
        $project = Project::findOrFail($id);
        $this->saveData($project, $request);

        $notify[] = ['success', 'Project updated successfully'];
        return back()->withNotify($notify);
    }

    protected function saveData($project, $request)
    {
        // Manejar carga de imagen
        if ($request->hasFile('image')) {
            try {
                $fileManager = new FileManager($request->image);
                $fileManager->path = $fileManager->projectImage()->path;
                $fileManager->size = $fileManager->projectImage()->size;
                $fileManager->old = $project->image ?? null;
                $fileManager->upload();
                $project->image = $fileManager->filename;
            } catch (\Exception $e) {
                throw ValidationException::withMessages(['image' => 'Error uploading image: ' . $e->getMessage()]);
            }
        }

        // Manejar carga de PDF (opcional)
        if ($request->hasFile('pdf')) {
            try {
                $fileManager = new FileManager($request->pdf);
                $fileManager->path = $fileManager->projectFile()->path;
                $fileManager->old = $project->pdf ?? null;
                $fileManager->upload();
                $project->pdf = $fileManager->filename;
            } catch (\Exception $e) {
                throw ValidationException::withMessages(['pdf' => 'Error uploading PDF: ' . $e->getMessage()]);
            }
        }

        $project->name = $request->name;
        $project->description = $request->description;
        $project->minimum_investment = $request->minimum_investment ?? 0;
        $project->maximum_investment = $request->maximum_investment ?? 0;
        $project->days_to_init = $request->days_to_init ?? 1;
        $project->featured = $request->featured ? Status::YES : Status::NO;
        $project->testing = $request->testing ? Status::YES : Status::NO;
        $project->save();
    }

    protected function validation($request, $id = null)
    {
        $imageRule = $id ? 'nullable' : 'required';

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => $imageRule . '|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pdf' => 'nullable|mimes:pdf|max:10240',
            'minimum_investment' => 'required|numeric|gte:0',
            'maximum_investment' => 'required|numeric|gt:minimum_investment',
            'days_to_init' => 'required|integer|min:1',
        ]);
    }

    public function status($id)
    {
        return Project::changeStatus($id);
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);

        // Verificar si tiene planes asociados
        if ($project->plans()->count() > 0) {
            $notify[] = ['error', 'Cannot delete project with existing plans. Delete plans first.'];
            return back()->withNotify($notify);
        }

        // Eliminar imagen y PDF
        if ($project->image) {
            fileManager()->removeFile(getFilePath('projectImage') . '/' . $project->image);
        }
        if ($project->pdf) {
            fileManager()->removeFile(getFilePath('projectFile') . '/' . $project->pdf);
        }

        $project->delete();

        $notify[] = ['success', 'Project deleted successfully'];
        return back()->withNotify($notify);
    }
}
