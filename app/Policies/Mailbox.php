<?php

namespace App\Policies;

use App\Models\Mailbox as MailboxModel;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class Mailbox
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function edit(User $user, MailboxModel $mailbox)
    {
        return $user->id == $mailbox->user_id;
    }

    public function destroy(User $user, MailboxModel $mailbox)
    {
        return $user->id == $mailbox->user_id;
    }
}
