<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = InventoryTransaction::with([
            'product',
            'field',
            'enteredBy',
            'usedBy'
        ])
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            })
            ->when($request->type, function($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->product_id, function($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            })
            ->when($request->field_id, function($query) use ($request) {
                return $query->where('field_id', $request->field_id);
            })
            ->when($request->used_by_user_id, function($query) use ($request) {
                return $query->where('used_by_user_id', $request->used_by_user_id);
            })
            ->orderBy('date', 'desc')
            ->paginate(25);

        $summary = InventoryTransaction::select([
            DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in'),
            DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_out')
        ])
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            })
            ->first();

        return view('transactions.index', [
            'transactions' => $transactions,
            'products' => Product::orderBy('name')->get(),
            'fields' => Field::orderBy('bloc_number')->get(),
            'users' => User::all(),
            'summary' => $summary,
            'filters' => $request->all()
        ]);
    }

    public function export(Request $request)
    {
        $transactions = InventoryTransaction::with([
            'product',
            'field',
            'enteredBy',
            'usedBy'
        ])
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            })
            ->when($request->type, function($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->product_id, function($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            })
            ->when($request->field_id, function($query) use ($request) {
                return $query->where('field_id', $request->field_id);
            })
            ->orderBy('date', 'desc')
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=transactions_export.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Date', 'Product', 'Type', 'Quantity',
                'Field/Block', 'Crop Type', 'Notes',
                'Recorded By', 'Used By'
            ]);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->date->format('Y-m-d'),
                    $t->product->name,
                    strtoupper($t->type),
                    $t->quantity,
                    $t->field ? $t->field->bloc_number : 'N/A',
                    $t->field ? $t->field->crop_type : 'N/A',
                    $t->notes,
                    $t->enteredBy->name,
                    $t->usedBy ? $t->usedBy->name : 'N/A'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function dashboard()
    {
        $monthlySummary = InventoryTransaction::select([
            DB::raw("DATE_FORMAT(date, '%Y-%m') as month"),
            DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in'),
            DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_out')
        ])
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        $topProducts = InventoryTransaction::with('product')
            ->select([
                'product_id',
                DB::raw('SUM(quantity) as total_used')
            ])
            ->where('type', 'out')
            ->groupBy('product_id')
            ->orderBy('total_used', 'desc')
            ->limit(5)
            ->get();

        $recentIns = InventoryTransaction::with(['product', 'enteredBy'])
            ->where('type', 'in')
            ->latest()
            ->limit(5)
            ->get();

        $recentOuts = InventoryTransaction::with(['product', 'field', 'enteredBy', 'usedBy'])
            ->where('type', 'out')
            ->latest()
            ->limit(5)
            ->get();

        return view('transactions.dashboard', [
            'monthlySummary' => $monthlySummary,
            'topProducts' => $topProducts,
            'recentIns' => $recentIns,
            'recentOuts' => $recentOuts,
            'totalIn' => $monthlySummary->sum('total_in'),
            'totalOut' => $monthlySummary->sum('total_out')
        ]);
    }
}
