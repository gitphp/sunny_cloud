<?php

namespace App\Http\Controllers\frontend;

use App\Exceptions\BusinessException;
use App\Http\Requests\frontend\FeedbackRequest;
use App\Service\FeedbackService;
use Illuminate\Http\JsonResponse;
use Throwable;

class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly FeedbackService $feedbackService
    ) {
    }

    public function store(FeedbackRequest $request): JsonResponse
    {
        try {
            $feedback = $this->feedbackService->create(
                $request->validated(),
                $request->ip() ?? ''
            );

            return $this->success([
                'id' => (string) $feedback->id,
            ], '提交成功，我们会尽快处理');
        } catch (BusinessException $e) {
            return $this->error($e->getErrorCode(), $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return $this->error(950099, '提交失败');
        }
    }
}
