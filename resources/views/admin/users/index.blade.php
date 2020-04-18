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
                
                    <table class="table">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>
                                <a href="" class="btn btn-default">Show</a>
                                <a href="{{ route('user.edit') }}" class="btn btn-primary">Edit</a>
                                <a href="{{ route('user.delete') }}" class="btn btn-danger">Hapus</a>
                            </td>
                        </tr>                            
                        @endforeach

                    </table>

                </div>
            </div>
        </div>
    </div>
</div>    
@endsection
