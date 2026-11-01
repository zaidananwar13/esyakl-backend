@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@else

@extends('home/master2')
@section('judul', 'Form Data Silabus')
@section('data-controller', 'active')
@section('silabus', 'text-active')
@section('kategori-silabus', 'text-active')
@section('css')
<style>
    .custom-container {
        background-color: white;
        padding-top: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .1);
    }

    .text-active {
        color: #009ef7 !important;
    }
</style>
@section('content')

<div class="custom-container">
    <form role="form" enctype="multipart/form-data"
     action="/project/submit" method="POST">
        {{ csrf_field() }}
        <div class="card-body">
          <input type="hidden" name="api_token" value="8efec85d53601009dcc59ec97490e90bbe6e7b6e79f3a1dacf5ca20ca0fce967">
          <input type="hidden" name="project" value="9">
        <input type="file" name="project_file">
        <textarea name="note" placeholder="noto" cols="30" rows="10"></textarea>


        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            <input type="submit" class="btn btn-success" value="{{ ($action!='kategori_silabus.store') ? 'Simpan' : 'Update' }}">
        </div>
    </form>

</div>

@endsection

@endif