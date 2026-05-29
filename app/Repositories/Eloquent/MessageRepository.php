<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Models\User;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    /**
     * Get inbox for a user (direct messages where receiver_id = user)
     */
    public function getInboxForUser(User $user)
    {
        return Message::direct()
            ->where('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(20);
    }

    /**
     * Get messages sent by a user
     */
    public function getSentByUser(User $user)
    {
        return Message::where('sender_id', $user->id)
            ->with(['receiver', 'team'])
            ->latest()
            ->paginate(20);
    }

    /**
     * Get all announcements for a team
     */
    public function getAnnouncementsForTeam(int $teamId)
    {
        return Message::announcements()
            ->where('team_id', $teamId)
            ->with(['sender', 'reads'])
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20);
    }

    /**
     * Get conversation between two users (bidirectional)
     */
    public function getConversation(User $userA, User $userB)
    {
        return Message::direct()
            ->where(function ($query) use ($userA, $userB) {
                $query->where([
                    ['sender_id', $userA->id],
                    ['receiver_id', $userB->id],
                ])->orWhere([
                    ['sender_id', $userB->id],
                    ['receiver_id', $userA->id],
                ]);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();
    }

    /**
     * Get unread message count for a user
     */
    public function getUnreadCount(User $user): int
    {
        $directUnread = Message::direct()
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $announcementUnread = Message::announcements()
            ->whereHas('team.users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDoesntHave('reads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        return $directUnread + $announcementUnread;
    }

    /**
     * Create a new message
     */
    public function create(array $data): Message
    {
        return Message::create($data);
    }

    /**
     * Find a message by ID
     */
    public function find(int $id): ?Message
    {
        return Message::find($id);
    }

    /**
     * Update a message
     */
    public function update(Message $message, array $data): Message
    {
        $message->update($data);
        return $message;
    }

    /**
     * Delete a message (soft delete)
     */
    public function delete(Message $message): bool
    {
        return $message->delete();
    }
}
