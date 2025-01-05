<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteVideoFileJob;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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


    public function deleteVideo(Request $request, $id)
    {
        $fileName = $request->input('fileName');

        // Find the upload record
        $upload = Video::where('id', $id)->first();

        if (!$upload) {
            return response()->json(['error' => 'Record not found.'], 404);
        }


        // Mark the file for deletion
        $filePath = storage_path("app/uploads/{$upload->fileName}");
        if (file_exists($filePath)) {
            // Dispatch a background job for file deletion
            DeleteVideoFileJob::dispatch($filePath);

            // return response()->json(['message' => 'Record deleted. File deletion is in progress.']);
        }

        // Delete the database record
        $upload->delete();

        // return response()->json(['message' => 'Record deleted. File not found.']);
        return to_route('admin.video.list')->with('success', 'Video Delete successfully');
    }

    public function edit($id)
    {
        $video = Video::find($id);
        $categories = Category::latest()->get();
        return view('admin.video.update', compact('video', 'categories'));
    }



    public function updateUploadChunk(Request $request)
    {
        $fileName = $request->input('fileName');
        $chunkIndex = $request->input('chunkIndex');
        $totalChunks = $request->input('totalChunks');

        // upload image code please
        // Find or create an upload record
        $upload = Video::firstOrCreate(
            ['id' => $request->video_id],
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

        // if($fileName){}
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

            $old_file = storage_path('/app/' . $upload->file_path);

            if (File::exists($old_file)) {
                File::delete($old_file);
            }
            fclose($mergedFile);
            rmdir($tempDir);

            $upload->status = 'completed';

            if ($request->thumbnail != 'undefined') {
                $thumbnail_path = public_path('images/video/thumbnail/' . $upload->thumbnail);
                if (File::exists($thumbnail_path)) {
                    File::delete($thumbnail_path);
                }
                $thumbnail_file = $request->thumbnail->getClientOriginalName();
                $request->thumbnail->move(public_path('images/video/thumbnail'), $thumbnail_file);
                $upload->thumbnail = $thumbnail_file;
                $upload->save();
            }

            return response()->json(['message' => 'Upload complete'], 200);
        }

        return response()->json(['message' => 'Chunk uploaded successfully'], 200);
    }


    public function getUpdateUploadProgress(Request $request)
    {
        $fileName = $request->input('fileName');
        $id = $request->input('id');
        $upload = Video::where(['file_name' => $fileName, 'id' => $id])->first();

        if ($upload) {
            return response()->json([
                'currentChunk' => $upload->current_chunk,
                'totalChunks' => $upload->total_chunks,
                'status' => $upload->status
            ]);
        }

        return response()->json(['currentChunk' => 0, 'totalChunks' => 0, 'status' => 'not_started']);
    }

    public function changeStatus($id)
    {
        $video = Video::find($id);
        if ($video->is_recommended == 0) {
            $video->is_recommended = 1;
            $video->save();
            return response()->json([
                'success'=>true,
                'message'=>'Recommended On successfully'
            ]);
            // return to_route('admin.video.list')->with('success', 'Recommended On successfully');
        } else {
            $video->is_recommended = 0;
            $video->save();
            return response()->json([
                'success'=>true,
                'message'=>'Recommended Off successfully'
            ]);
            // return to_route('admin.video.list')->with('success', 'Recommended Off successfully');
        }
        // return response()->json(['message' => 'Recommended updated successfully'], 200);
    }


}
