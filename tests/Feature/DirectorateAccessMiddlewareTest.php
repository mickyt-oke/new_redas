<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DirectorateAccessMiddlewareTest extends TestCase
{
    public function test_directorate_user_can_pass_directorate_access_constraints(): void
    {
        $user = new User();
        $user->forceFill([
            'user_category' => 'directorate_user',
            'primary_location_type' => 'directorate',
            'role' => 'directorate',
            'access_level' => 0,
        ]);

        $request = Request::create('/user/directorate', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new CheckAccess();

        $response = $middleware->handle(
            $request,
            fn (Request $request) => new Response('ok', 200),
            'category=directorate_user|directorate_admin,location=directorate,role=user|admin|directorate|minLevel=0'
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }
}
