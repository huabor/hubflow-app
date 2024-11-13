<?php

namespace App\Http\Controllers\Hubspot;

use App\Models\HubspotToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class DeleteController
{
    public function __invoke(Request $request, HubspotToken $token): RedirectResponse
    {
        if ($token->user_id !== $request->user()->id || true) {
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
