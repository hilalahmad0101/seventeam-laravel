@extends('layouts.guest')
@section('title')
    Login
@endsection


@section('content')
<div class="card-body">
    <div class="col-lg-12">
        <div class="row row-cards">
            <div class="col-12">
                <form action="{{ route('admin.login') }}" method="POST" class="">
                    @csrf
                    <div class="">
                        <div class="row ">
                            <div class="col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Enter Email</label>
                                    <input type="text" class="form-control" name="email"
                                        placeholder="Email" value="{{ old('email') }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Enter Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="Password" >
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" pb-2">
                        <button type="submit" class="btn btn-success">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection