<?php

namespace App\Http\Controllers\backend;

use App\Enums\FeedbackStatus;
use App\Exceptions\BusinessException;
use App\Http\Requests\backend\FeedbackRequest;
use App\Http\Resources\backend\FeedbackResource;
use App\Models\Feedback;
use App\Service\FeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly FeedbackService $feedbackService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->feedbackService->paginate(
            $request->only(['keyword', 'fb_status']),
            (int) $request->query('per_page', 15)
        );

        return $this->success([
            'list' => collect($paginator->items())
                ->map(fn (Feedback $item) => $this->feedbackService->toListArray($item))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'options' => [
                'fb_status' => FeedbackStatus::labels(),
            ],
        ]);
    }

    public function show(Feedback $feedback): JsonResponse
    {
        return $this->success($this->feedbackService->toArray($feedback));
    }

    public function reply(FeedbackRequest $request, Feedback $feedback): JsonResponse
    {
        try {
            $item = $this->feedbackService->reply(
                $feedback,
                (string) $request->validated('reply_content')
            );

            return $this->success(
                (new FeedbackResource($item))->resolve(),
                '回复成功'
            );
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(950099, '回复失败');
        }
    }

    public function updateStatus(FeedbackRequest $request, Feedback $feedback): JsonResponse
    {
        $item = $this->feedbackService->updateStatus(
            $feedback,
            (int) $request->validated('fb_status')
        );

        return $this->success(
            (new FeedbackResource($item))->resolve(),
            '状态更新成功'
        );
    }

    public function destroy(Feedback $feedback): JsonResponse
    {
        try {
            $this->feedbackService->delete($feedback);

            return $this->success(null, '删除成功');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(950099, '删除失败');
        }
    }
}
