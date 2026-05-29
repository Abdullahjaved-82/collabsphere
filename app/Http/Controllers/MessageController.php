<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use App\Services\MessageService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private MessageService $messageService,
    ) {
    }

    /**
     * Show inbox for authenticated user
     */
    public function inbox()
    {
        $messages = $this->messageRepository->getInboxForUser(auth()->user());
        $sent = $this->messageRepository->getSentByUser(auth()->user());
        $unreadCount = $this->messageService->getUnreadCount(auth()->user());
        
        $teamMembers = auth()->user()->teams()
            ->with('users')
            ->get()
            ->flatMap(fn($team) => $team->users)
            ->where('id', '!=', auth()->id())
            ->unique('id');

        return view('projects.inbox', compact('messages', 'sent', 'unreadCount', 'teamMembers'));
    }

    /**
     * Show sent messages
     */
    public function sent()
    {
        $messages = $this->messageRepository->getSentByUser(auth()->user());

        return view('projects.inbox', compact('messages'));
    }

    /**
     * Show compose form and handle direct message send
     */
    public function compose(Request $request)
    {
        if ($request->isMethod('get')) {
            $teamMembers = auth()->user()->teams()
                ->with('members')
                ->get()
                ->flatMap(fn($team) => $team->members)
                ->where('id', '!=', auth()->id())
                ->unique('id');

            return view('projects.inbox', compact('teamMembers'));
        }

        // POST: send direct message
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id|different:id',
            'body' => 'required|string|max:2000',
        ], [
            'receiver_id.different' => 'You cannot send a message to yourself.',
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);

        try {
            $this->messageService->sendDirect(
                auth()->user(),
                $receiver,
                $validated['body']
            );

            return redirect()->route('messages.inbox')
                ->with('success', "Message sent to {$receiver->name}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show announcement form and handle sending
     */
    public function announcement(Request $request)
    {
        if ($request->isMethod('get')) {
            $teams = auth()->user()->teams()
                ->wherePivot('role', 'leader')
                ->get();

            if ($teams->isEmpty()) {
                return redirect()->route('messages.inbox')
                    ->with('error', 'You must be a team leader to send announcements.');
            }
            
            $teamId = $request->input('team_id');

            return view('projects.announcement', compact('teams', 'teamId'));
        }

        // POST: send announcement
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'subject' => 'required|string|max:100',
            'body' => 'required|string|max:2000',
            'is_pinned' => 'nullable|boolean',
        ]);

        $teamId = (int) $validated['team_id'];

        if (!Gate::allows('sendAnnouncement', $teamId)) {
            throw new AuthorizationException();
        }

        try {
            $this->messageService->sendAnnouncement(
                auth()->user(),
                $teamId,
                $validated['subject'],
                $validated['body'],
                $validated['is_pinned'] ?? false
            );

            return redirect()->route('messages.announcements')
                ->with('success', 'Announcement sent successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show announcements for a team
     */
    public function announcements(Request $request)
    {
        $teamId = $request->input('team_id');
        $teams = auth()->user()->teams;

        if (!$teamId && $teams->isNotEmpty()) {
            $teamId = $teams->first()->id;
        }

        $messages = $teamId
            ? $this->messageRepository->getAnnouncementsForTeam($teamId)
            : collect([]);

        return view('projects.announcements', compact('messages', 'teams', 'teamId'));
    }

    /**
     * Show single message and mark as read
     */
    public function show(Message $message)
    {
        if (!Gate::allows('view-message', $message)) {
            throw new AuthorizationException();
        }

        $this->messageService->markAsRead($message, auth()->user());

        $message->load(['sender', 'receiver', 'team', 'reads.user']);

        return view('projects.message-show', compact('message'));
    }

    /**
     * Delete a message
     */
    public function destroy(Message $message)
    {
        if (!Gate::allows('delete-message', $message)) {
            throw new AuthorizationException();
        }

        $this->messageService->deleteMessage($message);

        return redirect()->back()->with('success', 'Message deleted');
    }

    /**
     * Toggle pin on announcement
     */
    public function pin(Message $message)
    {
        if (!Gate::allows('pin-message', $message)) {
            throw new AuthorizationException();
        }

        $this->messageService->pinAnnouncement($message);

        return redirect()->back()->with('success', 'Announcement pinned');
    }

    /**
     * Mark all messages as read for authenticated user
     */
    public function markAllRead()
    {
        $this->messageService->markAllAsRead(auth()->user());

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count for AJAX badge updates
     */
    public function getUnreadCount()
    {
        $count = $this->messageService->getUnreadCount(auth()->user());

        return response()->json(['unread_count' => $count]);
    }
}
