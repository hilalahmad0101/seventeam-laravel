@extends('layouts.auth')
@section('title')
    Banner Create
@endsection

@section('content')
    {{-- <x-header title="Banner - List" sub_title="Banner" /> --}}
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Banner</h3>
            <h6 class="op-7 mb-2">Banner - <a href="{{ route('admin.category.list') }}">List</a> - Create</h6>
        </div>
        <div class="ms-md-auto py-2 py-md-0">
            <a href="{{ route('admin.banner.list') }}" class="btn btn-primary btn-round">Go Back</a>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl ">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Banner Create</h3>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row row-cards">
                                    <div class="col-12">
                                        <form action="{{ route('admin.banner.store') }}" method="POST" class="" enctype="multipart/form-data">
                                            @csrf
                                            <div class="card-body">
                                                <div class="row row-cards">
                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Enter Banner Title</label>
                                                            <input type="text" class="form-control" name="title"
                                                                placeholder="Title" id="title" value="">
                                                            @error('title')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Enter Description</label>
                                                            <textarea name="description" class="form-control" id="description" cols="30" rows="10"></textarea>
                                                            @error('description')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input type="file" class="form-control" name="image">
                                                        @error('image')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Upload</button>
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
