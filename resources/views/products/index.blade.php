@extends('layouts.app')

@section('content')
    <main>
        <div class="album py-5 bg-body-tertiary">
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                    @each('products.parts.grid', $products, 'product')
                </div>
                <div class="row mt-5">
                    <div class="col">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection
