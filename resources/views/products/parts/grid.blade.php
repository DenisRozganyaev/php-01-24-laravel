<div class="col">
    <div class="card shadow-sm" style="height: 100%; width: 100%; @unless($product->isExists) filter: grayscale(1); color: lightgray; @endunless">
        <img src="{{$product->thumbnailUrl}}" class="card-img-top" alt="{{$product->title}}">
        <div class="card-body" style="display: flex; align-items: stretch; flex-direction: column; justify-content: flex-end; height: 50%;">
            <h3 class="card-text">{{$product->title}}</h3>
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center justify-content-start gap-1">
                    <a href="{{route('products.show', $product)}}" class="btn btn-sm btn-outline-primary">Show</a>
                    @if ($product->isExists)
                        @if($product->isInCart)
                            <form action="{{route('cart.remove')}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="rowId" value="{{$product->rowId}}" />
                                <button type="submit" class="btn btn-sm btn-outline-warning">Remove</button>
                            </form>
                        @else
                            <form action="{{route('cart.add', $product)}}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">Buy</button>
                            </form>
                        @endif
                    @endif
                </div>
                <small class="text-body-secondary">${{$product->finalPrice}}</small>
            </div>
        </div>
    </div>
</div>
