@extends('layouts.app')

@section('content')
    <main>
        <div class="container">
            <div class="row">
                <div class="col-9">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cart->content() as $row)
                            <tr>
                                <td><img src="{{$row->model->thumbnailUrl}}" style="width: 100px;" alt="{{$row->name}}"></td>
                                <td><a class="link-info link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                                        href="{{route('products.show', $row->model)}}">{{$row->name}}</a></td>
                                <td>
                                    <form action="{{route('cart.count', $row->model)}}" method="POST">
                                        @csrf
                                        <input type="hidden" name="rowId" value="{{$row->rowId}}" />
                                        <input type="number" name="count" id="counter" value="{{$row->qty}}" min="1" max="{{$row->model->quantity}}" />
                                    </form>
                                </td>
                                <td>{{$row->price}}$</td>
                                <td>{{$row->subtotal}}$</td>
                                <td>
                                    <form action="{{route('cart.remove')}}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="rowId" value="{{$row->rowId}}" />
                                        <button type="submit" class="btn btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-3">
                    <table class="table table-dark table-striped">
                        <tbody>
                        <tr>
                            <td>Subtotal</td>
                            <td>{{$cart->subtotal()}}</td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td>{{$cart->tax()}}</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td>{{$cart->total()}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <a href="{{route('checkout')}}" class="btn btn-outline-success w-100">Proceed to checkout</a>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('footer-js')
    @vite(['resources/js/cart.js'])
@endpush
