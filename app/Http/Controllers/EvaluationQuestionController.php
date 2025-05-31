<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EvaluationQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EvaluationQuestionController extends Controller
{
    /**
     * Display the evaluation questions for an event.
     */
    public function index(Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to manage evaluation questions.');
        }
        
        $questions = $event->evaluationQuestions()->orderBy('display_order')->get();
        
        return view('events.questions.index', compact('event', 'questions'));
    }
    
    /**
     * Show the form for creating a new evaluation question.
     */
    public function create(Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to create evaluation questions.');
        }
        
        return view('events.questions.create', compact('event'));
    }
    
    /**
     * Store a newly created evaluation question in storage.
     */
    public function store(Request $request, Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to create evaluation questions.');
        }
        
        // Validate the request
        $validated = $request->validate([
            'question_text' => ['required', 'string', 'max:255'],
            'question_type' => ['required', 'in:rating,text'],
            'is_required' => ['boolean'],
        ]);
        
        // Get the highest display order
        $maxOrder = $event->evaluationQuestions()->max('display_order') ?? 0;
        
        try {
            // Create the question
            $question = new EvaluationQuestion([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'is_required' => $request->has('is_required'),
                'display_order' => $maxOrder + 1,
            ]);
            
            $event->evaluationQuestions()->save($question);
            
            return redirect()->route('events.questions.index', $event)
                ->with('success', 'Evaluation question created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create evaluation question: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create evaluation question. Please try again.');
        }
    }
    
    /**
     * Show the form for editing the specified evaluation question.
     */
    public function edit(Event $event, EvaluationQuestion $question)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to edit evaluation questions.');
        }
        
        // Check if the question belongs to the event
        if ($question->event_id !== $event->id) {
            return redirect()->route('events.questions.index', $event)
                ->with('error', 'The question does not belong to this event.');
        }
        
        return view('events.questions.edit', compact('event', 'question'));
    }
    
    /**
     * Update the specified evaluation question in storage.
     */
    public function update(Request $request, Event $event, EvaluationQuestion $question)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to update evaluation questions.');
        }
        
        // Check if the question belongs to the event
        if ($question->event_id !== $event->id) {
            return redirect()->route('events.questions.index', $event)
                ->with('error', 'The question does not belong to this event.');
        }
        
        // Validate the request
        $validated = $request->validate([
            'question_text' => ['required', 'string', 'max:255'],
            'question_type' => ['required', 'in:rating,text'],
            'is_required' => ['boolean'],
        ]);
        
        try {
            // Update the question
            $question->question_text = $validated['question_text'];
            $question->question_type = $validated['question_type'];
            $question->is_required = $request->has('is_required');
            $question->save();
            
            return redirect()->route('events.questions.index', $event)
                ->with('success', 'Evaluation question updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update evaluation question: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update evaluation question. Please try again.');
        }
    }
    
    /**
     * Remove the specified evaluation question from storage.
     */
    public function destroy(Event $event, EvaluationQuestion $question)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to delete evaluation questions.');
        }
        
        // Check if the question belongs to the event
        if ($question->event_id !== $event->id) {
            return redirect()->route('events.questions.index', $event)
                ->with('error', 'The question does not belong to this event.');
        }
        
        try {
            $question->delete();
            
            // Reorder the remaining questions
            $remainingQuestions = $event->evaluationQuestions()->orderBy('display_order')->get();
            foreach ($remainingQuestions as $index => $q) {
                $q->display_order = $index + 1;
                $q->save();
            }
            
            return redirect()->route('events.questions.index', $event)
                ->with('success', 'Evaluation question deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete evaluation question: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete evaluation question. Please try again.');
        }
    }
    
    /**
     * Reorder the evaluation questions.
     */
    public function reorder(Request $request, Event $event)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to reorder evaluation questions.');
        }
        
        // Validate the request
        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*' => ['required', 'integer', 'exists:evaluation_questions,id'],
        ]);
        
        try {
            // Update the order of the questions
            foreach ($validated['questions'] as $index => $questionId) {
                $question = EvaluationQuestion::find($questionId);
                if ($question && $question->event_id === $event->id) {
                    $question->display_order = $index + 1;
                    $question->save();
                }
            }
            
            return redirect()->route('events.questions.index', $event)
                ->with('success', 'Evaluation questions reordered successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to reorder evaluation questions: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to reorder evaluation questions. Please try again.');
        }
    }
}
