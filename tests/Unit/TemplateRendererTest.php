<?php

namespace Tests\Unit;

use App\Services\Messaging\TemplateRenderer;
use PHPUnit\Framework\TestCase;

class TemplateRendererTest extends TestCase
{
    public function test_it_replaces_placeholders_with_spaced_and_unspaced_variants(): void
    {
        $renderer = new TemplateRenderer();

        $template = 'Hello {{ name }}, code={{code}} expires={{ expires }}.';
        $out = $renderer->render($template, [
            'name' => 'Alice',
            'code' => '123456',
            'expires' => '5 minutes',
        ]);

        $this->assertSame('Hello Alice, code=123456 expires=5 minutes.', $out);
    }
}
