{{-- ================================================================
     FILE: resources/views/home.blade.php
     ================================================================

     INI ADALAH CHILD TEMPLATE
     Akan mengisi placeholder di parent template

     ================================================================ --}}

@extends('layouts.app')
{{-- ↑ DIRECTIVE @extends() - Mewarisi Layout Parent

     HARUS SELALU DI BARIS PERTAMA!

     Ini memberitahu Blade:
     "Gunakan layouts/app.blade.php sebagai kerangka,
      lalu saya akan isi bagian @yield dengan @section"

     ALUR RENDERING:
     1. Blade baca home.blade.php
     2. Lihat @extends('layouts.app')
     3. Baca layouts/app.blade.php
     4. Ganti setiap @yield dengan @section yang sesuai
     5. Return HTML lengkap --}}

@section('title', 'Beranda')
{{-- ↑ DIRECTIVE @section() - Mengisi @yield di Parent

     SYNTAX PENDEK (1 baris, tanpa HTML kompleks):
     @section('nama', 'nilai')

     Ini akan mengganti:
     Parent: @yield('title', 'Default')
     Menjadi: 'Beranda'

     SYNTAX PANJANG (untuk konten kompleks):
     @section('nama')
         <h1>Konten HTML yang panjang</h1>
     @endsection --}}

@section('content')
{{-- ↑ Mulai mengisi @yield('content') di parent --}}

    <section class="bg-primary text-white py-5">
    {{-- ↑ HERO SECTION - Banner utama homepage
         bg-primary: Background warna primary (biru/indigo)
         text-white: Semua text putih
         py-5: Padding atas-bawah 3rem (Bootstrap spacing) --}}

        <div class="container">
        {{-- ↑ Container Bootstrap: lebar max 1140px, centered --}}

            <div class="row align-items-center">
            {{-- ↑ Row untuk grid system Bootstrap
                 align-items-center: vertical center --}}

                <div class="col-lg-6">
                {{-- ↑ 6 kolom di desktop (50% lebar)
                     col-lg-6: Hanya berlaku di layar large (≥992px)
                     Di layar kecil, otomatis jadi full width --}}

                    <h1 class="display-4 fw-bold mb-3">
                        Belanja Online Mudah & Terpercaya
                    </h1>
                    {{-- display-4: Ukuran heading besar (Bootstrap typography)
                         fw-bold: font-weight bold
                         mb-3: margin-bottom 1rem --}}

                    <a href="{{ route('catalog.index') }}" class="btn btn-light btn-lg">
                    {{-- ↑ HELPER route() - Menghasilkan URL dari nama route

                         route('catalog.index')
                         Mencari di routes/web.php:
                         Route::get('/products', ...)->name('catalog.index')
                         Return: "http://domain.com/products"

                         KEUNTUNGAN PAKAI ROUTE NAME:
                         Jika suatu hari URL berubah dari /products ke /shop
                         Cukup ubah di web.php, semua link otomatis updated

                         route() DENGAN PARAMETER:
                         route('catalog.show', ['slug' => 'laptop'])
                         atau shorthand:
                         route('catalog.show', $product->slug)
                         Return: "http://domain.com/products/laptop" --}}
                        <i class="bi bi-bag me-2"></i>Mulai Belanja
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Kategori Populer</h2>

            <div class="row g-4">
            {{-- ↑ g-4: Gap/gutter antar kolom 1.5rem --}}

                @foreach($categories as $category)
                {{-- ↑ DIRECTIVE @foreach - Looping Data

                     SAMA DENGAN PHP:
                     <?php foreach($categories as $category): ?>

                     $categories adalah Collection yang dikirim dari Controller:
                     return view('home', compact('categories'));

                     Setiap iterasi, $category berisi 1 object Category
                     Kita bisa akses property: $category->name, $category->slug

                     VARIABEL LOOP TERSEDIA:
                     $loop->index     - Index saat ini (0-based)
                     $loop->iteration - Iterasi ke-berapa (1-based)
                     $loop->first     - Apakah iterasi pertama
                     $loop->last      - Apakah iterasi terakhir
                     $loop->count     - Total item --}}

                    <div class="col-6 col-md-4 col-lg-2">
                    {{-- ↑ RESPONSIVE COLUMNS:
                         col-6: Default, 2 kolom (50% per item)
                         col-md-4: Di medium screen, 3 kolom (33%)
                         col-lg-2: Di large screen, 6 kolom (16.67%)

                         Ini membuat grid yang responsive:
                         HP: 2 kategori per row
                         Tablet: 3 kategori per row
                         Desktop: 6 kategori per row --}}

                        <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                           class="text-decoration-none">
                        {{-- ↑ route() DENGAN QUERY PARAMETER:
                             route('catalog.index', ['category' => 'elektronik'])
                             Return: "http://domain.com/products?category=elektronik"

                             Catatan: ini QUERY STRING (?category=)
                             Bukan route parameter (/products/{category}) --}}

                            <div class="card border-0 shadow-sm text-center h-100">
                                <div class="card-body">
                                    <img src="{{ $category->image_url }}"
                                    {{-- ↑ MENGAKSES ACCESSOR dari Model

                                         $category->image_url tidak ada di database!
                                         Ini adalah Accessor (computed property):

                                         // Di Model Category:
                                         public function getImageUrlAttribute() {
                                             return $this->image
                                                 ? asset('storage/'.$this->image)
                                                 : asset('images/default-category.png');
                                         }

                                         Blade otomatis memanggil method ini
                                         saat kita akses $category->image_url --}}
                                         alt="{{ $category->name }}"
                                         class="rounded-circle mb-3"
                                         width="80" height="80"
                                         style="object-fit: cover;">

                                    <h6 class="card-title mb-0">{{ $category->name }}</h6>
                                    {{-- ↑ {{ $var }} - OUTPUT DENGAN ESCAPE

                                         SANGAT PENTING untuk keamanan!

                                         Jika di database: name = "<script>alert('XSS')</script>"
                                         {{ }} akan mengubahnya jadi:
                                         "&lt;script&gt;alert('XSS')&lt;/script&gt;"
                                         Browser menampilkan sebagai text, bukan menjalankan script

                                         INI MENCEGAH XSS ATTACK!

                                         {!! $var !!} - Output TANPA escape
                                         HATI-HATI! Hanya untuk HTML yang memang sengaja
                                         seperti rich text editor yang sudah disanitize --}}

                                    <small class="text-muted">{{ $category->products_count }} produk</small>
                                    {{-- ↑ products_count dari withCount() di Controller
                                         Ini bukan kolom database, tapi hasil aggregate COUNT() --}}
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
                {{-- ↑ Akhiri loop --}}
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-0">Produk Unggulan</h2>

            <div class="row g-4">
                @foreach($featuredProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">

                        @include('partials.product-card', ['product' => $product])
                        {{-- ↑ INCLUDE DENGAN PASSING VARIABLE

                             Bentuk lengkap:
                             @include('nama_file', ['key' => $value])

                             File partials/product-card.blade.php akan
                             menerima variabel $product

                             KENAPA PAKAI PARTIAL?
                             Product card dipakai di banyak tempat:
                             - Homepage (featured, latest)
                             - Katalog
                             - Wishlist
                             - Related products

                             Dengan partial, kita tulis sekali, pakai di mana saja
                             Update sekali, semua terUpdate --}}

                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
{{-- ↑ AKHIRI @section('content')
     Semua konten di atas akan menggantikan @yield('content') di parent --}}