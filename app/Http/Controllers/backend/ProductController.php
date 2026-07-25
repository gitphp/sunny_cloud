<?php

namespace App\Http\Controllers\backend;

use App\Enums\ProductMediaType;
use App\Enums\ProductSkuSaleStatus;
use App\Enums\ProductStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\ProductRequest;
use App\Models\Product;
use App\Service\LocalUploadService;
use App\Service\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly LocalUploadService $localUploadService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->productService->paginate(
            $request->only(['keyword', 'category_id', 'brand_id', 'product_status']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (Product $item) => $this->productService->toListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'product_status' => ProductStatus::labels(),
                'media_type' => ProductMediaType::labels(),
                'sale_status' => ProductSkuSaleStatus::labels(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->success($this->productService->detail($product));
    }

    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->create($request->validated());

            return $this->success($this->productService->detail($product), '添加成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(500099, '添加失败');
        }
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        try {
            $product = $this->productService->update($product, $request->validated());

            return $this->success($this->productService->detail($product), '修改成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(500099, '修改失败');
        }
    }

    public function updateStatus(ProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->updateStatus(
            $product,
            (int) $request->validated('product_status')
        );

        return $this->success($this->productService->toListArray($product), '状态更新成功');
    }

    public function destroy(Product $product): JsonResponse
    {
        try {
            $this->productService->delete($product);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(500099, '删除失败');
        }
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'],
            'media_type' => ['nullable', 'integer'],
        ], [
            'file.required' => '请选择文件',
            'file.max' => '文件不能超过20MB',
        ]);

        try {
            $meta = $this->localUploadService->store($request->file('file'), 'products');
            $meta['media_type'] = (int) $request->input('media_type', 0);

            return $this->success($meta, '上传成功');
        } catch (Throwable $e) {
            report($e);

            return $this->error(500098, '上传失败');
        }
    }
}
