<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::all();
        return view('settings.payment_methods.index', compact('methods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
        ]);

        PaymentMethod::create($request->all());

        return back()->with('success', 'Método de pago creado correctamente.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name,' . $paymentMethod->id,
        ]);

        $paymentMethod->update($request->all());

        return back()->with('success', 'Método de pago actualizado.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Verificar si se está usando en algún pago
        if ($paymentMethod->payments()->exists()) {
            return back()->with('error', 'No se puede eliminar un método que ya ha sido utilizado en pagos.');
        }

        $paymentMethod->delete();
        return back()->with('success', 'Método de pago eliminado.');
    }
}
