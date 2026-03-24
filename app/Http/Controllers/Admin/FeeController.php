<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Fee;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index()
    {
        $this->authorize('view_fees');
        $fees = Fee::paginate(10);
        return view('admin.fees.index', compact('fees'));
    }

    public function create()
    {
        $this->authorize('create_fees');
        return view('admin.fees.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_fees');
        $request->validate([
            'name' => 'required|string|max:255',
            'customer_fee_fixed' => 'required|numeric|min:0',
            'customer_fee_percent' => 'required|numeric|min:0|max:100',
            'site_fee_fixed' => 'required|numeric|min:0',
            'site_fee_percent' => 'required|numeric|min:0|max:100',
        ]);

        Fee::create($request->all());

        return redirect()->route('admin.fees.index')->with('success', 'Fee structure created successfully.');
    }

    public function edit(Fee $fee)
    {
        $this->authorize('edit_fees');
        return view('admin.fees.edit', compact('fee'));
    }

    public function update(Request $request, Fee $fee)
    {
        $this->authorize('edit_fees');
        $request->validate([
            'name' => 'required|string|max:255',
            'customer_fee_fixed' => 'required|numeric|min:0',
            'customer_fee_percent' => 'required|numeric|min:0|max:100',
            'site_fee_fixed' => 'required|numeric|min:0',
            'site_fee_percent' => 'required|numeric|min:0|max:100',
        ]);

        $fee->update($request->all());

        return redirect()->route('admin.fees.index')->with('success', 'Fee structure updated successfully.');
    }

    public function destroy(Fee $fee)
    {
        $this->authorize('delete_fees');
        $fee->delete();
        return redirect()->route('admin.fees.index')->with('success', 'Fee structure deleted successfully.');
    }
}
