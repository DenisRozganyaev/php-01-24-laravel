@extends('layouts.app')

@section('content')
    <main>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div id="product-gallery" class="carousel slide">
                        <div class="carousel-indicators">
                            @foreach($gallery as $key => $data)
                            <button type="button" data-bs-target="#product-gallery" data-bs-slide-to="{{$key}}"
                                    class="{{$key === 0 ? 'active' : ''}}"
                                    aria-current="true" aria-label="Slide {{$key + 1}}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach($gallery as $key => $url)
                                <div class="carousel-item {{$key === 0 ? 'active' : ''}}">
                                    <img src="{{$url}}" class="d-block w-100" alt="{{$product->title}}">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#product-gallery" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#product-gallery" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <h3>{{$product->title}}</h3>
                    <small>{{$product->SKU}}</small>
                    <hr>
                    <p>Quantity: {{$product->quantity}}</p>
                    <p>Categories: @each('categories.parts.label', $product->categories, 'category')</p>
                    <hr>
                    @auth()
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">WishList</h5>
                                @include(
                                    'products.parts.wishlist.price',
                                    [
                                        'product' => $product,
                                        'isFollowed' => $wishes['price'],
                                        'mini' => false
                                    ]
                                )
                                @unless($product->isExists)
                                    <br>
                                    @include(
                                        'products.parts.wishlist.exist',
                                        [
                                            'product' => $product,
                                            'isFollowed' => $wishes['exist'],
                                            'mini' => false
                                        ]
                                    )
                                @endif
                            </div>
                        </div>
                    @endauth()
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Price: {{$product->finalPrice}}$</h5>
                        </div>
                        <div class="card-footer">
                            @if ($product->isExists)
                                @if($product->isInCart)
                                    <form action="{{route('cart.remove')}}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="rowId" value="{{$product->rowId}}" />
                                        <button type="submit" class="btn btn-warning">Remove from cart</button>
                                    </form>
                                @else
                                    <form action="{{route('cart.add', $product)}}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Add to cart</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p>
                        {{ $product->description }}
                    </p>
                </div>
            </div>
        </div>
    </main>
@endsection
