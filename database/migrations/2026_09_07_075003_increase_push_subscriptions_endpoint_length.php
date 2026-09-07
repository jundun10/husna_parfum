<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'publicKey' => ['required', 'string'],
            'authToken' => ['required', 'string'],
        ]);

        $request->user()->updatePushSubscription(
            $validated['endpoint'],
            $validated['publicKey'],
            $validated['authToken']
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi browser berhasil diaktifkan.',
        ]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->user()->deletePushSubscription(
            $request->input('endpoint')
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi browser berhasil dinonaktifkan.',
        ]);
    }
}