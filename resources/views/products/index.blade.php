@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Product Inventory</h1>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add Product
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Bar -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search by product name or category..."
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th class="text-end">Stock</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                    @if($product->description)
                                        <small class="text-muted d-block">{{ Str::limit($product->description, 40) }}</small>
                                    @endif
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td class="text-end">
                                <span class="badge bg-{{ $product->current_quantity > 20 ? 'success' : 'warning' }}">
                                    {{ $product->current_quantity }} units
                                </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- View Button -->
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Stock Management Dropdown -->
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-boxes"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('products.add-stock.form', $product) }}">
                                                        <i class="fas fa-plus text-success me-2"></i> Add Stock
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('products.use-stock.form', $product) }}">
                                                        <i class="fas fa-minus text-danger me-2"></i> Use Stock
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Delete Button -->
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Delete this product?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    @if(request('search'))
                                        No products found matching "{{ request('search') }}"
                                    @else
                                        No products found
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
                        </div>
                        <div>
                            {{ $products->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .table th {
            white-space: nowrap;
        }
        .badge {
            font-size: 0.85em;
            min-width: 70px;
        }
        .btn-group .dropdown-toggle::after {
            margin-left: 0.3em;
        }
        .input-group {
            max-width: 500px;
        }
    </style>
@endsection
