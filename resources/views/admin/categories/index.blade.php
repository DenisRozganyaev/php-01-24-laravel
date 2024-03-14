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
                    <th>@sortablelink('id', 'ID')</th>
                    <th>@sortablelink('name', 'Name')</th>
                    <th>@sortablelink('parent.name', 'Parent')</th>
                    <th>@sortablelink('childs_count', 'Childs count')</th>
                    <th>@sortablelink('products_count', 'Products count')</th>
                    <th>@sortablelink('created_at', 'Created')</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{$category->id}}</td>
                            <td>{{$category->name}}</td>
                            <td>
                                @if ($category->parent)
                                    <a class="link-dark" href="{{route('admin.categories.edit', $category->parent)}}">{{$category->parent->name}}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{$category->childs_count}}</td>
                            <td>{{$category->products_count}}</td>
                            <td>{{$category->created_at}}</td>
                            <td>
                                <form action="{{route('admin.categories.destroy', $category)}}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{route('admin.categories.edit', $category)}}" class="btn btn-warning"><i class="fa-regular fa-pen-to-square"></i></a>
                                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
