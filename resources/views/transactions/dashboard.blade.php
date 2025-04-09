@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="mb-0">Inventory Dashboard</h1>
                <p class="text-muted">Overview of stock movements and usage patterns</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-primary shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-primary">Stock Added</h5>
                                <h2 class="mb-0">{{ $totalIn }}</h2>
                            </div>
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                        <p class="text-muted mb-0">This month</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-danger shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-danger">Stock Used</h5>
                                <h2 class="mb-0">{{ $totalOut }}</h2>
                            </div>
                            <div class="icon-circle bg-danger text-white">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                        <p class="text-muted mb-0">This month</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-success shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-success">Net Change</h5>
                                <h2 class="mb-0">{{ $totalIn - $totalOut }}</h2>
                            </div>
                            <div class="icon-circle bg-success text-white">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                        </div>
                        <p class="text-muted mb-0">Monthly balance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Monthly Inventory Flow</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyFlowChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Top Products Used</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topProductsChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Stock Additions</h5>
                        <a href="{{ route('transactions.index', ['type' => 'in']) }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th class="text-end">Qty</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentIns as $transaction)
                                    <tr>
                                        <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                        <td>{{ $transaction->product->name }}</td>
                                        <td class="text-end">{{ $transaction->quantity }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Field Usage</h5>
                        <a href="{{ route('transactions.index', ['type' => 'out']) }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Field</th>
                                    <th>Product</th>
                                    <th class="text-end">Qty</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentOuts as $transaction)
                                    <tr>
                                        <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                        <td>{{ $transaction->field->bloc_number }}</td>
                                        <td>{{ $transaction->product->name }}</td>
                                        <td class="text-end">{{ $transaction->quantity }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Flow Chart
        const monthlyCtx = document.getElementById('monthlyFlowChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlySummary->pluck('month')),
                datasets: [
                    {
                        label: 'Stock Added',
                        data: @json($monthlySummary->pluck('total_in')),
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Stock Used',
                        data: @json($monthlySummary->pluck('total_out')),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity'
                        }
                    }
                }
            }
        });

        // Top Products Chart
        const productsCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(productsCtx, {
            type: 'doughnut',
            data: {
                labels: @json($topProducts->pluck('product.name')),
                datasets: [{
                    data: @json($topProducts->pluck('total_used')),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .card {
            border-radius: 0.5rem;
        }
        .table-responsive {
            min-height: 200px;
        }
    </style>
@endsection
