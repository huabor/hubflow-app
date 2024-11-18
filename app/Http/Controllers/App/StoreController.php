<?php

namespace App\Http\Controllers\App;

use App\Http\Requests\App\StoreRequest;
use App\Models\App;
use Illuminate\Http\RedirectResponse;

final class StoreController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(StoreRequest $request): RedirectResponse
    {
        $user = $request->user();

        $app = new App;
        $app->fill($request->validated());
        $user->apps()->save($app);

        return to_route('app.show', $app)->with('notification', [
            'type' => 'success',
            'message' => "$app->name successfully created!'",
        ]);
    }
}
