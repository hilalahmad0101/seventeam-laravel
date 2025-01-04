@extends('layouts.auth')
@section('title')
    Category Update
@endsection

@section('content')
    {{-- <x-header title="Category - List" sub_title="Category" /> --}}
    {{-- <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Category - Update
                    </div>
                    <h2 class="page-title">
                        Category
                    </h2>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Category</h3>
            <h6 class="op-7 mb-2">Category - <a href="{{ route('admin.category.list') }}">List</a> - Update</h6>
        </div>
        <div class="ms-md-auto py-2 py-md-0">
            <a href="{{ route('admin.category.list') }}" class="btn btn-primary btn-round">Go Back</a>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl ">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Categories Update</h3>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row row-cards">
                                    <div class="col-12">
                                        <form action="{{ route('admin.category.update',['id'=>$category->id]) }}" method="POST" class="">
                                            @csrf
                                            <div class="card-body">
                                                <div class="row row-cards">
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Enter Category Name</label>
                                                            <input type="text" class="form-control" name="name"
                                                                placeholder="Name" value="{{ $category->name }}">

                                                                @error('name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Enter Description</label>
                                                            <input type="text" class="form-control" name="description" value="{{ $category->description }}" placeholder="Description" >
                                                            @error('description')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="px-3 pb-2">
                                                <button type="submit" class="btn btn-success">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
