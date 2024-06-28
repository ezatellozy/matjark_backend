<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\User;

class UserObserver
{

    public function saved(User $user)
    {
        if (request()->hasFile('avatar') && request()->avatar->isValid()) {
            if ($user->media()->where('option', 'avatar')->exists()) {
                $avatar = $user->media()->where('option', 'avatar')->first();

                if (file_exists(storage_path('app/public/images/user/'. $avatar->media))) {
                    \File::delete(storage_path('app/public/images/user/'. $avatar->media));
                }

                AppMedia::where(['app_mediaable_type' => 'App\Models\User', 'app_mediaable_id' => $user->id, 'media' => $avatar->media, 'media_type' => 'image', 'option' => 'avatar'])->delete();
            }

            $avatar = uploadImg(request()->avatar, 'user');
            $user->media()->create(['media' => $avatar, 'media_type' => 'image', 'option' => 'avatar', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
        }

        
    }

    public function deleted(User $user)
    {
        if ($user->media()->where('option', 'avatar')->exists()) {
            $avatar = $user->media()->where('option', 'avatar')->first();

            if (file_exists(storage_path('app/public/images/user/' . $avatar->media))) {
                \File::delete(storage_path('app/public/images/user/' . $avatar->media));
            }

            AppMedia::where(['app_mediaable_type' => 'App\Models\User', 'app_mediaable_id' => $user->id, 'media' => $avatar->media, 'media_type' => 'image', 'option' => 'avatar'])->delete();
        }
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
