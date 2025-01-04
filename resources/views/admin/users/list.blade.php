@extends('layouts.auth')
@section('title')
    User List
@endsection

@section('content')
    {{-- <x-header title="user - List" sub_title="user" /> --}}
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Users</h3>
            <h6 class="op-7 mb-2">Users - List</h6>
        </div>
        {{-- <div class="ms-md-auto py-2 py-md-0">
            <a href="{{ route('admin.user.create') }}" class="btn btn-primary btn-round">Add user</a>
        </div> --}}
    </div>
    <div class="">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Users</h3>
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
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Is Verified</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->is_verified ? 1 : 0 }}</td>
                                            <td>
                                                {{-- <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}"
                                                    class="btn btn-success">Edit</a> --}}
                                                <a href="{{ route('admin.user.delete', ['id' => $user->id]) }}"
                                                    class="btn btn-danger btn-delete">Delete</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>user not found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            {!! $users->links('pagination::bootstrap-4') !!}
                            {{-- <p class="m-0 text-secondary">Showing <span>1</span> to <span>8</span> of <span>16</span>
                                entries</p> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
