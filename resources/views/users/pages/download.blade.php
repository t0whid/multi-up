@extends('users.layouts.app')
@section('title', 'Download File')

@section('content')
<div class="hosting">
    <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">

    <div class="upload-card">
        <h3>✅ Upload Complete!</h3>
        <p>Your file is ready to download.</p>

        <div class="file-info">
            <strong>File:</strong> {{ $fileName }} <br>
            <strong>Download URL:</strong> <br>
            <a href="{{ $downloadLink }}" target="_blank" class="btn btn-purple">
                {{ $downloadLink }}
            </a>
        </div>

        <div class="mt-3">
            <a href="{{ route('upload.index') }}" class="btn btn-gray">⬅ Upload Another</a>
        </div>
    </div>
</div>
@endsection
