<?php

namespace App\Http\Controllers\frontend;

use App\Service\PortalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortalController extends AbstractController
{
    public function __construct(
        private readonly PortalService $portalService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $channelId = $request->query('channel_id');
        if ($channelId === '' || $channelId === null) {
            $channelId = null;
        }

        return $this->success($this->portalService->index($channelId !== null ? (string) $channelId : null));
    }
}
