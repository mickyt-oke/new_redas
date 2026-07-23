<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsWebTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(
        string $role = 'officer',
        string $category = 'state_user',
        string $locationType = 'state',
        int $accessLevel = 0
    ): User {
        return User::factory()->create([
            'service_number' => fake()->unique()->regexify('NIS/[A-Z]{3}/[0-9]{4}'),
            'role' => $role,
            'user_category' => $category,
            'primary_location_type' => $locationType,
            'primary_location_code' => 'AB',
            'access_level' => $accessLevel,
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    public function test_notifications_api_returns_items_and_supports_unread_filter(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $unread1 = UserNotification::query()->create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'T1',
            'description' => 'D1',
            'tag' => 'System',
            'action_url' => '/user/dashboard',
            'is_read' => false,
            'payload_json' => null,
            'error' => null,
        ]);

        $unread2 = UserNotification::query()->create([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'T2',
            'description' => 'D2',
            'tag' => 'Approvals',
            'action_url' => '/user/returns/create',
            'is_read' => false,
            'payload_json' => null,
            'error' => null,
        ]);

        $read = UserNotification::query()->create([
            'user_id' => $user->id,
            'type' => 'danger',
            'title' => 'T3',
            'description' => 'D3',
            'tag' => 'Urgent',
            'action_url' => '/user/returns/create',
            'is_read' => true,
            'payload_json' => null,
            'error' => null,
        ]);

        $this->assertTrue($user->hasCategory('state_user'));
        $this->assertTrue($user->hasLocationType('state'));
        $this->assertTrue($user->hasRoleType('officer'));
        $this->assertTrue($user->hasLegacyRole('officer'));
        $this->assertTrue($user->hasMinimumAccessLevel(0));

        $resAll = $this->withoutMiddleware(\App\Http\Middleware\CheckAccess::class)
            ->withoutExceptionHandling()
            ->getJson('/user/notifications/api');
        $resAll->assertStatus(200);
        $resAll->assertJsonStructure([
            'items' => [
                '*' => [
                    'id',
                    'type',
                    'title',
                    'description',
                    'tag',
                    'action_url',
                    'is_read',
                    'created_at',
                ],
            ],
        ]);
        $this->assertCount(3, $resAll->json('items'));

        $resUnread = $this->getJson('/user/notifications/api?unread=1');
        $resUnread->assertStatus(200);
        $this->assertCount(2, $resUnread->json('items'));

        $ids = array_map(fn ($i) => $i['id'], $resUnread->json('items'));
        $this->assertContains($unread1->id, $ids);
        $this->assertContains($unread2->id, $ids);
        $this->assertNotContains($read->id, $ids);
    }

    public function test_notifications_api_count_mark_all_read_and_mark_single_read(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $n1 = UserNotification::query()->create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'T1',
            'description' => 'D1',
            'tag' => 'System',
            'action_url' => '/user/dashboard',
            'is_read' => false,
            'payload_json' => null,
            'error' => null,
        ]);

        $n2 = UserNotification::query()->create([
            'user_id' => $user->id,
            'type' => 'success',
            'title' => 'T2',
            'description' => 'D2',
            'tag' => 'Approvals',
            'action_url' => '/user/returns/create',
            'is_read' => true,
            'payload_json' => null,
            'error' => null,
        ]);

        $count = $this->withoutMiddleware(\App\Http\Middleware\CheckAccess::class)
            ->getJson('/user/notifications/count');
        $count->assertStatus(200);
        $count->assertJson(['unread_count' => 1]);

        $markAll = $this->withoutMiddleware(\App\Http\Middleware\CheckAccess::class)
            ->postJson('/user/notifications/mark-all-read', []);
        $markAll->assertStatus(200);
        $markAll->assertJson(['message' => 'OK']);

        $this->assertDatabaseHas('user_notifications', [
            'id' => $n1->id,
            'is_read' => 1,
        ]);

        $count2 = $this->withoutMiddleware(\App\Http\Middleware\CheckAccess::class)
            ->getJson('/user/notifications/count');
        $count2->assertStatus(200);
        $count2->assertJson(['unread_count' => 0]);

        $markSingle = $this->withoutMiddleware(\App\Http\Middleware\CheckAccess::class)
            ->postJson("/user/notifications/{$n2->id}/read", []);
        $markSingle->assertStatus(200);
        $markSingle->assertJson(['message' => 'OK']);
    }

    public function test_mark_single_read_returns_not_found_for_missing_notification(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $res = $this->withoutMiddleware(\App\Http\Middleware\CheckAccess::class)
            ->postJson('/user/notifications/999999/read', []);
        $res->assertStatus(404);
        $res->assertJson(['message' => 'NOT_FOUND']);
    }
}
