<?php

namespace App\Services;

use App\Models\Venue;
use App\Models\VenueClosure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VenueService
{
    public function getAllVenues(array $filters): LengthAwarePaginator
    {
        $query = Venue::query()->with('manager')->withCount('reviews');

        if (! empty($filters['sport_type'])) {
            $query->bySportType($filters['sport_type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 15);
    }

    public function getVenueDetail(int $id): Venue
    {
        return Venue::with([
            'photos' => fn ($query) => $query->orderBy('sort_order'),
            'slots' => fn ($query) => $query->where('is_active', true),
            'reviews' => fn ($query) => $query->orderByDesc('created_at')->limit(10),
            'manager',
        ])->findOrFail($id);
    }

    public function createVenue(array $data): Venue
    {
        $venue = DB::transaction(function () use ($data) {
            return Venue::create($data);
        });

        return $venue;
    }

    public function updateVenue(Venue $venue, array $data): Venue
    {
        $venue->fill($data);
        $venue->save();

        return $venue;
    }

    public function deleteVenue(Venue $venue): bool
    {
        return $venue->delete();
    }

    public function uploadPhotos(Venue $venue, array $files): void
    {
        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->storeAs('venues/'.$venue->id, uniqid('photo_').'.'.$file->extension(), 'public');

            $venue->photos()->create([
                'photo_path' => $path,
                'sort_order' => $index + 1,
            ]);
        }
    }

    public function toggleActive(Venue $venue): Venue
    {
        $venue->is_active = ! $venue->is_active;
        $venue->save();

        return $venue;
    }

    public function closeTemporary(Venue $venue, string $date, ?string $reason): VenueClosure
    {
        return VenueClosure::create([
            'venue_id' => $venue->id,
            'closed_date' => $date,
            'reason' => $reason,
            'closed_by' => auth()->id(),
        ]);
    }
}
