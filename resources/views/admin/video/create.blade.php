@extends('layouts.auth')
@section('title')
    Video Create
@endsection

@section('content')
    {{-- <x-header title="Video - List" sub_title="Video" /> --}}
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
            <h6 class="op-7 mb-2">Video - <a href="{{ route('admin.video.list') }}">List</a> - Create</h6>
        </div>
        <div class="ms-md-auto py-2 py-md-0">
            <a href="{{ route('admin.video.list') }}" class="btn btn-primary btn-round">Go Back</a>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl ">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Video Create</h3>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row row-cards">
                                    <div class="col-12">
                                        {{-- <form action="{{ route('admin.vodep') }}" method="POST" class=""> --}}
                                        @csrf
                                        <div class="card-body">
                                            <div class="row row-cards">
                                                <div class="col-sm-12 col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Enter Video Title</label>
                                                        <input type="text" class="form-control" name="title"
                                                            placeholder="Title" id="title" value="">
                                                        @error('title')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Select Category</label>
                                                        <select name="category_id" id="category_id" class="form-select">
                                                            <option value="">Select Category</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_id')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Enter Description</label>
                                                        <textarea name="description" class="form-control" id="description" cols="30" rows="10"></textarea>
                                                        @error('description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 col-md-12">
                                                    <label for="">Upload Thumbnail</label>
                                                    <input type="file" class="form-control" id="video-thumbnail">
                                                </div>

                                                <div class="col-sm-12 col-md-12">
                                                    <label for="">Upload Video</label>
                                                    <input type="file" class="form-control" id="video-upload">
                                                    <progress id="progress-bar" value="0"
                                                        style="width: 100%;margin:10px 0px" max="100"></progress>
                                                    {{-- <button id="stop-button" class="btn btn-success">Stop</button>
                                                    <button id="resume-button" class="btn btn-info">Resume</button> --}}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="px-3 pb-2">
                                                <button type="submit" class="btn btn-success">Create</button>
                                            </div> --}}
                                        <button id="upload-button" class="btn btn-primary">Upload</button>
                                        {{-- </form> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const fileInput = document.getElementById('video-upload');
        const fileThumbnail = document.getElementById('video-thumbnail');
        const progressBar = document.getElementById('progress-bar');
        const stopButton = document.getElementById('stop-button');
        const resumeButton = document.getElementById('resume-button');
        const uploadButton = document.getElementById('upload-button');

        const CHUNK_SIZE = 1 * 1024 * 1024; // 10 MB
        let file, totalChunks, currentChunk = 0,
            uploading = false,thumbnail;

        // Store selected file and initialize chunk details
        fileInput.addEventListener('change', () => {
            file = fileInput.files[0];
            totalChunks = Math.ceil(file.size / CHUNK_SIZE);
            currentChunk = 0;
            progressBar.value = 0;
        });

        fileThumbnail.addEventListener('change', function(){
            thumbnail=fileThumbnail.files[0];
        })

        // Start uploading when Upload button is clicked
        uploadButton.addEventListener('click', async () => {
            if (!file) {
                alert('Please select a video file first.');
                return;
            }

            // Disable the Upload button and show progress
            uploadButton.disabled = true;
            uploadButton.textContent = "Uploading...";

            // Check the current progress from the server
            const response = await fetch('/get-upload-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    fileName: file.name,
                    "_token": "{{ csrf_token() }}"
                }),
            });

            const data = await response.json();
            currentChunk = data.currentChunk;

            if (data.status === 'completed') {
                alert('This file has already been uploaded.');
                resetUploadState();
                return;
            }

            await uploadChunks();
        });

        // Stop uploading
        // stopButton.addEventListener('click', async () => {
        //     uploading = false;
        //     await fetch('/stop-upload', {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json'
        //         },
        //         body: JSON.stringify({
        //             fileName: file.name,
        //             "_token": "{{ csrf_token() }}"
        //         }),
        //     });
        // });

        // Resume uploading
        // resumeButton.addEventListener('click', async () => {
        //     const response = await fetch('/resume-upload', {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json'
        //         },
        //         body: JSON.stringify({
        //             fileName: file.name,
        //             "_token": "{{ csrf_token() }}"
        //         }),
        //     });

        //     const data = await response.json();
        //     currentChunk = data.currentChunk;
        //     totalChunks = data.totalChunks;

        //     await uploadChunks();
        // });

        // Upload file chunks
        async function uploadChunks() {
            uploading = true;

            while (currentChunk < totalChunks && uploading) {
                const start = currentChunk * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('thumbnail',thumbnail);
                formData.append('fileName', file.name);
                formData.append('chunkIndex', currentChunk);
                formData.append('totalChunks', totalChunks);
                formData.append('title', document.getElementById('title').value);
                formData.append('description', document.getElementById('description').value);
                formData.append('category_id', document.getElementById('category_id').value);
                formData.append('_token', "{{ csrf_token() }}");

                await fetch('/upload-chunk', {
                    method: 'POST',
                    body: formData,
                });

                currentChunk++;
                progressBar.value = (currentChunk / totalChunks) * 100;
            }

            if (currentChunk === totalChunks) {
                alert('Upload complete!');
                resetUploadState();
            }
        }

        // Reset upload state after completion
        function resetUploadState() {
            fileInput.value = ""; // Clear file input
            file = null; // Reset file variable
            progressBar.value = 0; // Reset progress bar
            uploadButton.disabled = false; // Enable Upload button
            uploadButton.textContent = "Upload"; // Reset button text
        }
    </script>
@endsection
