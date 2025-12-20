@extends('layouts.admin')

@section('page-title', 'Daftar Produk')

@section('content')
    <h1>Daftar Produk</h1>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->formatted_price }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
