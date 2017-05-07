@extends('layouts.main')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">PDF Uploader</div>
            <div class="panel-body">
                @if (Session::has('message'))
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                @endif
                @if (!$configExists)
                    <p>
                        Please upload auth configuration JSON to connect your Google Drive account.
                    </p>
                    {!! Form::open(array('route' => 'site.uploadConfig', 'enctype' => 'multipart/form-data')) !!}

                    <div class="row cancel">
                        <div class="col-md-12">

                            {!! Form::file('config') !!}

                        </div>
                        <div class="col-md-12" style="margin-top: 10px">
                                <button type="submit" class="btn btn-primary">Upload</button>
                        </div>

                    </div>
                {!! Form::close() !!}
                @else
                    <p>
                        Please fill your Gmail credentials to upload files to Google Drive.
                    </p>
                    {!! Form::open(array('route' => 'site.uploadFromMail', 'enctype' => 'multipart/form-data')) !!}
                    <div class="row cancel">

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::input('email', 'email', null, ['placeholder' => 'Enter your email', 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::input('password', 'password', null, ['placeholder' => 'Enter your password', 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>

                    </div>
                    {!! Form::close() !!}
                @endif
            </div>
        </div>
    </div>
@endsection