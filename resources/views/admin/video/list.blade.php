@extends('layouts.auth')
@section('title')
    Video List
@endsection

@section('content')
    {{-- <x-header title="video - List" sub_title="video" /> --}}
    {{-- <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                         Video - List
                    </div>
                    <h2 class="page-title">
                         Video
                    </h2>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Video</h3>
            <h6 class="op-7 mb-2">Video - <a href="{{ route('admin.category.list') }}">List</a></h6>
        </div>
        <div class="ms-md-auto py-2 py-md-0">
            <a href="{{ route('admin.video.create') }}" class="btn btn-primary btn-round">Upload Video</a>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Videos</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <div class="d-flex">
                                <div class="text-secondary">
                                    Show
                                    <div class="mx-2 d-inline-block">
                                        <input type="text" class="form-control form-control-sm" value="8"
                                            size="3" aria-label="Invoices count">
                                    </div>
                                    entries
                                </div>
                                <div class="ms-auto text-secondary">
                                    Search:
                                    <div class="ms-2 d-inline-block">
                                        <input type="text" class="form-control form-control-sm"
                                            aria-label="Search invoice">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Title</th>
                                        <th>Video Name</th>
                                        <th>Current Chunk</th>
                                        <th>Total Chunk</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($videos as $video)
                                        <tr>
                                            <td>{{ $video->id }}</td>
                                            <td>{{ $video->title }}</td>
                                            <td>{{ $video->category->name }}</td>
                                            <td>{{ $video->current_chunk }}</td>
                                            <td>{{ $video->total_chunks }}</td>
                                            <td>
                                                {{-- <a href="{{ route('admin.video.edit', ['id' => $video->id]) }}"
                                                    class="btn btn-success">Edit</a> --}}
                                                <a href="{{ route('admin.video.delete', ['id' => $video->id]) }}"
                                                    class="btn btn-danger btn-delete">Delete</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>video not found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            <p class="m-0 text-secondary">Showing <span>1</span> to <span>8</span> of <span>16</span>
                                entries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
