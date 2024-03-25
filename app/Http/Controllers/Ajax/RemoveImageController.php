<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RemoveImageController extends Controller
{
    public function __invoke(Image $image): JsonResponse
    {
        try {
            $image->deleteOrFail();

            return response()->json(['message' => 'Image was removed']);
        } catch (\Throwable $throwable) {
            logs()->error($throwable);

            return response()->json([
                'message' => $throwable->getMessage()
            ]);
        }
    }
}
