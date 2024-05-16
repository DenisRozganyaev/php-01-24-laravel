<div class="col">
    <div class="card shadow-sm" style="height: 100%; width: 100%;">
        <img src="{{$product->thumbnailUrl}}" class="card-img-top" alt="{{$product->title}}">
        <div class="card-body" style="display: flex; align-items: stretch; flex-direction: column; justify-content: flex-end; height: 50%;">
            <h3 class="card-text">{{$product->title}}</h3>
            <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                    <a href="{{route('products.show', $product)}}" class="btn btn-sm btn-outline-primary">Show</a>
                    <button type="button" class="btn btn-sm btn-outline-success">Buy</button>
                </div>
                <small class="text-body-secondary">${{$product->finalPrice}}</small>
            </div>
        </div>
    </div>
</div>
