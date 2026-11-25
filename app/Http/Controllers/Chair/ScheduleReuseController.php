<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Services\ScheduleReuseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleReuseController extends Controller
{
    public function __construct(private ScheduleReuseService $reuse)
    {
    }

    public function previewPbs(Request $request): JsonResponse
    {
        return $this->preview($request, 'pbs', 'section_id');
    }

    public function previewPbt(Request $request): JsonResponse
    {
        return $this->preview($request, 'pbt', 'faculty_id');
    }

    public function applyPbs(Request $request): JsonResponse
    {
        return $this->apply($request, 'pbs', 'section_id');
    }

    public function applyPbt(Request $request): JsonResponse
    {
        return $this->apply($request, 'pbt', 'faculty_id');
    }

    private function preview(Request $request, string $scope, string $targetField): JsonResponse
    {
        $data = $request->validate($this->rules($targetField));

        return response()->json($this->reuse->preview(
            $scope,
            $data[$targetField],
            $data['destination_semester_id'],
            $data['source_semester_id'],
            $data['mappings'] ?? [],
            $data['excluded'] ?? []
        ));
    }

    private function apply(Request $request, string $scope, string $targetField): JsonResponse
    {
        $data = $request->validate($this->rules($targetField) + [
            'workload_override_reason' => 'nullable|string|max:1000',
        ]);
        $result = $this->reuse->apply(
            $scope,
            $data[$targetField],
            $data['destination_semester_id'],
            $data['source_semester_id'],
            $data['mappings'] ?? [],
            $data['excluded'] ?? [],
            $data['workload_override_reason'] ?? null
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    private function rules(string $targetField): array
    {
        return [
            $targetField => 'required|uuid',
            'destination_semester_id' => 'required|uuid|exists:semester,sem_id',
            'source_semester_id' => 'required|uuid|exists:semester,sem_id',
            'mappings' => 'sometimes|array',
            'mappings.sections' => 'sometimes|array',
            'mappings.sections.*' => 'nullable|uuid',
            'mappings.courses' => 'sometimes|array',
            'mappings.courses.*' => 'nullable|uuid',
            'mappings.rooms' => 'sometimes|array',
            'mappings.rooms.*' => 'nullable|uuid',
            'excluded' => 'sometimes|array',
            'excluded.*' => 'string|max:120',
        ];
    }
}
