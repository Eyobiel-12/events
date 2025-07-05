<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::query()
            ->where('status', 'published')
            ->where('start_date', '>=', now())
            ->with(['organisation', 'ticketTypes' => function ($query) {
                $query->where('is_active', true)
                    ->where('sale_start_date', '<=', now())
                    ->where('sale_end_date', '>=', now());
            }]);

        // Zoek functionaliteit
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        // Filter op stad
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Filter op land
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $events = $query->orderBy('start_date')->paginate(12);

        // Unieke steden en landen voor filters
        $cities = Event::where('status', 'published')
            ->distinct()
            ->pluck('city')
            ->filter()
            ->sort()
            ->values();

        $countries = Event::where('status', 'published')
            ->distinct()
            ->pluck('country')
            ->filter()
            ->sort()
            ->values();

        return view('events.index', compact('events', 'cities', 'countries'));
    }

    public function show(Event $event): View
    {
        // Alleen gepubliceerde events zijn toegankelijk
        if ($event->status !== 'published') {
            abort(404);
        }

        // Laad tickettypes die actief zijn en beschikbaar
        $event->load(['ticketTypes' => function ($query) {
            $query->where('is_active', true)
                ->where('sale_start_date', '<=', now())
                ->where('sale_end_date', '>=', now())
                ->whereRaw('quantity > sold_quantity'); // Alleen als er nog tickets beschikbaar zijn
        }, 'organisation']);

        return view('events.show', compact('event'));
    }
} 