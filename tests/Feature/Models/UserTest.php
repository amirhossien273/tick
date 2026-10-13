<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_user_receives_uuid_and_records_safe_activity_changes(): void
    {
        $user = User::factory()->create();

        $activity = $user->activities()->where('event', 'created')->sole();

        $this->assertTrue(Str::isUuid($user->id));
        $this->assertSame('users', $activity->log_name);
        $this->assertTrue($activity->subject->is($user));
        $this->assertArrayHasKey('name', $activity->attribute_changes->get('attributes'));
        $this->assertArrayNotHasKey('password', $activity->attribute_changes->get('attributes'));
    }
}
