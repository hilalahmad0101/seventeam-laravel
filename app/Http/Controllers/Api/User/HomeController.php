<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function listOfMovies()
    {
        try {
            // Fetch paginated videos
            $videos = Video::latest()->paginate(10);

            // Return response with pagination details
            return response()->json([
                'success' => true,
                'data' => $videos->items(), // The actual paginated data
                'pagination' => [
                    'total' => $videos->total(), // Total number of items
                    'per_page' => $videos->perPage(), // Items per page
                    'current_page' => $videos->currentPage(), // Current page number
                    'last_page' => $videos->lastPage(), // Last page number
                    'next_page_url' => $videos->nextPageUrl(), // URL of the next page
                    'prev_page_url' => $videos->previousPageUrl(), // URL of the previous page
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function listMoviesByCategory()
    {
        try {
            // Fetch categories with their movies
            $categories = Category::with([
                'videos' => function ($query) {
                    $query->latest(); // Order videos by latest
                }
            ])->get();

            // Format response
            $data = $categories->map(function ($category) {
                return [
                    'category_name' => $category->name,
                    'movies' => $category->videos->map(function ($video) {
                        return [
                            'id' => $video->id,
                            'title' => $video->title,
                            // Include other video fields as needed
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }


    public function movieDetails($id)
    {
        try {
            $video = Video::findOrFail($id);
            if (!$video) {
                return response()->json([
                    'success' => false,
                    'message' => 'Video not found'
                ]);
            }
            return response()->json([
                'success' => true,
                'video' => $video
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }



    public function streamVideo($id)
    {
        try {

            $video = Video::findOrFail($id);
            if (!$video) {
                return response()->json([
                    'success' => false,
                    'message' => 'Video not found'
                ]);
            }
            $filename = $video->file_name;
            // Path to the video file
            $filePath = storage_path("app/uploads/{$filename}");

            // Check if file exists
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // File size
            $fileSize = filesize($filePath);

            // Default start and end range
            $start = 0;
            $end = $fileSize - 1;

            // Check for the Range header
            $headers = getallheaders();
            if (isset($headers['Range'])) {
                $range = str_replace('bytes=', '', $headers['Range']);
                [$start, $end] = explode('-', $range) + [$start, $end];
                $end = $end === "" ? $fileSize - 1 : (int) $end;
            }

            // Set headers for partial content
            $length = $end - $start + 1;
            $responseHeaders = [
                'Content-Type' => mime_content_type($filePath),
                'Content-Length' => $length,
                'Accept-Ranges' => 'bytes',
                'Content-Range' => "bytes $start-$end/$fileSize",
            ];

            // Open file and stream the content
            $file = fopen($filePath, 'rb');
            fseek($file, $start);
            $content = fread($file, $length);
            fclose($file);

            $video->views = $video->views + 1;
            $video->save();

            return response($content, 206, $responseHeaders);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function searchVideos(Request $request)
    {
        try {
            // Retrieve search query parameters
            $query = $request->input('query', '');
            $category = $request->input('category', '');

            // Build the query for videos
            $videos = Video::query();

            if (!empty($query)) {
                $videos->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            }

            if (!empty($category)) {
                $videos->whereHas('category', function ($q) use ($category) {
                    $q->where('name', 'LIKE', "%{$category}%");
                });
            }

            // Fetch paginated results
            $results = $videos->latest()->paginate(10);

            // Response
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function getBanner()
    {
        try {
            $banners = Banner::where('status', 1)->latest()->get();
            if (count($banners) == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No banner found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'banner' => $banners
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}