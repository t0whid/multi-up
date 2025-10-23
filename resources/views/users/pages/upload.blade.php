@extends('users.layouts.app')
@section('title', 'Upload to MultiUp')

@section('content')
<div class="hosting">
    <img src="{{ asset('images/icon.svg') }}" alt="icon" class="top-icon">

    <div class="upload-card">
        <h3>The free-for-all File Hosting.</h3>
        <p>Running in private mode.</p>

        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <label for="fileInput" class="custom-file-label" id="fileLabel">📁 Choose a file to upload</label>
            <input type="file" id="fileInput" name="file" accept=".zip" required>
            <button class="btn btn-purple" type="submit">🚀 Upload</button>
            <div class="small-text">Max upload size: <b>2 GB (only zip)</b></div>
        </form>

        <div id="progressContainer" class="mt-3" style="display:none;">
            <div class="progress">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width:0%">0%</div>
            </div>
            <div class="small-text mt-1">Speed: <span id="uploadSpeed">0 KB/s</span></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@endsection
