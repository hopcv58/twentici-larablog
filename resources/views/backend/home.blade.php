@extends('layouts.backend.main')

@section('title', 'LaraBlog | Dashboard')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Dashboard
            </h1>
            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body ">
                            @include('backend.partials.message')
                            <h3>Welcome to LaraBlog!</h3>
                            <p class="lead text-muted">Nice to meet you, {{ Auth::user()->name }}. Welcome to LaraBlog</p>

                            <h4>Get started</h4>
                            <p><a href="{{ route('backend.blog.create') }}" class="btn btn-primary">Write new blog post</a> </p>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
            <!-- ./row -->
        </section>
        <!-- /.content -->
    </div>
@endsection
