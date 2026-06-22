<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Venue\CreateSlotRequest;
use App\Models\Venue;
use App\Models\VenueSlot;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminSlotController extends Controller
{
    public function index($venueId): View
    {
        $venue = Venue::findOrFail($venueId);

        return view('admin.slots.index', [
            'venue' => $venue,
            'slots' => $venue->slots()->orderBy('day_of_week')->orderBy('start_time')->get(),
        ]);
    }

    public function store($venueId, CreateSlotRequest $request): RedirectResponse
    {
        $venue = Venue::findOrFail($venueId);
        $venue->slots()->create($request->validated());

        return redirect()->route('admin.slots.index', $venue);
    }

    public function update($venueId, $slotId, Request $request): RedirectResponse
    {
        $venue = Venue::findOrFail($venueId);
        $slot = VenueSlot::where('venue_id', $venue->id)->where('id', $slotId)->firstOrFail();
        $slot->fill($request->validate([
            'day_of_week' => ['sometimes', 'integer', 'between:0,6'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'price_normal' => ['sometimes', 'numeric', 'min:0'],
            'price_campus' => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]));
        $slot->save();

        return redirect()->route('admin.slots.index', $venue);
    }

    public function destroy($venueId, $slotId): RedirectResponse
    {
        $venue = Venue::findOrFail($venueId);
        $slot = VenueSlot::where('venue_id', $venue->id)->where('id', $slotId)->firstOrFail();
        $slot->delete();

        return redirect()->route('admin.slots.index', $venue);
    }

    public function bulkCreate($venueId, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'array'],
            'day_of_week.*' => ['integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'interval_minutes' => ['required', 'integer', 'min:1'],
            'price_normal' => ['required', 'numeric', 'min:0'],
            'price_campus' => ['required', 'numeric', 'min:0'],
        ]);

        $venue = Venue::findOrFail($venueId);
        $slots = [];
        $current = strtotime($data['start_time']);
        $end = strtotime($data['end_time']);

        while ($current < $end) {
            $next = $current + ($data['interval_minutes'] * 60);
            if ($next > $end) {
                break;
            }

            foreach ($data['day_of_week'] as $dayOfWeek) {
                $slots[] = [
                    'day_of_week' => $dayOfWeek,
                    'start_time' => date('H:i', $current),
                    'end_time' => date('H:i', $next),
                    'price_normal' => $data['price_normal'],
                    'price_campus' => $data['price_campus'],
                    'is_active' => true,
                ];
            }

            $current = $next;
        }

        $venue->slots()->createMany($slots);

        return redirect()->route('admin.slots.index', $venue);
    }
}
