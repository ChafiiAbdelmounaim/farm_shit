@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Inventory Transactions</h1>
            <div>
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="fas fa-filter me-1"></i> Filters
                </button>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="collapse mb-4" id="filtersCollapse">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('transactions.index') }}" method="GET">
                        <div class="row g-3">
                            <!-- Date Range -->
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ request('end_date') }}">
                            </div>

                            <!-- Transaction Type -->
                            <div class="col-md-2">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">All</option>
                                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                                </select>
                            </div>

                            <!-- Product Filter -->
                            <div class="col-md-2">
                                <label for="product_id" class="form-label">Product</label>
                                <select class="form-select" id="product_id" name="product_id">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Field Filter -->
                            <div class="col-md-2">
                                <label for="field_id" class="form-label">Field</label>
                                <select class="form-select" id="field_id" name="field_id">
                                    <option value="">All Fields</option>
                                    @foreach($fields as $field)
                                        <option value="{{ $field->id }}"
                                            {{ request('field_id') == $field->id ? 'selected' : '' }}>
                                            {{ $field->bloc_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-md-12 d-flex justify-content-end align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Apply Filters
                                </button>
                                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th class="text-center">Type</th>
                            <th class="text-end">Qty</th>
                            <th>Field/Block</th>
                            <th>Notes</th>
                            <th>Recorded By</th>
                            <th>Used By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->product->name }}</td>
                                <td class="text-center">
                                <span class="badge bg-{{ $transaction->type == 'in' ? 'success' : 'danger' }}">
                                    {{ $transaction->type == 'in' ? 'IN' : 'OUT' }}
                                </span>
                                </td>
                                <td class="text-end">{{ $transaction->quantity }}</td>
                                <td>
                                    @if($transaction->field)
                                        {{ $transaction->field->bloc_number }} ({{ $transaction->field->crop_type }})
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ Str::limit($transaction->notes, 30) }}</td>
                                <td>{{ $transaction->enteredBy->name }}</td>
                                <td>{{ $transaction->usedBy->name }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                            data-bs-target="#transactionModal{{ $transaction->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Transaction Detail Modal -->
                            <div class="modal fade" id="transactionModal{{ $transaction->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Transaction Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <strong>Product:</strong>
                                                    <p>{{ $transaction->product->name }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Type:</strong>
                                                    <p>
                                                    <span class="badge bg-{{ $transaction->type == 'in' ? 'success' : 'danger' }}">
                                                        {{ $transaction->type == 'in' ? 'Stock In' : 'Stock Out' }}
                                                    </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <strong>Quantity:</strong>
                                                    <p>{{ $transaction->quantity }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Date:</strong>
                                                    <p>{{ $transaction->date->format('d/m/Y') }}</p>
                                                </div>
                                            </div>
                                            @if($transaction->field)
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <strong>Field/Block:</strong>
                                                        <p>
                                                            {{ $transaction->field->bloc_number }} -
                                                            {{ $transaction->field->crop_type }}
                                                            @if($transaction->field->location)
                                                                ({{ $transaction->field->location }})
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($transaction->notes)
                                                <div class="row">
                                                    <div class="col-12">
                                                        <strong>Notes:</strong>
                                                        <p>{{ $transaction->notes }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No transactions found matching your criteria
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($transactions->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries
                        </div>
                        <div>
                            {{ $transactions->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .badge {
            min-width: 60px;
        }
        .table th {
            white-space: nowrap;
        }
    </style>
@endsection
