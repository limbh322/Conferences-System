<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paper;
use App\Models\Conference;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PaperController extends Controller
{
    // ===================== SUBMIT PAPER ===================== //
    public function create($conference_code)
    {
        $conference = Conference::where('conference_code', $conference_code)->firstOrFail();
        $reviewers = User::where('role', 'Reviewer')->get();

        return view('paper.submit', compact('conference', 'reviewers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string|max:255',
            'file_path' => 'required|file|mimes:pdf|max:2048',
            'conference_code' => 'required|exists:conferences,conference_code',
            'reviewers' => 'nullable|array',
            'reviewers.*' => 'exists:users,id',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->withErrors('You must be logged in to submit a paper.');
        }

        $filePath = $request->file('file_path')->store('papers', 'public');

        $paper = Paper::create([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
            'file_path' => $filePath,
            'author_id' => Auth::id(),
            'conference_code' => $validated['conference_code'],
            'status' => 'Submitted',
        ]);

        $paper->load('conference');

        // ---------------- Notify admins manually ----------------
        $admins = User::where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            DB::table('notifications')->insert([
                'user_id' => $admin->id,
                'type' => 'PaperSubmittedNotification',
                'data' => json_encode([
                    'title' => 'New Paper Submitted',
                    'message' => 'A new paper "' . $paper->title . '" has been submitted for conference "' . $paper->conference->title . '".',
                    'conference_code' => $paper->conference->conference_code,
                    'paper_id' => $paper->id,
                    'link' => route('home')
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ---------------- Optional reviewer assignment ----------------
        if (!empty($validated['reviewers'])) {
            foreach ($validated['reviewers'] as $reviewer_id) {
                Assignment::create([
                    'paper_id' => $paper->paper_id,
                    'reviewer_id' => $reviewer_id,
                ]);

                DB::table('notifications')->insert([
                    'user_id' => $reviewer_id,
                    'type' => 'ReviewerAssigned',
                    'data' => json_encode([
                        'title' => 'You have been assigned a paper',
                        'message' => 'Paper "' . $paper->title . '" has been assigned to you for review.',
                        'conference_code' => $paper->conference->conference_code,
                        'paper_id' => $paper->id,
                        'link' => route('reviewer.home')
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $paper->update(['status' => 'Under Review']);
        }

        return redirect()->route('home')->with('success', '✅ Paper submitted successfully!');
    }

    // ===================== VIEW PAPER ===================== //
    public function show($id)
    {
        $paper = Paper::with(['author', 'conference', 'assignedReviewers', 'reviews.reviewer'])->findOrFail($id);

        if (Auth::id() !== $paper->author_id && !in_array(Auth::user()->role, ['Reviewer', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        return view('paper.show', compact('paper'));
    }

    public function viewFile($id)
    {
        $paper = Paper::findOrFail($id);
        $path = storage_path('app/public/' . $paper->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->file($path);
    }

    // ===================== EDIT PAPER ===================== //
    public function edit($paper_id)
    {
        $paper = Paper::findOrFail($paper_id);

        if (Auth::id() !== $paper->author_id) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($paper->status, ['Submitted', 'Rejected', 'Revision Needed', 'Resubmitted'])) {
            return redirect()->route('home')->withErrors('❌ This paper cannot be edited at its current stage.');
        }

        return view('paper.edit', compact('paper'));
    }

    public function update(Request $request, $paper_id)
    {
        $paper = Paper::findOrFail($paper_id);

        if (Auth::id() !== $paper->author_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string|max:255',
            'file_path' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('file_path')) {
            if ($paper->file_path && Storage::disk('public')->exists($paper->file_path)) {
                Storage::disk('public')->delete($paper->file_path);
            }
            $paper->file_path = $request->file('file_path')->store('papers', 'public');
        }

        $paper->update([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'] ?? $paper->abstract,
            'keywords' => $validated['keywords'] ?? $paper->keywords,
            'status' => in_array($paper->status, ['Rejected', 'Revision Needed']) ? 'Resubmitted' : $paper->status,
        ]);

        return redirect()->route('home')->with('success', '✅ Paper updated successfully.');
    }

    // ===================== REVIEWER ASSIGNMENT ===================== //
    public function assignReviewer(Request $request, Paper $paper)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        Assignment::updateOrCreate(
            ['paper_id' => $paper->paper_id, 'reviewer_id' => $request->reviewer_id],
            []
        );

        $paper->update(['status' => 'Under Review']);

        if ($paper->conference_code) {
            DB::table('conference_reviewer')->updateOrInsert(
                ['conference_code' => $paper->conference_code, 'reviewer_id' => $request->reviewer_id],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }

        DB::table('notifications')->insert([
            'user_id' => $request->reviewer_id,
            'type' => 'AdminAssignReviewerNotification',
            'data' => json_encode([
                'title' => 'You have been assigned a paper',
                'message' => 'Paper "' . $paper->title . '" has been assigned to you for review.',
                'conference_code' => $paper->conference_code,
                'paper_id' => $paper->paper_id,
                'link' => route('reviewer.home')
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', '✅ Reviewer assigned and notified successfully!');
    }

    // ===================== REVIEW SUBMISSION ===================== //
    public function submitReview(Request $request, $paper_id)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:10',
            'comments' => 'nullable|string',
            'recommendation' => 'required|in:Accept,Reject,Revise',
        ]);

        $paper = Paper::findOrFail($paper_id);
        $userId = Auth::id();

        if (!Assignment::where('paper_id', $paper_id)->where('reviewer_id', $userId)->exists()) {
            return back()->withErrors('❌ You are not assigned to review this paper.');
        }

        Review::updateOrCreate(
            ['paper_id' => $paper_id, 'reviewer_id' => $userId],
            [
                'score' => $validated['score'],
                'comments' => $validated['comments'] ?? null,
                'recommendation' => $validated['recommendation'],
            ]
        );

        $statusMap = [
            'Accept' => 'Approved',
            'Reject' => 'Rejected',
            'Revise' => 'Revision Needed',
        ];
        $paper->update(['status' => $statusMap[$validated['recommendation']]]);

        // ---------------- Notify author manually ----------------
        $author = $paper->author;
        if ($author) {
            DB::table('notifications')->insert([
                'user_id' => $author->id,
                'type' => 'ReviewerSubmitReviewNotification',
                'data' => json_encode([
                    'title' => 'Your paper has been reviewed',
                    'message' => 'Your paper "' . $paper->title . '" has a new review.',
                    'conference_code' => $paper->conference_code,
                    'paper_id' => $paper->paper_id,
                    'link' => route('home')
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('reviewer.home')->with('success', '✅ Review submitted and author notified!');
    }

    // ===================== REVIEWER DASHBOARD ===================== //
    public function reviewerHome()
    {
        $userId = Auth::id();

        $assignedPapers = Assignment::with('paper.conference')
            ->where('reviewer_id', $userId)
            ->get()
            ->pluck('paper');

        $unassignedPapers = Paper::whereDoesntHave('assignments', function ($query) use ($userId) {
                $query->where('reviewer_id', $userId);
            })
            ->with('conference')
            ->get();

        return view('reviewer.home', compact('assignedPapers', 'unassignedPapers'));
    }

    // ===================== AUTHOR DASHBOARD ===================== //
    public function mySubmissions()
    {
        $userId = auth()->id();
        $papers = Paper::with(['conference', 'reviews.reviewer'])
                       ->where('author_id', $userId)
                       ->get();
        $conferences = Conference::all();

        return view('home', compact('papers', 'conferences'));
    }
}
