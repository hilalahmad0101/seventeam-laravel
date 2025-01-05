<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteVideoFileJob;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{

    public function index()
    {
        $videos = Video::latest()->get();
        return view('admin.video.list', compact('videos'));
    }
    public function create()
    {
        $categories = Category::latest()->get();
        return view('admin.video.create', compact('categories'));
    }
    public function uploadChunk(Request $request)
    {
        $fileName = $request->input('fileName');
        $chunkIndex = $request->input('chunkIndex');
        $totalChunks = $request->input('totalChunks');

        // upload image code please




        // Find or create an upload record
        $upload = Video::firstOrCreate(
            ['file_name' => $fileName],
            [
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'file_path' => 'uploads/' . $fileName,
                'current_chunk' => 0,
                'total_chunks' => $totalChunks,
                // 'thumbnail' => $imageName,
                'status' => 'in_progress',
            ]
        );

        // Check if upload is stopped
        if ($upload->status === 'stopped') {
            return response()->json(['message' => 'Upload is stopped'], 403);
        }

        // Save chunk
        $tempDir = storage_path('app/temp/' . $fileName);
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunkPath = $tempDir . '/' . $chunkIndex;
        file_put_contents($chunkPath, $request->file('file')->get());
        $upload->current_chunk = $chunkIndex + 1; // Update progress
        $upload->save();

        // Check if all chunks are uploaded
        if ($upload->current_chunk == $totalChunks) {
            $finalPath = storage_path('app/' . $upload->file_path);
            $mergedFile = fopen($finalPath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $tempDir . '/' . $i;
                fwrite($mergedFile, file_get_contents($chunkPath));
                unlink($chunkPath);
            }

            fclose($mergedFile);
            rmdir($tempDir);

            $upload->status = 'completed';

            $imageName = time() . '.' . $request->thumbnail->getClientOriginalExtension();
            $request->thumbnail->move(public_path('images/video/thumbnail'), $imageName);
            $upload->thumbnail = $imageName;
            $upload->save();

            return response()->json(['message' => 'Upload complete'], 200);
        }

        return response()->json(['message' => 'Chunk uploaded successfully'], 200);
    }

    public function stopUpload(Request $request)
    {
        $fileName = $request->input('fileName');
        $upload = Video::where('file_name', $fileName)->firstOrFail();

        $upload->status = 'stopped';
        $upload->save();

        return response()->json(['message' => 'Upload stopped successfully'], 200);
    }

    public function resumeUpload(Request $request)
    {
        $fileName = $request->input('fileName');
        $upload = Video::where('file_name', $fileName)->firstOrFail();

        $upload->status = 'in_progress';
        $upload->save();

        return response()->json(['currentChunk' => $upload->current_chunk, 'totalChunks' => $upload->total_chunks]);
    }

    public function getUploadProgress(Request $request)
    {
        $fileName = $request->input('fileName');
        $upload = Video::where('file_name', $fileName)->first();

        if ($upload) {
            return response()->json([
                'currentChunk' => $upload->current_chunk,
                'totalChunks' => $upload->total_chunks,
                'status' => $upload->status
            ]);
        }

        return response()->json(['currentChunk' => 0, 'totalChunks' => 0, 'status' => 'not_started']);
    }


    public function deleteVideo(Request $request)
    {
        $fileName = $request->input('fileName');

        // Find the upload record
        $upload = Video::where('file_name', $fileName)->first();

        if (!$upload) {
            return response()->json(['error' => 'Record not found.'], 404);
        }

        // Delete the database record
        $upload->delete();

        // Mark the file for deletion
        $filePath = storage_path("app/uploads/{$fileName}");

        if (file_exists($filePath)) {
            // Dispatch a background job for file deletion
            DeleteVideoFileJob::dispatch($filePath);

            return response()->json(['message' => 'Record deleted. File deletion is in progress.']);
        }

        return response()->json(['message' => 'Record deleted. File not found.']);
    }


}
