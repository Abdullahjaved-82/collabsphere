<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public function __construct(private MessageRepositoryInterface $messageRepository)
    {
    }

    /**
     * Send a direct message to a user in the same team
     */
    public function sendDirect(User $sender, User $receiver, string $body): Message
    {
        // Validate both users are in the same team
        $senderTeamIds = $sender->teams->pluck('id')->toArray();
        $receiverTeamIds = $receiver->teams->pluck('id')->toArray();

        if (!count(array_intersect($senderTeamIds, $receiverTeamIds))) {
            throw new \Exception('Users must be in the same team to send direct messages.');
        }

        return $this->messageRepository->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'type' => 'direct',
            'body' => $body,
        ]);
    }

    /**
     * Send an announcement to a team (team_lead only)
     */
    public function sendAnnouncement(User $sender, int $teamId, string $subject, string $body, bool $pin = false): Message
    {
        // Verify sender is team_lead in this team
        $teamMembership = $sender->teams()
            ->where('team_id', $teamId)
            ->first();

        if (!$teamMembership || $teamMembership->pivot->role !== 'leader') {
            throw new \Exception('Only team leaders can send announcements.');
        }

        $message = $this->messageRepository->create([
            'sender_id' => $sender->id,
            'team_id' => $teamId,
            'type' => 'announcement',
            'subject' => $subject,
            'body' => $body,
            'is_pinned' => $pin,
        ]);

        // Auto-mark sender as read
        $message->reads()->create([
            'user_id' => $sender->id,
        ]);

        return $message;
    }

    /**
     * Mark a direct message as read
     */
    public function markAsRead(Message $message, User $user): void
    {
        if ($message->type === 'direct' && $message->receiver_id === $user->id) {
            $message->update(['read_at' => now()]);
        } elseif ($message->type === 'announcement') {
            // Mark in message_reads for announcements
            $message->reads()->firstOrCreate([
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Mark all direct messages for a user as read
     */
    public function markAllAsRead(User $user): void
    {
        Message::direct()
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Delete a message (soft delete)
     */
    public function deleteMessage(Message $message): bool
    {
        return $this->messageRepository->delete($message);
    }

    /**
     * Toggle pin status on an announcement
     */
    public function pinAnnouncement(Message $message): Message
    {
        return $this->messageRepository->update($message, [
            'is_pinned' => !$message->is_pinned,
        ]);
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return $this->messageRepository->getUnreadCount($user);
    }
}
