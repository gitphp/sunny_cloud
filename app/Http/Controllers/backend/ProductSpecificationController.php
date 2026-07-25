<?php

namespace App\Http\Controllers\backend;

use App\Enums\ProductShowStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\ProductSpecificationRequest;
use App\Http\Resources\backend\ProductSpecificationResource;
use App\Http\Resources\backend\ProductSpecificationValueResource;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationValue;
use App\Service\ProductSpecificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ProductSpecificationController extends AbstractController
{
    public function __construct(
        private readonly ProductSpecificationService $productSpecificationService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->productSpecificationService->paginate(
            $request->only(['keyword', 'spec_status']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (ProductSpecification $item) => $this->productSpecificationService->toArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'spec_status' => ProductShowStatus::labels(),
                'value_status' => ProductShowStatus::labels(),
            ],
        ]);
    }

    public function store(ProductSpecificationRequest $request): JsonResponse
    {
        try {
            $spec = $this->productSpecificationService->create($request->validated());

            return $this->success((new ProductSpecificationResource($spec))->resolve(), '添加成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(300099, '添加失败');
        }
    }

    public function update(ProductSpecificationRequest $request, ProductSpecification $productSpecification): JsonResponse
    {
        try {
            $spec = $this->productSpecificationService->update($productSpecification, $request->validated());

            return $this->success((new ProductSpecificationResource($spec))->resolve(), '修改成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(300099, '修改失败');
        }
    }

    public function updateSort(ProductSpecificationRequest $request, ProductSpecification $productSpecification): JsonResponse
    {
        $spec = $this->productSpecificationService->updateSort(
            $productSpecification,
            (int) $request->validated('sort_order')
        );

        return $this->success((new ProductSpecificationResource($spec))->resolve(), '排序更新成功');
    }

    public function updateStatus(ProductSpecificationRequest $request, ProductSpecification $productSpecification): JsonResponse
    {
        $spec = $this->productSpecificationService->updateStatus(
            $productSpecification,
            (int) $request->validated('spec_status')
        );

        return $this->success((new ProductSpecificationResource($spec))->resolve(), '状态更新成功');
    }

    public function destroy(ProductSpecification $productSpecification): JsonResponse
    {
        try {
            $this->productSpecificationService->delete($productSpecification);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(300099, '删除失败');
        }
    }

    public function values(ProductSpecification $productSpecification): JsonResponse
    {
        return $this->success($this->productSpecificationService->listValues($productSpecification));
    }

    public function storeValue(ProductSpecificationRequest $request, ProductSpecification $productSpecification): JsonResponse
    {
        try {
            $value = $this->productSpecificationService->createValue($productSpecification, $request->validated());

            return $this->success((new ProductSpecificationValueResource($value))->resolve(), '添加成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(400099, '添加失败');
        }
    }

    public function updateValue(ProductSpecificationRequest $request, ProductSpecificationValue $productSpecificationValue): JsonResponse
    {
        try {
            $value = $this->productSpecificationService->updateValue($productSpecificationValue, $request->validated());

            return $this->success((new ProductSpecificationValueResource($value))->resolve(), '修改成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(400099, '修改失败');
        }
    }

    public function updateValueSort(ProductSpecificationRequest $request, ProductSpecificationValue $productSpecificationValue): JsonResponse
    {
        $value = $this->productSpecificationService->updateValueSort(
            $productSpecificationValue,
            (int) $request->validated('sort_order')
        );

        return $this->success((new ProductSpecificationValueResource($value))->resolve(), '排序更新成功');
    }

    public function updateValueStatus(ProductSpecificationRequest $request, ProductSpecificationValue $productSpecificationValue): JsonResponse
    {
        $value = $this->productSpecificationService->updateValueStatus(
            $productSpecificationValue,
            (int) $request->validated('value_status')
        );

        return $this->success((new ProductSpecificationValueResource($value))->resolve(), '状态更新成功');
    }

    public function destroyValue(ProductSpecificationValue $productSpecificationValue): JsonResponse
    {
        try {
            $this->productSpecificationService->deleteValue($productSpecificationValue);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(400099, '删除失败');
        }
    }
}
