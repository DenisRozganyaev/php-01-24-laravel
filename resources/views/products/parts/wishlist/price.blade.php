@unless ($isFollowed)
    <form action="{{route('wishlist.add', $product)}}" method="post" class="w-100">
        @csrf
        <input type="hidden" name="type" value="price" />
        <div class="row w-100">
            @unless($mini)
                <label for="#" class="col-10 col-form-label">Notify when price will be lower</label>
            @endunless
            <div class="col-2">
                <button type="submit" class="btn btn-outline-success"><i class="fa-regular fa-eye"></i></button>
            </div>
        </div>
    </form>
@else
    <form action="{{route('wishlist.remove', $product)}}" method="post" class="w-100">
        @csrf
        @method('delete')
        <input type="hidden" name="type" value="price" />
        <div class="row w-100">
            @unless($mini)
                <label for="#" class="col-10 col-form-label">Product price unsubscribe</label>
            @endunless
            <div class="col-2">
                <button type="submit" class="btn btn-outline-danger"><i class="fa-regular fa-eye-slash"></i></button>
            </div>
        </div>
    </form>
@endif
