@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Laporan Penjualan</h1>

    @if($laporan->isEmpty())
        <div class="alert alert-info">
            Belum ada data laporan.
        </div>
    @else
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Total Orders</th>
                    <th>Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporan as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                        <td>{{ $data->total_orders }}</td>
                        <td>Rp {{ number_format($data->total_sales, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th>{{ $laporan->sum('total_orders') }}</th>
                    <th>Rp {{ number_format($laporan->sum('total_sales'), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    @endif
</div>
@endsection
