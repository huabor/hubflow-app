<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Models\ContactCluster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AppController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, ContactCluster $cluster): JsonResponse
    {
        $objects = $cluster->resolvedObjects;

        return response()->json($objects);
    }
}
