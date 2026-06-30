<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CashRegisterClosure;

class CheckOpenCashRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $openSession = CashRegisterClosure::where('status', 'open')->first();
        if (!$openSession) {
            return redirect()->route('cash_register.index')
                ->with('error', 'Debes abrir una sesión de caja antes de realizar operaciones financieras.');
        }
        return $next($request);
    }
}
