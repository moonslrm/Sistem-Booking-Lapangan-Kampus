<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success(mixed $data, string $message, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $this->meta(),
        ], $code);
    }

    public function error(string $message, mixed $errors = null, int $code = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $errors === null ? null : ['errors' => $errors],
            'meta' => $this->meta(),
        ], $code);
    }

    public function paginated(mixed $paginator, string $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'items' => method_exists($paginator, 'items') ? $paginator->items() : [],
                'pagination' => [
                    'current_page' => method_exists($paginator, 'currentPage') ? $paginator->currentPage() : null,
                    'per_page' => method_exists($paginator, 'perPage') ? $paginator->perPage() : null,
                    'total' => method_exists($paginator, 'total') ? $paginator->total() : null,
                    'last_page' => method_exists($paginator, 'lastPage') ? $paginator->lastPage() : null,
                    'from' => method_exists($paginator, 'firstItem') ? $paginator->firstItem() : null,
                    'to' => method_exists($paginator, 'lastItem') ? $paginator->lastItem() : null,
                ],
            ],
            'meta' => $this->meta(),
        ]);
    }

    protected function meta(): array
    {
        return [
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0.0',
        ];
    }
}