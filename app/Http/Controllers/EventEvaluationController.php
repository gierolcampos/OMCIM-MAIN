<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventEvaluationController extends Controller
{
    /**
     * Show the evaluation form for the specified event.
     */
    public function showForm(Event $event)
    {
        // Check if evaluation is open
        if (!$event->isEvaluationOpen()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'The evaluation for this event is currently closed.');
        }

        // Check if user has already submitted an evaluation
        $existingEvaluation = $event->evaluations()->where('user_id', Auth::id())->first();
        if ($existingEvaluation) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You have already submitted an evaluation for this event.');
        }

        // Get the evaluation questions for this event
        $questions = $event->evaluationQuestions()->orderBy('display_order')->get();

        return view('events.evaluate_dynamic', compact('event', 'questions'));
    }

    /**
     * Store a newly created evaluation in storage.
     */
    public function store(Request $request, Event $event)
    {
        // Validate the request
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            // Create or update the evaluation
            $event->evaluations()->updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'rating' => $validated['rating'],
                    'feedback' => $validated['feedback'] ?? null,
                ]
            );

            return redirect()->route('events.show', $event)
                ->with('success', 'Thank you for your evaluation!');
        } catch (\Exception $e) {
            Log::error('Failed to save event evaluation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to save your evaluation. Please try again.');
        }
    }

    /**
     * Submit the detailed evaluation form.
     */
    public function submitEvaluation(Request $request, Event $event)
    {
        // Check if evaluation is open
        if (!$event->isEvaluationOpen()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'The evaluation for this event is currently closed.');
        }

        // Check if user has already submitted an evaluation
        $existingEvaluation = $event->evaluations()->where('user_id', Auth::id())->first();
        if ($existingEvaluation) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You have already submitted an evaluation for this event.');
        }

        // Get the questions for this event
        $questions = $event->evaluationQuestions()->orderBy('display_order')->get();

        // Build validation rules based on questions
        $rules = [
            'privacy_agreement' => ['required', 'in:1'],
        ];

        foreach ($questions as $question) {
            if ($question->is_required) {
                if ($question->isRatingType()) {
                    $rules["question_{$question->id}"] = ['required', 'integer', 'min:1', 'max:5'];
                } else {
                    $rules["question_{$question->id}"] = ['required', 'string', 'max:1000'];
                }
            } else {
                if ($question->isRatingType()) {
                    $rules["question_{$question->id}"] = ['nullable', 'integer', 'min:1', 'max:5'];
                } else {
                    $rules["question_{$question->id}"] = ['nullable', 'string', 'max:1000'];
                }
            }
        }

        // Validate the request
        $validated = $request->validate($rules);

        try {
            // Calculate average rating from rating questions
            $ratingSum = 0;
            $ratingCount = 0;

            // Prepare feedback text
            $feedbackText = "";

            foreach ($questions as $question) {
                $questionKey = "question_{$question->id}";
                if (isset($validated[$questionKey])) {
                    if ($question->isRatingType()) {
                        $ratingSum += (int)$validated[$questionKey];
                        $ratingCount++;
                        $feedbackText .= "{$question->question_text}: {$validated[$questionKey]}/5\n";
                    } else {
                        $feedbackText .= "{$question->question_text}:\n{$validated[$questionKey]}\n\n";
                    }
                }
            }

            // Calculate average rating
            $averageRating = $ratingCount > 0 ? round($ratingSum / $ratingCount) : 0;

            // Create a new evaluation with responses
            $evaluation = $event->evaluations()->create([
                'user_id' => Auth::id(),
                'rating' => $averageRating,
                'feedback' => $feedbackText,
            ]);

            // Save individual responses
            foreach ($questions as $question) {
                $questionKey = "question_{$question->id}";
                if (isset($validated[$questionKey])) {
                    $evaluation->responses()->create([
                        'question_id' => $question->id,
                        'rating_value' => $question->isRatingType() ? $validated[$questionKey] : null,
                        'response_text' => !$question->isRatingType() ? $validated[$questionKey] : null,
                    ]);
                }
            }

            // Redirect to a thank you page
            return redirect()->route('events.evaluation.thankyou', $event);
        } catch (\Exception $e) {
            Log::error('Failed to save event evaluation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to save your evaluation. Please try again.');
        }
    }

    /**
     * Display a thank you page after evaluation submission.
     */
    public function thankYou(Event $event)
    {
        return view('events.evaluation_thankyou', compact('event'));
    }

    /**
     * Display a view-only version of the evaluation form for admins.
     */
    public function viewForm(Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to view the evaluation form.');
        }

        // Get the evaluation questions for this event
        $questions = $event->evaluationQuestions()->orderBy('display_order')->get();

        return view('events.evaluate_view', compact('event', 'questions'));
    }

    /**
     * Display a list of all users who have submitted evaluations for an event.
     */
    public function respondents(Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to view evaluation respondents.');
        }

        // Get all evaluations with their users
        $evaluations = $event->evaluations()->with('user')->get();

        return view('events.evaluation_respondents', compact('event', 'evaluations'));
    }
}
