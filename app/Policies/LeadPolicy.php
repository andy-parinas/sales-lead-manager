<?php

namespace App\Policies;

use App\Lead;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any leads.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isHeadOffice();
    }

    /**
     * Determine whether the user can view the lead.
     *
     * @param  \App\User  $user
     * @param  \App\Lead  $lead
     * @return mixed
     */
    public function view(User $user, Lead $lead)
    {
        return $user->isHeadOffice();
    }

    /**
     * Determine whether the user can create leads.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the lead.
     *
     * @param  \App\User  $user
     * @param  \App\Lead  $lead
     * @return mixed
     */
    public function update(User $user, Lead $lead)
    {
        //
    }

    /**
     * Determine whether the user can delete the lead.
     *
     * @param  \App\User  $user
     * @param  \App\Lead  $lead
     * @return mixed
     */
    public function delete(User $user, Lead $lead)
    {
        return $user->isHeadOffice();
    }

    /**
     * Determine whether the user can restore the lead.
     *
     * @param  \App\User  $user
     * @param  \App\Lead  $lead
     * @return mixed
     */
    public function restore(User $user, Lead $lead)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the lead.
     *
     * @param  \App\User  $user
     * @param  \App\Lead  $lead
     * @return mixed
     */
    public function forceDelete(User $user, Lead $lead)
    {
        //
    }
}
