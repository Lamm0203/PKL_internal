@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="container">
    <h1 class="mb-4">Laporan Penjualan</h1>

    {{-- Filter tanggal --}}
    <form action="{{ route('admin.reports.sales') }}" method="GET" class="mb-4 row g-2">
        <div class="col-auto">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-auto">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    {{-- Tabel laporan --}}
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                <td>{{ $item->produk->nama }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data penjualan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Total --}}
    @if($laporan->count())
    <div class="mt-3">
        <h5>Total Penjualan: Rp {{ number_format($laporan->sum('total_harga'), 0, ',', '.') }}</h5>
    </div>
    @endif
</div>
@endsection
