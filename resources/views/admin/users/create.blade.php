@extends('layouts.app')
@section('title')
    Users
@endsection
@section('headerContentRight')
    <a href="{{ route(strtolower($title).'.create') }} " class="btn btn-primary">+ Tambah {{ $title }}</a>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Demo purpose only -->
                <div style="min-height: 300px;">
                    <h6>Manajemen Data {{ $title }} </h6>
                    <p>Tabel {{ $title }}</p>
                


                </div>
            </div>
        </div>
    </div>
</div>    
@endsection