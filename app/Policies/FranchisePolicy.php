<?php

namespace App\Policies;

use App\Franchise;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FranchisePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any franchises.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isHeadOffice();
    }

    /**
     * Determine whether the user can view the franchise.
     *
     * @param  \App\User  $user
     * @param  \App\Franchise  $franchise
     * @return mixed
     */
    public function view(User $user, Franchise $franchise)
    {
        return $user->franchises->contains('id', $franchise->id) || $user->isHeadOffice();
    }

    /**
     * Determine whether the user can create franchises.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isHeadOffice();
    }

    /**
     * Determine wether the user can create a lead inside the Franchise
     */
    public function createLead(User $user, Franchise $franchise)
    {
        return $user->franchises->contains('id', $franchise->id) || $user->isHeadOffice();
        
    }


    public function updateLead(User $user, Franchise $franchise)
    {
        return $user->franchises->contains('id', $franchise->id) || $user->isHeadOffice();

    }

    public function changeLeadFranchise(User $user, Franchise $franchise)
    {
        return $user->isFranchiseAdmin() || $user->isHeadOffice();
    }

    /**
     * Determine whether the user can update the franchise.
     *
     * @param  \App\User  $user
     * @param  \App\Franchise  $franchise
     * @return mixed
     */
    public function update(User $user, Franchise $franchise)
    {
        return $user->isHeadOffice();
    }

    /**
     * Determine whether the user can delete the franchise.
     *
     * @param  \App\User  $user
     * @param  \App\Franchise  $franchise
     * @return mixed
     */
    public function delete(User $user, Franchise $franchise)
    {
        return $user->isHeadOffice();
    }

    /**
     * Determine whether the user can restore the franchise.
     *
     * @param  \App\User  $user
     * @param  \App\Franchise  $franchise
     * @return mixed
     */
    public function restore(User $user, Franchise $franchise)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the franchise.
     *
     * @param  \App\User  $user
     * @param  \App\Franchise  $franchise
     * @return mixed
     */
    public function forceDelete(User $user, Franchise $franchise)
    {
        //
    }
}
