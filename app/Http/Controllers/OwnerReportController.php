<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Settlement;
use App\Models\Property;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OwnerReportController extends Controller
{
    public function index()
    {
        $reports = Report::latest()->paginate(15);
        return view('owners.reports.index', compact('reports'));
    }

    public function create(Request $request)
    {
        $owners = Owner::orderBy('name')->get();
        $selectedOwnerId = $request->query('owner_id');

        return view('owners.reports.setup', compact('owners', 'selectedOwnerId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_ids' => 'required|array',
            'owner_ids.*' => 'exists:owners,id',
            'start_month' => 'required|integer|min:1|max:12',
            'start_year' => 'required|integer|min:2000',
            'end_month' => 'required|integer|min:1|max:12',
            'end_year' => 'required|integer|min:2000',
        ]);

        $startStr = sprintf('%04d-%02d', $request->start_year, $request->start_month);
        $endStr = sprintf('%04d-%02d', $request->end_year, $request->end_month);

        if ($startStr > $endStr) {
            return back()->with('error', 'El período de inicio no puede ser posterior al período de fin.');
        }

        $startLabel = Carbon::create($request->start_year, $request->start_month, 1)->translatedFormat('F Y');
        $endLabel = Carbon::create($request->end_year, $request->end_month, 1)->translatedFormat('F Y');
        $title = "Reporte Patrimonial (" . ucwords($startLabel) . ' a ' . ucwords($endLabel) . ")";

        $report = Report::create([
            'title' => $title,
            'start_month' => $request->start_month,
            'start_year' => $request->start_year,
            'end_month' => $request->end_month,
            'end_year' => $request->end_year,
            'owner_ids' => $request->owner_ids,
        ]);

        return redirect()->route('reports.show', $report)->with('success', 'Reporte generado y guardado correctamente.');
    }

    public function show(Report $report)
    {
        $startStr = sprintf('%04d-%02d', $report->start_year, $report->start_month);
        $endStr = sprintf('%04d-%02d', $report->end_year, $report->end_month);

        $ownersData = [];
        $owners = Owner::whereIn('id', $report->owner_ids)->orderBy('name')->get();

        foreach ($owners as $owner) {
            $settlements = Settlement::where('owner_id', $owner->id)
                ->where('status', 'paid')
                ->whereRaw("CONCAT(year, '-', LPAD(month, 2, '0')) >= ?", [$startStr])
                ->whereRaw("CONCAT(year, '-', LPAD(month, 2, '0')) <= ?", [$endStr])
                ->get();

            $ownersData[] = [
                'owner' => $owner,
                'totalNetIncome' => $settlements->sum('net_amount'),
                'totalExpensesManaged' => $settlements->sum('total_expense'),
                'settlementsCount' => $settlements->count(),
            ];
        }

        $periodLabel = Carbon::create($report->start_year, $report->start_month, 1)->translatedFormat('M Y') . ' a ' . Carbon::create($report->end_year, $report->end_month, 1)->translatedFormat('M Y');

        return view('owners.reports.show', compact('report', 'ownersData', 'periodLabel'));
    }

    public function showIndividual(Report $report, $ownerId)
    {
        return $this->getDossierData($report, [$ownerId]);
    }

    public function showPublic(Report $report, $ownerId)
    {
        return $this->getDossierData($report, [$ownerId], true);
    }

    public function showBatch(Report $report)
    {
        return $this->getDossierData($report, $report->owner_ids);
    }

    public function sendEmail(Request $request, Report $report, $ownerId)
    {
        $owner = Owner::findOrFail($ownerId);
        
        $startStr = sprintf('%04d-%02d', $report->start_year, $report->start_month);
        $endStr = sprintf('%04d-%02d', $report->end_year, $report->end_month);

        $settlements = Settlement::where('owner_id', $owner->id)
            ->where('status', 'paid')
            ->whereRaw("CONCAT(year, '-', LPAD(month, 2, '0')) >= ?", [$startStr])
            ->whereRaw("CONCAT(year, '-', LPAD(month, 2, '0')) <= ?", [$endStr])
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $totalNetIncome = $settlements->sum('net_amount');
        $totalExpensesManaged = $settlements->sum('total_expense');

        $monthlyIncome = [];
        foreach ($settlements as $s) {
            $monthLabel = Carbon::create($s->year, $s->month, 1)->translatedFormat('M Y');
            $monthlyIncome[] = [
                'period' => $monthLabel,
                'amount' => $s->net_amount
            ];
        }

        $properties = Property::with(['activeLease.tenant', 'city', 'province'])->where('owner_id', $owner->id)->get();

        $periodLabel = Carbon::create($report->start_year, $report->start_month, 1)->translatedFormat('M Y') . ' a ' . Carbon::create($report->end_year, $report->end_month, 1)->translatedFormat('M Y');

        $publicUrl = route('reports.show_public', [$report, $owner->id]);

        $payload = [
            'type' => 'period_report',
            'report_id' => $report->id,
            'period' => $periodLabel,
            'owner' => [
                'id' => $owner->id,
                'name' => $owner->name,
                'email' => $owner->email,
                'phone' => $owner->phone,
                'dni_cuit' => $owner->dni_cuit,
            ],
            'totals' => [
                'total_net_income' => $totalNetIncome,
                'total_expenses_managed' => $totalExpensesManaged,
                'settlements_count' => $settlements->count(),
            ],
            'monthly_income' => $monthlyIncome,
            'properties' => $properties->map(function($p) {
                return [
                    'location' => $p->location,
                    'city' => $p->city->name ?? 'Ciudad',
                    'province' => $p->province->name ?? 'Provincia',
                    'rooms' => $p->rooms,
                    'status' => $p->activeLease ? 'Alquilada' : 'Vacante',
                    'tenant_name' => $p->activeLease->tenant->name ?? null,
                    'lease_end_date' => $p->activeLease ? Carbon::parse($p->activeLease->end_date)->format('d/m/Y') : null,
                ];
            }),
            'public_url' => $publicUrl,
            'n8n_code' => \App\Services\N8nCodeService::getDossierCode(),
        ];

        try {
            $webhookUrl = 'https://n8n.apxsoftware.com.ar/webhook/7d4e873d-0c30-47d2-a21e-be8fd3758b58';

            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->timeout(20)
                ->post($webhookUrl, $payload);
            
            if (!$response->successful()) {
                throw new \Exception("El servidor respondió con código " . $response->status() . ": " . $response->body());
            }
            
            return back()->with('success', 'Reporte enviado a n8n correctamente para envío de email.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error Webhook Report Email: " . $e->getMessage());
            return back()->with('error', 'Error al enviar al webhook de email: ' . $e->getMessage());
        }
    }

    private function getDossierData(Report $report, array $ownerIds, bool $isPublic = false)
    {
        $startStr = sprintf('%04d-%02d', $report->start_year, $report->start_month);
        $endStr = sprintf('%04d-%02d', $report->end_year, $report->end_month);

        $ownersData = [];
        $owners = Owner::whereIn('id', $ownerIds)->orderBy('name')->get();

        foreach ($owners as $owner) {
            $settlements = Settlement::where('owner_id', $owner->id)
                ->where('status', 'paid')
                ->whereRaw("CONCAT(year, '-', LPAD(month, 2, '0')) >= ?", [$startStr])
                ->whereRaw("CONCAT(year, '-', LPAD(month, 2, '0')) <= ?", [$endStr])
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            $totalNetIncome = $settlements->sum('net_amount');
            $totalExpensesManaged = $settlements->sum('total_expense');

            $monthlyIncome = [];
            foreach ($settlements as $s) {
                $monthLabel = Carbon::create($s->year, $s->month, 1)->translatedFormat('M Y');
                $monthlyIncome[$monthLabel] = $s->net_amount;
            }

            // Propiedades del propietario
            $properties = Property::with(['activeLease.tenant'])->where('owner_id', $owner->id)->get();

            $ownersData[] = [
                'owner' => $owner,
                'totalNetIncome' => $totalNetIncome,
                'totalExpensesManaged' => $totalExpensesManaged,
                'monthlyIncome' => $monthlyIncome,
                'properties' => $properties,
                'settlementsCount' => $settlements->count(),
            ];
        }

        $periodLabel = Carbon::create($report->start_year, $report->start_month, 1)->translatedFormat('M Y') . ' a ' . Carbon::create($report->end_year, $report->end_month, 1)->translatedFormat('M Y');

        return view('owners.reports.dossier', compact('ownersData', 'periodLabel', 'report', 'isPublic'));
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Reporte eliminado correctamente.');
    }
}
