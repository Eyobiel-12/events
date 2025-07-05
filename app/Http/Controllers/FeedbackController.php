<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Feedback;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

final class FeedbackController extends Controller
{
    public function show(Event $event): View
    {
        // Controleer of event is afgelopen
        if ($event->end_date->isFuture()) {
            abort(404, 'Feedback is alleen beschikbaar na afloop van het event.');
        }

        $feedback = $event->feedback()
            ->approved()
            ->with(['user'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        $stats = [
            'average_rating' => $event->average_rating,
            'total_feedback' => $event->total_feedback_count,
            'rating_distribution' => $event->rating_distribution,
        ];

        return view('feedback.show', compact('event', 'feedback', 'stats'));
    }

    public function create(Event $event): View
    {
        // Controleer of event is afgelopen
        if ($event->end_date->isFuture()) {
            abort(404, 'Feedback kan alleen worden gegeven na afloop van het event.');
        }

        return view('feedback.create', compact('event'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        // Controleer of event is afgelopen
        if ($event->end_date->isFuture()) {
            return back()->withErrors(['error' => 'Feedback kan alleen worden gegeven na afloop van het event.']);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'categories' => 'nullable|array',
            'categories.*' => 'string|in:organization,venue,content,value,overall',
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
        ]);

        // Controleer of gebruiker al feedback heeft gegeven
        if (auth()->check()) {
            $existingFeedback = Feedback::where('event_id', $event->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($existingFeedback) {
                return back()->withErrors(['error' => 'Je hebt al feedback gegeven voor dit event.']);
            }
        }

        // Controleer op basis van email
        $existingFeedback = Feedback::where('event_id', $event->id)
            ->where('attendee_email', $request->attendee_email)
            ->first();

        if ($existingFeedback) {
            return back()->withErrors(['error' => 'Er is al feedback gegeven met dit email adres.']);
        }

        DB::beginTransaction();
        try {
            $feedback = Feedback::create([
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'attendee_name' => $request->attendee_name,
                'attendee_email' => $request->attendee_email,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'categories' => $request->categories,
                'submitted_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('feedback.thank-you', $event)
                ->with('success', 'Bedankt voor je feedback!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Er is een fout opgetreden. Probeer het opnieuw.']);
        }
    }

    public function thankYou(Event $event): View
    {
        return view('feedback.thank-you', compact('event'));
    }

    public function myFeedback(): View
    {
        $feedback = auth()->user()->feedback()
            ->with(['event'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('feedback.my-feedback', compact('feedback'));
    }

    public function adminIndex(): View
    {
        $feedback = Feedback::with(['event', 'user', 'reviewer'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Feedback::count(),
            'pending' => Feedback::pending()->count(),
            'approved' => Feedback::approved()->count(),
            'rejected' => Feedback::rejected()->count(),
            'average_rating' => Feedback::approved()->avg('rating') ?? 0,
        ];

        return view('feedback.admin.index', compact('feedback', 'stats'));
    }

    public function adminShow(Feedback $feedback): View
    {
        return view('feedback.admin.show', compact('feedback'));
    }

    public function adminApprove(Feedback $feedback): RedirectResponse
    {
        $feedback->approve(auth()->user());

        return back()->with('success', 'Feedback goedgekeurd.');
    }

    public function adminReject(Request $request, Feedback $feedback): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $feedback->reject(auth()->user(), $request->reason);

        return back()->with('success', 'Feedback afgewezen.');
    }
}
