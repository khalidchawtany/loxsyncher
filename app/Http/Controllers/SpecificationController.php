<?php

namespace App\Http\Controllers;

use App\Http\Requests\Specifications\StoreSpecification;
use App\Http\Requests\Specifications\UpdateSpecification;
use App\Models\Specification;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;

class SpecificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_specification', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_specification', ['only' => ['create']]);
        $this->middleware('permission:update_specification', ['only' => ['update']]);
        $this->middleware('permission:destroy_specification', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('specifications.index');
    }

    public function list(Request $request)
    {
        $user_can_view_all_specs = auth()->user()->can('view_all_specification');

        return JQueryBuilder::for(Specification::class)
            ->allowedFilters(
                'category',
                'title',
                'title_eng',
                'number',
                'standard',
                'status',
                'specifications.status',
                'note',
                'user_id'
            )
            ->when(!$user_can_view_all_specs, function ($query) {
                return $query->where('specifications.status', 'Active');
            })
            ->jsonJPaginate();
    }

    public function listCategories()
    {
        return Specification::selectRaw('distinct(category) as category')
            ->get();
    }

    protected function create(StoreSpecification $request)
    {
        $specification = Specification::create($request->input());

        return ezReturnSuccessMessage('Standard created successfully!', $specification);
    }

    public function update(UpdateSpecification $request)
    {
        $specification = Specification::findOrFail($request->id);

        $specification->update($request->input());

        return ezReturnSuccessMessage('Standard updated successfully!', $specification);
    }

    public function destroy(Request $request)
    {
        $specification = Specification::findOrFail($request->id);

        $specification->delete();

        return ezReturnSuccessMessage('Standard removed successfully!');
    }

    public function showAttachDocumentDialog($specification_id)
    {
        $specification = Specification::findOrFail($specification_id);

        return view('specifications.attach_document_dialog')->with('specification', $specification);
    }

    public function attachDocument(Request $request)
    {
        $specification = Specification::findOrFail($request->specification_id);

        $specification->update([
            'document_url' => $request->specificationDocument,
        ]);

        return ezReturnSuccessMessage('Document attached successfully.');
    }

    public function showDocumentDialog($specification_id)
    {
        $specification = Specification::findOrFail($specification_id);

        return view('specifications.document_dialog')
            ->with('specification', $specification);
    }
}

