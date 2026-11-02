<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_renders(): void
    {
        $this->get(route('pages.privacy'))
            ->assertOk()
            ->assertSeeText('Privacy Policy')
            ->assertSeeText('privacy@vefiri.com')
            ->assertSeeText('Last Updated: January 1, 2026');
    }

    public function test_terms_of_use_page_renders(): void
    {
        $this->get(route('pages.terms'))
            ->assertOk()
            ->assertSeeText('Terms and Conditions')
            ->assertSeeText('legal@vefiri.com')
            ->assertSeeText('Governing Law');
    }

    public function test_returns_and_refunds_page_renders(): void
    {
        $this->get(route('pages.returns'))
            ->assertOk()
            ->assertSeeText('Returns & Refunds')
            ->assertSeeText('7 days')
            ->assertSeeText('15 days')
            ->assertSeeText('support@vefiri.com');
    }
}
