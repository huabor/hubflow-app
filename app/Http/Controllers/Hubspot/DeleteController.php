<?php

namespace App\Http\Controllers\Hubspot;

use App\Models\HubspotToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class DeleteController
{
    public function __invoke(Request $request, HubspotToken $token): RedirectResponse
    {
        $user = $request->user();
        if ($user->hub_id === $token->hub_id) {
            $user->hub_id = null;
            $user->save();
        }

        if ($token->user_id !== $user->id) {
            return to_route('hubspot.token.index')->with('notification', [
                'type' => 'error',
                'message' => 'Token cannot be deleted!',
            ]);
        }

        $token->delete();

        return to_route('hubspot.token.index')->with('notification', [
            'type' => 'success',
            'message' => 'Token successfully deleted!',
        ]);
    }
}
