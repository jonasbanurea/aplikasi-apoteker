<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner|admin_gudang|kasir']);
    }

    public function index(Request $request)
    {
        $date = $request->input('date');
        $status = $request->input('status');

        $query = Shift::with('user')
            ->withSum('sales as total_sales', 'total')
            ->withSum(['sales as total_cash' => fn($q) => $q->where('payment_method', Sale::METHOD_CASH)], 'total')
            ->withSum(['sales as total_non_cash' => fn($q) => $q->where('payment_method', Sale::METHOD_NON_CASH)], 'total')
            ->when($date, fn($q) => $q->whereDate('opened_at', $date))
            ->when($status === 'open', fn($q) => $q->whereNull('closed_at'))
            ->when($status === 'closed', fn($q) => $q->whereNotNull('closed_at'))
            ->latest('opened_at');

        if ($request->get('export') === 'csv') {
            return $this->exportCsv($query->get());
        }

        $shifts = $query->paginate(15)->withQueryString();
        $openShift = $this->getOpenShift($request->user()->id);

        return view('shifts.index', compact('shifts', 'date', 'status', 'openShift'));
    }

    public function create(Request $request)
    {
        $openShift = $this->getOpenShift($request->user()->id);
        if ($openShift) {
            return redirect()->route('shifts.index')->with('error', 'Shift masih terbuka. Tutup shift sebelum membuka yang baru.');
        }

        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0'],
        ]);

        $userId = $request->user()->id;
        $existing = $this->getOpenShift($userId);
        if ($existing) {
            return redirect()->route('shifts.index')->with('error', 'Shift masih terbuka. Tutup shift sebelum membuka yang baru.');
        }

        Shift::create([
            'user_id' => $userId,
            'opened_at' => Carbon::now(),
            'opening_cash' => (float) $data['opening_cash'],
            'cash_expected' => (float) $data['opening_cash'],
        ]);

        return redirect()->route('shifts.index')->with('success', 'Shift dibuka.');
    }

    public function show(Shift $shift)
    {
        $shift->load('user');
        $totals = $this->computeTotals($shift);

        $sales = $shift->sales()
            ->with('user')
            ->latest('sale_date')
            ->paginate(10);

        return view('shifts.show', compact('shift', 'totals', 'sales'));
    }

    public function closeForm(Request $request, Shift $shift)
    {
        $this->authorizeShiftAccess($request->user()->id, $shift);
        if ($shift->closed_at) {
            return redirect()->route('shifts.show', $shift)->with('error', 'Shift sudah ditutup.');
        }

        $totals = $this->computeTotals($shift);

        return view('shifts.close', compact('shift', 'totals'));
    }

    public function close(Request $request, Shift $shift)
    {
        $this->authorizeShiftAccess($request->user()->id, $shift);
        if ($shift->closed_at) {
            return redirect()->route('shifts.show', $shift)->with('error', 'Shift sudah ditutup.');
        }

        $data = $request->validate([
            'closing_cash_actual' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($shift, $data) {
            $totals = $this->computeTotals($shift, true);

            $shift->update([
                'closed_at' => Carbon::now(),
                'closing_cash_actual' => (float) $data['closing_cash_actual'],
                'cash_expected' => $totals['expected_cash'],
                'discrepancy' => (float) $data['closing_cash_actual'] - $totals['expected_cash'],
            ]);
        });

        return redirect()->route('shifts.show', $shift)->with('success', 'Shift ditutup.');
    }

    protected function computeTotals(Shift $shift, bool $lock = false): array
    {
        $salesQuery = $shift->sales();
        if ($lock) {
            $salesQuery->lockForUpdate();
        }

        $totalCash = (float) $salesQuery->where('payment_method', Sale::METHOD_CASH)->sum('total');
        $totalNonCash = (float) $shift->sales()->where('payment_method', Sale::METHOD_NON_CASH)->sum('total');
        $totalSales = $totalCash + $totalNonCash;
        $expectedCash = (float) $shift->opening_cash + $totalCash;

        return [
            'total_cash' => $totalCash,
            'total_non_cash' => $totalNonCash,
            'total_sales' => $totalSales,
            'expected_cash' => $expectedCash,
        ];
    }

    protected function getOpenShift(int $userId): ?Shift
    {
        return Shift::where('user_id', $userId)
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();
    }

    protected function authorizeShiftAccess(int $userId, Shift $shift): void
    {
        if ($shift->user_id !== $userId && !auth()->user()->hasRole('owner')) {
            abort(403);
        }
    }

    protected function exportCsv($shifts): StreamedResponse
    {
        $filename = 'z-report-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($shifts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Shift ID', 'Kasir', 'Opened At', 'Closed At', 'Opening Cash', 'Cash Expected', 'Closing Cash Actual', 'Discrepancy', 'Total Sales', 'Total Cash', 'Total Non Cash']);
            foreach ($shifts as $shift) {
                $totals = [
                    'total_sales' => $shift->total_sales ?? 0,
                    'total_cash' => $shift->total_cash ?? 0,
                    'total_non_cash' => $shift->total_non_cash ?? 0,
                ];
                fputcsv($handle, [
                    $shift->id,
                    $shift->user?->name,
                    optional($shift->opened_at)->format('Y-m-d H:i:s'),
                    optional($shift->closed_at)->format('Y-m-d H:i:s'),
                    $shift->opening_cash,
                    $shift->cash_expected,
                    $shift->closing_cash_actual,
                    $shift->discrepancy,
                    $totals['total_sales'],
                    $totals['total_cash'],
                    $totals['total_non_cash'],
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
