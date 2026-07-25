<?php

namespace App\Http\Controllers\backend;

use App\Enums\HrIsMain;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\HrUserDeptPostRequest;
use App\Http\Resources\backend\HrUserDeptPostResource;
use App\Models\HrUserDeptPost;
use App\Service\HrUserDeptPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class HrUserDeptPostController extends AbstractController
{
    public function __construct(
        private readonly HrUserDeptPostService $hrUserDeptPostService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->hrUserDeptPostService->paginate(
            $request->only(['keyword', 'user_id', 'dept_id', 'post_id', 'is_main']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (HrUserDeptPost $item) => $this->hrUserDeptPostService->toArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'is_main' => HrIsMain::labels(),
            ],
        ]);
    }

    public function store(HrUserDeptPostRequest $request): JsonResponse
    {
        try {
            $record = $this->hrUserDeptPostService->create($request->validated());
            $record->load(['user', 'department', 'post']);

            return $this->success(
                (new HrUserDeptPostResource($record))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(850099, '添加失败');
        }
    }

    public function update(HrUserDeptPostRequest $request, HrUserDeptPost $hrUserDeptPost): JsonResponse
    {
        try {
            $record = $this->hrUserDeptPostService->update($hrUserDeptPost, $request->validated());

            return $this->success(
                (new HrUserDeptPostResource($record))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(850099, '修改失败');
        }
    }

    public function destroy(HrUserDeptPost $hrUserDeptPost): JsonResponse
    {
        try {
            $this->hrUserDeptPostService->delete($hrUserDeptPost);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(850099, '删除失败');
        }
    }
}
