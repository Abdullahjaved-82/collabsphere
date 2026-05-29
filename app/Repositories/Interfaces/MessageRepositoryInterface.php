<?php

namespace App\Repositories\Interfaces;

use App\Models\Message;
use App\Models\User;

interface MessageRepositoryInterface
{
    /**
     * Get inbox for a user (direct messages where receiver_id = user)
     */
    public function getInboxForUser(User $user);

    /**
     * Get messages sent by a user
     */
    public function getSentByUser(User $user);

    /**
     * Get all announcements for a team
     */
    public function getAnnouncementsForTeam(int $teamId);

    /**
     * Get conversation between two users (bidirectional)
     */
    public function getConversation(User $userA, User $userB);

    /**
     * Get unread message count for a user
     */
    public function getUnreadCount(User $user): int;

    /**
     * Create a new message
     */
    public function create(array $data): Message;

    /**
     * Find a message by ID
     */
    public function find(int $id): ?Message;

    /**
     * Update a message
     */
    public function update(Message $message, array $data): Message;

    /**
     * Delete a message
     */
    public function delete(Message $message): bool;
}
