<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload to 1fichier</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="text-center mb-4">📤 Upload to 1fichier</h2>

    <div class="card shadow-sm p-4">
        <input type="file" id="fileInput" class="form-control mb-3">
        <button class="btn btn-primary w-100" id="uploadBtn">Upload</button>

        <div class="progress mt-3" style="height: 25px;">
            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;">0%</div>
        </div>
        <p id="speed" class="mt-2"></p>
    </div>

    <pre id="output" class="mt-3 p-3 bg-white border"></pre>
</div>

<script>
document.getElementById('uploadBtn').onclick = async () => {
    const file = document.getElementById('fileInput').files[0];
    if (!file) return alert('Select a file first!');

    const formData = new FormData();
    formData.append('file', file);

    const output = document.getElementById('output');
    const progressBar = document.getElementById('progressBar');
    const speedText = document.getElementById('speed');

    output.innerText = 'Uploading...';
    progressBar.style.width = '0%';
    progressBar.innerText = '0%';
    speedText.innerText = '';

    const startTime = Date.now();

    try {
        // Use HTTPS-safe URL dynamically
        const uploadUrl = window.location.origin + '/api/upload';

        const response = await axios.post(uploadUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: progressEvent => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                progressBar.style.width = percentCompleted + '%';
                progressBar.innerText = percentCompleted + '%';

                // Calculate upload speed
                const elapsedTime = (Date.now() - startTime) / 1000; // seconds
                const speed = progressEvent.loaded / elapsedTime; // bytes/sec
                const speedMB = (speed / (1024*1024)).toFixed(2); // MB/sec
                speedText.innerText = `Upload speed: ${speedMB} MB/s`;
            }
        });

        const data = response.data;

        if (data.status === 'success') {
            const f = data.file;
            output.innerText = `✅ Upload Successful!\nFilename: ${f.filename}\nDownload: ${f.download_url}\nSize: ${f.size}\nWhirlpool: ${f.whirlpool}`;
        } else {
            output.innerText = `❌ Upload Failed!\nMessage: ${data.message}\nRaw: ${JSON.stringify(data.raw_response, null, 2)}`;
        }
    } catch (err) {
        console.error(err);
        output.innerText = 'Error occurred. Check console.';
    }
};
</script>
</body>
</html>
