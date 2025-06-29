<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\Societe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentCategoryController extends Controller
{
    /**
     * Affiche la liste des catégories de documents.
     */
    public function index()
    {
        $categories = DocumentCategory::where(function($query) {
            $query->where('is_default', true)
                  ->orWhere('societe_id', Auth::user()->societe_id)
                  ->orWhereNull('societe_id');
        })->orderBy('name')->get();
        
        return view('admin.document-categories.index', compact('categories'));
    }

    /**
     * Affiche le formulaire de création d'une catégorie.
     */
    public function create()
    {
        return view('admin.document-categories.create');
    }

    /**
     * Enregistre une nouvelle catégorie de document.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $category = new DocumentCategory();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->is_default = false;
        $category->societe_id = Auth::user()->societe_id;
        $category->created_by = Auth::id();
        $category->save();
        
        return redirect()->route('admin.document-categories.index')
            ->with('success', 'La catégorie a été créée avec succès.');
    }

    /**
     * Affiche les détails d'une catégorie spécifique.
     */
    public function show(string $id)
    {
        $category = DocumentCategory::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette catégorie
        if (!$category->is_default && $category->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à cette catégorie.');
        }
        
        return view('admin.document-categories.show', compact('category'));
    }

    /**
     * Affiche le formulaire d'édition d'une catégorie.
     */
    public function edit(string $id)
    {
        $category = DocumentCategory::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette catégorie
        if (!$category->is_default && $category->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à cette catégorie.');
        }
        
        return view('admin.document-categories.edit', compact('category'));
    }

    /**
     * Met à jour une catégorie spécifique.
     */
    public function update(Request $request, string $id)
    {
        $category = DocumentCategory::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette catégorie
        if (!$category->is_default && $category->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à cette catégorie.');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        
        return redirect()->route('admin.document-categories.index')
            ->with('success', 'La catégorie a été mise à jour avec succès.');
    }

    /**
     * Supprime une catégorie spécifique.
     */
    public function destroy(string $id)
    {
        $category = DocumentCategory::findOrFail($id);
        
        // Vérifier que l'utilisateur a accès à cette catégorie
        if (!$category->is_default && $category->societe_id !== Auth::user()->societe_id) {
            abort(403, 'Vous n\'avez pas accès à cette catégorie.');
        }
        
        // Vérifier si des documents utilisent cette catégorie
        if ($category->documents()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cette catégorie ne peut pas être supprimée car elle est utilisée par des documents.');
        }
        
        $category->delete();
        
        return redirect()->route('admin.document-categories.index')
            ->with('success', 'La catégorie a été supprimée avec succès.');
    }
    
    /**
     * Récupère les catégories en AJAX.
     */
    public function getCategories(Request $request)
    {
        $categories = DocumentCategory::where(function($query) {
            $query->where('is_default', true)
                  ->orWhere('societe_id', Auth::user()->societe_id)
                  ->orWhereNull('societe_id');
        })->orderBy('name')->get();
        
        return response()->json($categories);
    }
}
