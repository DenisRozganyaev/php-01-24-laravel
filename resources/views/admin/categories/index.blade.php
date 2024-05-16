@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mt-5 text-center">
            <h3>Categories</h3>
        </div>
        <div class="col-12">
            <hr>
        </div>
        <div class="col-12 mt-5">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Products count</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{$category->id}}</td>
                            <td>{{$category->name}}</td>
                            <td>{{$category->parent->name ?? '-'}}</td>
                            <td>{{$category->products_count}}</td>
                            <td>Actions</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
