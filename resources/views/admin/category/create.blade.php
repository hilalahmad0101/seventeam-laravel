@extends('layouts.auth')
@section('title')
    Category Create
@endsection

@section('content')
    {{-- <x-header title="Category - List" sub_title="Category" /> --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Category - Create
                    </div>
                    <h2 class="page-title">
                        Category
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl ">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Categories Create</h3>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-8">
                                <div class="row row-cards">
                                    <div class="col-12">
                                        <form action="{{ route('admin.category.store') }}" method="POST" class="card">
                                            @csrf
                                            <div class="card-body">
                                                <div class="row row-cards">
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Enter Category Name</label>
                                                            <input type="text" class="form-control" name="name"
                                                                placeholder="Name" value="">

                                                                @error('name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Enter Description</label>
                                                            <input type="text" class="form-control" name="description" placeholder="Description" >
                                                            @error('description')
                                                                <span>{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="px-3 pb-2">
                                                <button type="submit" class="btn btn-success">Create</button>
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
