<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil diambil.',
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'pagination' => [
                    'total' => $notifications->total(),
                    'count' => $notifications->count(),
                    'per_page' => $notifications->perPage(),
                    'current_page' => $notifications->currentPage(),
                    'total_pages' => $notifications->lastPage(),
                ],
            ],
        ]);
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $notification->update([
            'status' => 'read',
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai dibaca.',
            'data' => null,
            'meta' => null,
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', 'read')
            ->update([
                'status' => 'read',
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai dibaca.',
            'data' => null,
            'meta' => null,
        ]);
    }
}
