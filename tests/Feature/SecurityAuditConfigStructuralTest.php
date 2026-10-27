<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationComment;
use App\Models\User;
use App\Services\HashidService;
use App\Services\SubmissionWorkflow;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NewSeeder;
use Database\Seeders\UserCategorySeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityAuditConfigStructuralTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function hashid_service_throws_when_salt_is_empty(): void
    {
        $reflection = new \ReflectionClass(HashidService::class);
        $property = $reflection->getProperty('hashids');
        $property->setValue(null, null);

        Config::set('app.hashid_salt', '');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('HASHID_SALT is not configured.');

        HashidService::encode(1);
    }

    #[Test]
    public function authenticated_responses_carry_no_store_cache_headers(): void
    {
        $user = User::factory()->create([
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'FC',
            'geo_state' => 'FC',
            'email_verified_at' => now(),
            'is_enabled' => true,
        ]);

        $response = $this->actingAs($user)->get('/user/dashboard');

        $response->assertHeader('Cache-Control');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response->assertHeader('Pragma', 'no-cache');
        $response->assertHeader('Expires', '0');
    }

    #[Test]
    public function robots_txt_denies_by_default_with_directory_exception(): void
    {
        $path = public_path('robots.txt');
        $this->assertFileExists($path);

        $body = file_get_contents($path);
        $this->assertStringContainsString('Disallow: /', $body);
        $this->assertStringContainsString('Allow: /directory', $body);
        $this->assertStringContainsString('Allow: /$', $body);
    }

    #[Test]
    public function upload_validation_rejects_more_than_five_files(): void
    {
        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = UploadedFile::fake()->create('doc'.$i.'.pdf', 512, 'application/pdf');
        }

        $validator = \Illuminate\Support\Facades\Validator::make(
            ['attachments' => $files],
            [
                'attachments' => ['nullable', 'array', 'max:5'],
                'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240', 'extensions:pdf,doc,docx,jpg,jpeg,png'],
            ]
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('attachments', $validator->errors()->toArray());
    }

    #[Test]
    public function upload_validation_rejects_files_larger_than_ten_megabytes(): void
    {
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['attachments' => [UploadedFile::fake()->create('large.pdf', 10241, 'application/pdf')]],
            [
                'attachments' => ['nullable', 'array', 'max:5'],
                'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240', 'extensions:pdf,doc,docx,jpg,jpeg,png'],
            ]
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('attachments.0', $validator->errors()->toArray());
    }

    #[Test]
    public function workflow_approval_rolls_back_on_exception(): void
    {
        $officer = User::factory()->create([
            'user_category' => 'state_user',
            'primary_location_type' => 'state',
            'primary_location_code' => 'FC',
            'geo_state' => 'FC',
            'email_verified_at' => now(),
            'is_enabled' => true,
        ]);

        $deskAdmin = User::factory()->create([
            'user_category' => 'desk_admin',
            'primary_location_type' => 'state',
            'primary_location_code' => 'FC',
            'assigned_state_code' => 'FC',
            'geo_state' => 'FC',
            'email_verified_at' => now(),
            'is_enabled' => true,
        ]);

        $application = SubmissionWorkflow::create($officer, [
            'command_name' => 'Lagos Command',
            'report_period' => now()->format('Y-m'),
            'reporting_officer' => 'John Doe',
            'data_consent' => true,
        ]);

        $originalStatus = $application->fresh()->status;
        $originalStage = $application->fresh()->workflow_stage;
        $originalCommentCount = $application->fresh()->reviewComments()->count();

        // Force the comment insert (inside the transaction) to fail so we can
        // assert the application update is rolled back together with it.
        ApplicationComment::observe(new class {
            public function creating(): void
            {
                throw new \RuntimeException('Simulated comment failure');
            }
        });

        try {
            SubmissionWorkflow::approve($application, $deskAdmin, 'Verified against source records.');
            $this->fail('Expected approval to fail when the comment insert fails.');
        } catch (\Throwable $e) {
            // Expected.
        }

        $fresh = $application->fresh();
        $this->assertSame($originalStatus, $fresh->status);
        $this->assertSame($originalStage, $fresh->workflow_stage);
        $this->assertSame($originalCommentCount, $fresh->reviewComments()->count());

        ApplicationComment::flushEventListeners();
    }

    #[Test]
    public function seeders_throw_in_production(): void
    {
        $this->app->instance('env', 'production');

        $this->expectException(\RuntimeException::class);

        (new NewSeeder)->run();
    }

    #[Test]
    public function user_category_seeder_throws_in_production(): void
    {
        $this->app->instance('env', 'production');

        $this->expectException(\RuntimeException::class);

        (new UserCategorySeeder)->run();
    }

    #[Test]
    public function database_seeder_throws_in_production(): void
    {
        $this->app->instance('env', 'production');

        $this->expectException(\RuntimeException::class);

        (new DatabaseSeeder)->run();
    }
}
