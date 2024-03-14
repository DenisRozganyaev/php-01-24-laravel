@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 mt-5">
                <form action="{{route('admin.categories.update', $category)}}" method="POST" class="d-flex align-items-center justify-content-center">
                    <div class="card w-50">
                        <div class="card-header text-center">
                            <h3>Update "{{ $category->name }}"</h3>
                        </div>
                        <div class="card-body">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="name"
                                           value="{{ old('name') ?? $category->name }}" required autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="parent_id"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Parent Category') }}</label>

                                <div class="col-md-6">
                                    <select name="parent_id" id="parent_id"
                                            class="form-control @error('parent_id') is-invalid @enderror">
                                        <option value=""></option>
                                        @foreach($dropdown as $item)
                                            <option
                                                value="{{$item->id}}"
                                                @if($item->id === $category->parent?->id) selected @endif
                                            >{{$item->name}}</option>
                                        @endforeach
                                    </select>

                                    @error('parent_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
