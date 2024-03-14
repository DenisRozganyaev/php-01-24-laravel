@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mt-5 text-center">
            <h3>Products</h3>
        </div>
        <div class="col-12">
            <hr>
        </div>
        <div class="col-12 mt-5">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>@sortablelink('id', 'ID')</th>
                    <th>@sortablelink('title', 'Title')</th>
                    <th>@sortablelink('SKU', 'SKU')</th>
                    <th>@sortablelink('quantity', 'Quantity')</th>
                    <th>Categories</th>
                    <th>@sortablelink('finalPrice', 'Price')</th>
                    <th>@sortablelink('created_at', 'Created')</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{$product->id}}</td>
                            <td>{{$product->title}}</td>
                            <td>{{$product->SKU}}</td>
                            <td>{{$product->quantity}}</td>
                            <td>-</td>
                            <td>{{$product->finalPrice}}</td>
                            <td>{{$product->created_at}}</td>
                            <td>
                                <form action="{{route('admin.products.destroy', $product)}}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{route('admin.products.edit', $product)}}" class="btn btn-warning"><i class="fa-regular fa-pen-to-square"></i></a>
                                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
