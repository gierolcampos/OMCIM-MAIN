<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view events
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Event $event): bool
    {
        return true; // Everyone can view a specific event
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->canManageEvents(); // Users with event management permission can create events
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->canManageEvents(); // Users with event management permission can update events
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->canManageEvents(); // Users with event management permission can delete events
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): bool
    {
        return $user->canManageEvents(); // Users with event management permission can restore events
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return $user->canManageEvents(); // Users with event management permission can force delete events
    }

    /**
     * Determine whether the user can view attendees list.
     */
    public function viewAttendees(User $user, Event $event): bool
    {
        return $user->canManageEvents() || $event->created_by === $user->id;
    }
}
