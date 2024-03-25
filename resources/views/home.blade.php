@extends('layouts.app')

@section('content')
    <main>

        <section class="py-5 text-center container">
            <div class="row py-lg-5">
                <div class="col-lg-6 col-md-8 mx-auto">
                    <h1 class="fw-light">Top Categories</h1>
                    <div>
                        @each('categories.parts.label', $categories, 'category')
                    </div>
                </div>
            </div>
        </section>

        <div class="album py-5 bg-body-tertiary">
            <div class="container">

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                    @each('products.parts.grid', $products, 'product')
                </div>
            </div>
        </div>

    </main>
@endsection
