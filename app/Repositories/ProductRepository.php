<?php

namespace App\Repositories;

use App\Http\Requests\Admin\Products\CreateRequest;
use App\Http\Requests\Admin\Products\UpdateRequest;
use App\Models\Product;
use App\Repositories\Contract\ImageRepositoryContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductRepository implements Contract\ProductRepositoryContract
{

    public function __construct(protected ImageRepositoryContract $imageRepository)
    {
    }

    public function create(CreateRequest $request): Product|false
    {
        try {
            DB::beginTransaction();

            $data = $this->formRequestData($request);
            $product = Product::create($data['attributes']);
            $this->setProductData($product, $data);

            DB::commit();

            return $product;
        } catch (\Exception $exception) {
            DB::rollBack();
            logs()->error($exception);

            return false;
        }
    }

    public function update(Product $product, UpdateRequest $request): bool
    {
        try {
            DB::beginTransaction();

            $data = $this->formRequestData($request);
            $product->update($data['attributes']);
            $this->setProductData($product, $data);

            DB::commit();

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            logs()->error($exception);

            return false;
        }
    }

    protected function setProductData(Product $product, array $data): void
    {
        $product->categories()->sync($data['categories']);

        if (!empty($data['attributes']['images'])) {
            $this->imageRepository->attach(
                $product,
                'images',
                $data['attributes']['images'],
                $product->slug
            );
        }
    }

    protected function formRequestData(CreateRequest|UpdateRequest $request): array
    {
        return [
            'attributes' => collect($request->validated())
                ->except(['categories'])
                ->prepend(Str::slug($request->get('title')), 'slug')
                ->toArray(),
            'categories' => $request->get('categories', [])
        ];
    }
}
