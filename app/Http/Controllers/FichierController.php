<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;

class FichierController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('FICHIER_API_KEY'); // set your API key in .env
    }

   
    /**
     * Handle file upload via cURL
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:500000' // max 500MB
        ]);

        $file = $request->file('file');

        // -------------------------
        // STEP 1: Get upload node
        // -------------------------
        $ch = curl_init("https://api.1fichier.com/v1/upload/get_upload_server.cgi");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['pretty' => 1]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        if (!$response) {
            return response()->json(['status' => 'error', 'message' => 'Failed to get upload server', 'curl_error' => curl_error($ch)], 500);
        }
        $serverData = json_decode($response, true);
        if (!isset($serverData['url'], $serverData['id'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid upload server response', 'response' => $response], 500);
        }
        $uploadUrl = "https://{$serverData['url']}/upload.cgi?id={$serverData['id']}";

        // -------------------------
        // STEP 2: Upload file
        // -------------------------
        $postFields = [
            'file[]' => new \CURLFile($file->getRealPath(), $file->getMimeType(), $file->getClientOriginalName())
        ];

        $ch = curl_init($uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // important
        $uploadResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode != 302) {
            return response()->json([
                'status' => 'error',
                'message' => 'Upload failed',
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $uploadResponse
            ], 500);
        }

        // -------------------------
        // STEP 3: Get final download links
        // -------------------------
        $endUrl = "https://{$serverData['url']}/end.pl?xid={$serverData['id']}";
        $ch = curl_init($endUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'JSON: 1'
        ]);
        $finalResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$finalResponse || $httpCode != 200) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get final links',
                'http_code' => $httpCode,
                'response' => $finalResponse
            ], 500);
        }

        $json = json_decode($finalResponse, true);

        if (isset($json['links'][0])) {
            $data = $json['links'][0];

            // Optional: save to database
            $savedFile = File::create([
                'filename' => $data['filename'],
                'download_url' => $data['download'],
                'size' => $data['size'] ?? null,
                'whirlpool' => $data['whirlpool'] ?? null
            ]);

            return response()->json([
                'status' => 'success',
                'file' => $savedFile,
                'raw_response' => $json
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No links returned',
                'raw_response' => $json
            ], 500);
        }
    }
}
