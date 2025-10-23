<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conference;
use App\Models\User;
use App\Models\Paper;
use App\Models\Notification;
use App\Notifications\ConferenceReminderNotification;
use Carbon\Carbon;

class ConferenceController extends Controller
{
    // Redirect to dashboard
    public function index()
    {
        return redirect()->route('dashboard');
    }

    // Show form to create a new conference
    public function create()
    {
        $reviewers = User::where('role', 'Reviewer')->get();
        return view('admin.conferences.create', compact('reviewers'));
    }

    // Store a new conference and notify assigned reviewers & authors
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date_format:Y-m-d\TH:i',
            'conference_code' => 'nullable|string|max:50',
            'reviewers' => 'nullable|array',
            'reviewers.*' => 'exists:users,id',
        ]);

        // Generate unique conference code if not provided
        $conference_code = $request->conference_code ?: strtoupper('CONF-' . rand(1000, 9999));
        while (Conference::where('conference_code', $conference_code)->exists()) {
            $conference_code = strtoupper('CONF-' . rand(1000, 9999));
        }

        $conference = Conference::create([
            'conference_code' => $conference_code,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline ? Carbon::parse($request->deadline) : null,
        ]);

        // Notify assigned reviewers
        if ($request->filled('reviewers')) {
            $conference->reviewers()->sync($request->reviewers);
            foreach ($request->reviewers as $reviewerId) {
                Notification::create([
                    'user_id' => null,
                    'recipient_id' => $reviewerId,
                    'type' => 'new_conference',
                    'notifiable_id' => $conference->id,
                    'data' => json_encode([
                        'title' => 'New Conference Assigned',
                        'message' => "You have been assigned to review '{$conference->title}' (Code: {$conference->conference_code}).",
                        'link' => route('reviewer.conference.papers', $conference->conference_code),
                    ]),
                    'read_at' => null,
                ]);
            }
        }

        // Notify all authors
        $authors = User::where('role', 'Author')->get();
        foreach ($authors as $author) {
            Notification::create([
                'user_id' => null,
                'recipient_id' => $author->id,
                'type' => 'conference_created',
                'notifiable_id' => $conference->id,
                'data' => json_encode([
                    'title' => 'New Conference Available',
                    'message' => "A new conference '{$conference->title}' has been created. Submit your papers before the deadline: {$conference->deadline}.",
                    'link' => route('paper.submit', $conference->conference_code),
                ]),
                'read_at' => null,
            ]);
        }

        return redirect()->route('dashboard')->with('success', '✅ Conference added successfully and authors notified.');
    }

    // Show form to edit conference (title, description, deadline)
// Show form to edit all conference details
// Show edit form for title, description, deadline
public function edit($conference_code)
{
    $conference = Conference::where('conference_code', $conference_code)->firstOrFail();
    return view('admin.conferences.edit-date', compact('conference')); // make sure your Blade is in admin.conferences
}

// Update conference
public function update(Request $request, $conference_code)
{
    $conference = Conference::where('conference_code', $conference_code)->firstOrFail();

    $request->validate([
        'title' => 'required|string|max:150',
        'description' => 'required|string',
        'deadline' => 'required|date',
    ]);

    $conference->update([
        'title' => $request->title,
        'description' => $request->description,
        'deadline' => $request->deadline,
    ]);

    return redirect()->route('dashboard')->with('success', '✅ Conference updated successfully.');
}

    // Show form to assign reviewers
    public function editReviewers($conference_code)
    {
        $conference = Conference::where('conference_code', $conference_code)->firstOrFail();
        $reviewers = User::where('role', 'Reviewer')->get();
        $assignedReviewerIds = $conference->reviewers()->pluck('reviewer_id')->toArray();

        return view('admin.conferences.edit-reviewer', compact(
            'conference',
            'reviewers',
            'assignedReviewerIds'
        ));
    }

    // Update reviewers and notify newly assigned reviewers
    // Update reviewers and notify newly assigned & removed reviewers
public function updateReviewers(Request $request, $conference_code)
{
    $request->validate([
        'reviewers' => 'required|array',
        'reviewers.*' => 'exists:users,id',
    ]);

    $conference = Conference::where('conference_code', $conference_code)->firstOrFail();

    $currentReviewerIds = $conference->reviewers()->pluck('reviewer_id')->toArray();
    $newReviewerIds = $request->reviewers;

    // Sync new reviewers
    $conference->reviewers()->sync($newReviewerIds);

    // 1️⃣ Notify newly assigned reviewers
    $addedReviewerIds = array_diff($newReviewerIds, $currentReviewerIds);
    foreach ($addedReviewerIds as $reviewerId) {
        Notification::create([
            'user_id' => null,
            'recipient_id' => $reviewerId,
            'type' => 'assigned_reviewer',
            'notifiable_id' => $conference->id,
            'data' => json_encode([
                'title' => 'You have been assigned to a conference',
                'message' => "You are now assigned to review '{$conference->title}' (Code: {$conference->conference_code}).",
                'link' => route('reviewer.conference.papers', $conference->conference_code),
            ]),
            'read_at' => null,
        ]);
    }

    // 2️⃣ Notify reviewers who were removed
    $removedReviewerIds = array_diff($currentReviewerIds, $newReviewerIds);
    foreach ($removedReviewerIds as $reviewerId) {
        Notification::create([
            'user_id' => null,
            'recipient_id' => $reviewerId,
            'type' => 'removed_reviewer',
            'notifiable_id' => $conference->id,
            'data' => json_encode([
                'title' => 'You have been unassigned from a conference',
                'message' => "You are no longer assigned to review '{$conference->title}' (Code: {$conference->conference_code}).",
                'link' => route('reviewer.conference.papers', $conference->conference_code),
            ]),
            'read_at' => null,
        ]);
    }

    return redirect()->route('dashboard')->with('success', '✅ Reviewers updated successfully and notifications sent.');
}


    // Show submitted papers for a conference
    public function showPapers($conference_code)
    {
        $conference = Conference::where('conference_code', $conference_code)->firstOrFail();
        $papers = Paper::where('conference_code', $conference_code)->get();

        return view('admin.conferences.papers', compact('conference', 'papers'));
    }

    // Send reminder to authors
    public function sendReminder($conference_code)
    {
        $conference = Conference::where('conference_code', $conference_code)->firstOrFail();
        $authors = User::where('role', 'Author')->get();

        foreach ($authors as $author) {
            $author->notify(new ConferenceReminderNotification($conference));
        }

        return back()->with('success', '✅ Reminders sent to authors.');
    }
}
