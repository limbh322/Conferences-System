<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paper;
use App\Models\Conference;
use App\Models\Review;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ReviewerController extends Controller
{
    /** Reviewer Dashboard */
    public function index()
    {
        $reviewerId = Auth::id();

        $assignedConferences = Conference::whereHas('reviewers', fn($q) => $q->where('reviewer_id', $reviewerId))
            ->withCount([
                'papers as total_papers',
                'papers as reviewed_papers' => fn($q) => $q->whereHas('reviews', fn($r) => $r->where('reviewer_id', $reviewerId))
            ])
            ->get();

        $notifications = Notification::where('recipient_id', $reviewerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reviewer.home', compact('assignedConferences', 'notifications'));
    }

    /** Show all papers for a conference */
    public function showConferencePapers(string $conference_code)
    {
        $conference = Conference::where('conference_code', $conference_code)->firstOrFail();
        $reviewerId = Auth::id();

        if (!$conference->reviewers()->where('reviewer_id', $reviewerId)->exists()) {
            abort(403, 'You are not authorized to view papers for this conference.');
        }

        $papers = Paper::with(['author', 'reviews' => fn($q) => $q->where('reviewer_id', $reviewerId)])
            ->where('conference_code', $conference_code)
            ->get();

        $pendingPapers = $papers->filter(fn($p) => $p->reviews->isEmpty());
        $donePapers = $papers->filter(fn($p) => $p->reviews->isNotEmpty());

        return view('reviewer.papers', compact('conference', 'pendingPapers', 'donePapers'));
    }

    /** Show review form */
    public function reviewForm($paper_id)
    {
        $paper = Paper::with('conference')->findOrFail($paper_id); // $paper->id exists
        $review = Review::where('paper_id', $paper->paper_id)->where('reviewer_id', Auth::id())->first();

        return view('reviewer.review_form', compact('paper', 'review'));
    }


    /** Submit a review */
    public function submitReview(Request $request, int $paper_id)
    {
        $paper = Paper::with(['conference', 'author'])->findOrFail($paper_id);
        $this->authorizeReviewer($paper);

        $validated = $request->validate([
            'status' => 'required|string|in:Submitted,Approved,Rejected',
            'score' => 'nullable|numeric|min:0|max:10',
            'recommendation' => 'nullable|string|max:500',
            'comments' => 'nullable|string|max:1000',
        ]);

        // Save review
        $review = Review::updateOrCreate(
            [
                'paper_id' => $paper->paper_id,
                'reviewer_id' => Auth::id(),
            ],
            [
                'status' => $validated['status'],
                'score' => $validated['score'] ?? null,
                'recommendation' => $validated['recommendation'] ?? null,
                'comments' => $validated['comments'] ?? null,
                'conference_id' => $paper->conference->id,
            ]
        );

        // Notify author
        Notification::create([
            'user_id' => Auth::id(),
            'recipient_id' => $paper->author_id,
            'type' => 'review_submitted',
            'notifiable_id' => $paper->id,
            'data' => json_encode([
                'title' => 'Your paper has been reviewed',
                'message' => "Your paper '{$paper->title}' has been reviewed ({$validated['status']}).",
                'link' => route('paper.show', $paper->paper_id),
            ]),
            'read_at' => null,
        ]);

        return redirect()
            ->route('reviewer.conference.papers', ['conference_code' => $paper->conference_code])
            ->with('success', '✅ Review submitted successfully and author notified!');
    }

    /** Ensure reviewer is assigned to paper's conference */
    private function authorizeReviewer(Paper $paper): void
    {
        if (!$paper->conference->reviewers()->where('reviewer_id', Auth::id())->exists()) {
            abort(403, 'You are not authorized to review this paper.');
        }
    }

    /** Notify reviewer for new conference assignment */
    public function notifyNewConference(int $reviewerId, Conference $conference)
    {
        Notification::create([
            'user_id' => Auth::id(),
            'recipient_id' => $reviewerId,
            'type' => 'new_conference',
            'notifiable_id' => $conference->id,
            'data' => json_encode([
                'title' => 'New conference assigned',
                'message' => "You have been assigned to review '{$conference->title}' (Code: {$conference->conference_code}).",
                'link' => route('reviewer.conference.papers', $conference->conference_code),
            ]),
            'read_at' => null,
        ]);
    }

    /** Send reminders for upcoming deadlines */
    public function sendDeadlineReminders()
    {
        $today = now();
        $upcomingConferences = Conference::whereDate('deadline', '>=', $today)
            ->whereDate('deadline', '<=', $today->copy()->addDays(3))
            ->with('reviewers')
            ->get();

        foreach ($upcomingConferences as $conference) {
            foreach ($conference->reviewers as $reviewer) {
                Notification::create([
                    'user_id' => null,
                    'recipient_id' => $reviewer->id,
                    'type' => 'deadline_reminder',
                    'notifiable_id' => $conference->id,
                    'data' => json_encode([
                        'title' => 'Upcoming deadline',
                        'message' => "Reminder: The conference '{$conference->title}' review deadline is on {$conference->deadline->format('Y-m-d H:i')}.",
                        'link' => route('reviewer.conference.papers', $conference->conference_code),
                    ]),
                    'read_at' => null,
                ]);
            }
        }

        return 'Reminders sent!';
    }
}
