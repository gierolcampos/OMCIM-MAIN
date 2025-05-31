<?php

namespace App\Http\Controllers;

use App\Models\PaymentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentFees = PaymentFee::orderBy('purpose')->get();
        return view('payment_fees.index', compact('paymentFees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('payment_fees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|max:100|unique:payment_fees',
            'description' => 'nullable|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $paymentFee = new PaymentFee();
        $paymentFee->purpose = $request->purpose;
        $paymentFee->description = $request->description;
        $paymentFee->total_price = $request->total_price;
        $paymentFee->is_active = $request->has('is_active') ? 1 : 0;
        $paymentFee->save();

        return redirect()->route('admin.payment-fees.index')
            ->with('success', 'Payment fee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paymentFee = PaymentFee::findOrFail($id);
        return view('payment_fees.show', compact('paymentFee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $paymentFee = PaymentFee::findOrFail($id);
        return view('payment_fees.edit', compact('paymentFee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentFee = PaymentFee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|max:100|unique:payment_fees,purpose,' . $id . ',fee_id',
            'description' => 'nullable|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $paymentFee->purpose = $request->purpose;
        $paymentFee->description = $request->description;
        $paymentFee->total_price = $request->total_price;
        $paymentFee->is_active = $request->has('is_active') ? 1 : 0;
        $paymentFee->save();

        return redirect()->route('admin.payment-fees.index')
            ->with('success', 'Payment fee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentFee = PaymentFee::findOrFail($id);
        $paymentFee->delete();

        return redirect()->route('admin.payment-fees.index')
            ->with('success', 'Payment fee deleted successfully.');
    }

    /**
     * Toggle the active status of a payment fee.
     */
    public function toggleActive(string $id)
    {
        $paymentFee = PaymentFee::findOrFail($id);
        $paymentFee->is_active = !$paymentFee->is_active;
        $paymentFee->save();

        return redirect()->route('admin.payment-fees.index')
            ->with('success', 'Payment fee status updated successfully.');
    }
}
