<?php

namespace App\Http\Controllers\Ajax\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ajax\Products\UploadImageRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class UploadImage extends Controller
{
    public function __invoke(UploadImageRequest $request, Product $product)
    {
        try {
            $data = $request->validated();
            $imagesData = [];
            foreach($data['images'] as $image) {
                $img = $product->images()->create([
                    'path' => [
                        'image' => $image,
                        'directory' => $product->slug
                    ]
                ]);
                $imagesData[] = ['url' => $img->url, 'id' => $img->id];
            }

            return response()->json($imagesData);
        } catch (\Throwable $throwable) {
            logs()->error($throwable);

            return response(status: 422)
                ->json([
                    'message' => $throwable->getMessage()
                ]);
        }
    }
}
