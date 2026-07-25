<?php

namespace App\Http\Controllers\backend;

use App\Enums\HrDeptStatus;
use App\Enums\HrLeaderRoleType;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\HrDepartmentRequest;
use App\Http\Resources\backend\HrDepartmentResource;
use App\Models\HrDepartment;
use App\Service\HrDepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class HrDepartmentController extends AbstractController
{
    public function __construct(
        private readonly HrDepartmentService $hrDepartmentService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $tree = $this->hrDepartmentService->getTree($request->query('keyword'));

        return $this->success([
            'list' => $tree,
            'options' => [
                'dept_status' => HrDeptStatus::labels(),
                'role_type' => HrLeaderRoleType::labels(),
            ],
        ]);
    }

    public function store(HrDepartmentRequest $request): JsonResponse
    {
        try {
            $department = $this->hrDepartmentService->create($request->validated());

            return $this->success(
                (new HrDepartmentResource($department))->resolve(),
                '添加成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(830099, '添加失败');
        }
    }

    public function update(HrDepartmentRequest $request, HrDepartment $hrDepartment): JsonResponse
    {
        try {
            $department = $this->hrDepartmentService->update($hrDepartment, $request->validated());

            return $this->success(
                (new HrDepartmentResource($department))->resolve(),
                '修改成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(830099, '修改失败');
        }
    }

    public function updateSort(HrDepartmentRequest $request, HrDepartment $hrDepartment): JsonResponse
    {
        $department = $this->hrDepartmentService->updateSort(
            $hrDepartment,
            (int) $request->validated('dept_sort')
        );

        return $this->success(
            (new HrDepartmentResource($department))->resolve(),
            '排序更新成功'
        );
    }

    public function updateStatus(HrDepartmentRequest $request, HrDepartment $hrDepartment): JsonResponse
    {
        $department = $this->hrDepartmentService->updateStatus(
            $hrDepartment,
            (int) $request->validated('dept_status')
        );

        return $this->success(
            (new HrDepartmentResource($department))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(HrDepartment $hrDepartment): JsonResponse
    {
        try {
            $this->hrDepartmentService->delete($hrDepartment);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(830099, '删除失败');
        }
    }

    public function leaders(HrDepartment $hrDepartment): JsonResponse
    {
        return $this->success($this->hrDepartmentService->getLeaders($hrDepartment));
    }

    public function syncLeaders(HrDepartmentRequest $request, HrDepartment $hrDepartment): JsonResponse
    {
        try {
            $leaders = $this->hrDepartmentService->syncLeaders(
                $hrDepartment,
                $request->validated('leaders')
            );

            return $this->success($leaders, '负责人保存成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(830098, '负责人保存失败');
        }
    }
}
