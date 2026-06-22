<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\VenueClosure;
use App\Services\SlotAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminVenueController extends Controller
{
    public function __construct(private SlotAvailabilityService $availabilityService)
    {
    }

    public function index(): View
    {
        return view('admin.venues.index');
    }

    public function create(): View
    {
        return view('admin.venues.create');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('admin.venues.index');
    }

    public function edit(Venue $venue): View
    {
        return view('admin.venues.edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        return redirect()->route('admin.venues.index');
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        return redirect()->route('admin.venues.index');
    }

    public function uploadPhoto(Request $request, Venue $venue): RedirectResponse
    {
        return redirect()->route('admin.venues.edit', $venue);
    }

    public function toggleActive(Venue $venue): RedirectResponse
    {
        return redirect()->route('admin.venues.index');
    }

    public function closeTemporary(Request $request, Venue $venue): RedirectResponse
    {
        $attributes = $request->validate([
            'closed_date' => ['required', 'date'],
            'reason' => ['nullable', 'string'],
        ]);

        VenueClosure::create([
            'venue_id' => $venue->id,
            'closed_date' => $attributes['closed_date'],
            'reason' => $attributes['reason'] ?? null,
            'closed_by' => auth()->id(),
        ]);

        $this->availabilityService->invalidateCache($venue->id, $attributes['closed_date']);

        return redirect()->route('admin.venues.edit', $venue);
    }

    public function reopenVenue(Request $request, Venue $venue): RedirectResponse
    {
        $attributes = $request->validate([
            'date' => ['required', 'date'],
        ]);

        VenueClosure::where('venue_id', $venue->id)
            ->where('closed_date', $attributes['date'])
            ->delete();

        $this->availabilityService->invalidateCache($venue->id, $attributes['date']);

        return redirect()->route('admin.venues.edit', $venue);
    }
}
