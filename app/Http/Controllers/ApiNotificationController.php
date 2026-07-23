<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $unreadOnly = $request->boolean('unread');

        $query = UserNotification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        $limit = (int) $request->integer('limit', 20);
        $limit = max(1, min(100, $limit));

        $items = $query->limit($limit)->get()->map(function (UserNotification $n) {
            return [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'description' => $n->description,
                'tag' => $n->tag,
                'action_url' => $n->action_url,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'items' => $items,
        ]);
    }

    public function count(Request $request)
    {
        $user = Auth::user();

        $unread = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unread,
        ]);
    }

    public function markRead(Request $request, int $id)
    {
        $user = Auth::user();

        $n = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (! $n) {
            return response()->json(['message' => 'NOT_FOUND'], 404);
        }

        $n->is_read = true;
        $n->save();

        return response()->json(['message' => 'OK']);
    }

    public function markAllRead(Request $request)
    {
        $user = Auth::user();

        UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'OK']);
    }
}
