<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('customer_login.index', compact('customers'));
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->status = ($customer->status === 'active') ? 'inactive' : 'active';
        $customer->save();

        return back()->with('success', 'Status updated for ' . $customer->name);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer deleted successfully!');
    }
}