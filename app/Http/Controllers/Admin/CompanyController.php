<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Fee;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $this->authorize('view_companies');
        $user = auth()->user();
        $fees = Fee::where('status', true)->get();
        
        $query = Company::with('fee');
        if ($user->hasRole('Company Admin')) {
            $query->where('id', $user->company_id);
        }
        
        $companies = $query->paginate(10);
        return view('admin.companies.index', compact('companies', 'fees'));
    }

    public function create()
    {
        $this->authorize('create_companies');
        $fees = Fee::where('status', true)->get();
        return view('admin.companies.create', compact('fees'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_companies');
        $request->validate([
            'name' => 'required|string|max:255',
            'fee_id' => 'required|exists:fees,id',
            'status' => 'boolean'
        ]);

        Company::create($request->all());

        return redirect()->route('admin.companies.index')->with('success', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        $this->authorize('edit_companies');
        $fees = Fee::where('status', true)->get();
        return view('admin.companies.edit', compact('company', 'fees'));
    }

    public function update(Request $request, Company $company)
    {
        $this->authorize('edit_companies');
        $request->validate([
            'name' => 'required|string|max:255',
            'fee_id' => 'required|exists:fees,id',
            'status' => 'boolean'
        ]);

        $company->update($request->all());

        return redirect()->route('admin.companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $this->authorize('delete_companies');
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', 'Company deleted successfully.');
    }
}
