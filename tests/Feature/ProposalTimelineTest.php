<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProposalTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_proposal_can_be_submitted_with_a_date_range(): void
    {
        Storage::fake('public');
        $academic = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_proposals' => true,
        ]);

        $this->actingAs($academic)->post(route('proposals.store'), [
            'title' => 'Community reading event',
            'timeline_from' => '2026-12-01',
            'timeline_to' => '2026-12-15',
            'destination' => 'https://aru.ac.tz/submissions',
            'details' => 'Plan and host the event.',
            'attachment' => UploadedFile::fake()->create('proposal.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('proposal'));

        $proposal = Proposal::firstOrFail();
        $this->assertSame('2026-12-01', $proposal->timeline_from->format('Y-m-d'));
        $this->assertSame('2026-12-15', $proposal->timeline_to->format('Y-m-d'));
        $this->assertSame('https://aru.ac.tz/submissions', $proposal->destination);
        $this->assertSame('2026-12-01 to 2026-12-15', $proposal->timeline);
        Storage::disk('public')->assertExists($proposal->attachment);
    }

    public function test_academic_user_can_download_a_proposal_pdf(): void
    {
        Storage::fake('public');
        $academic = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_proposals' => true,
        ]);
        $path = UploadedFile::fake()->createWithContent('proposal.pdf', '%PDF test contents')
            ->store('proposals', 'public');
        $proposal = Proposal::create([
            'title' => 'Community reading event',
            'timeline' => '2026-12-01 to 2026-12-15',
            'timeline_from' => '2026-12-01',
            'timeline_to' => '2026-12-15',
            'destination' => 'Library office',
            'details' => 'Plan and host the event.',
            'attachment' => $path,
        ]);

        $response = $this->actingAs($academic)
            ->get(route('proposals.download', $proposal))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=community-reading-event.pdf');

        $this->assertSame('%PDF test contents', $response->streamedContent());
    }

    public function test_download_returns_not_found_when_the_pdf_is_missing(): void
    {
        Storage::fake('public');
        $academic = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_proposals' => true,
        ]);
        $proposal = Proposal::create([
            'title' => 'Missing attachment',
            'timeline' => '2026-12-01 to 2026-12-15',
            'timeline_from' => '2026-12-01',
            'timeline_to' => '2026-12-15',
            'destination' => 'Library office',
            'details' => 'Plan and host the event.',
            'attachment' => 'proposals/missing.pdf',
        ]);

        $this->actingAs($academic)
            ->get(route('proposals.download', $proposal))
            ->assertNotFound();
    }

    public function test_proposal_end_date_cannot_be_before_start_date(): void
    {
        Storage::fake('public');
        $academic = User::factory()->create([
            'staff_category' => 'academic',
            'can_manage_proposals' => true,
        ]);

        $this->actingAs($academic)->from(route('proposals.create'))
            ->post(route('proposals.store'), [
                'title' => 'Community reading event',
                'timeline_from' => '2026-12-15',
                'timeline_to' => '2026-12-01',
                'destination' => 'Library office',
                'details' => 'Plan and host the event.',
                'attachment' => UploadedFile::fake()->create('proposal.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('timeline_to');

        $this->assertDatabaseCount('proposals', 0);
    }

    public function test_proposal_date_search_returns_ranges_that_overlap_filter_dates(): void
    {
        $academic = User::factory()->create(['staff_category' => 'academic']);

        Proposal::create([
            'title' => 'December project',
            'timeline' => '2026-12-01 to 2026-12-31',
            'timeline_from' => '2026-12-01',
            'timeline_to' => '2026-12-31',
            'destination' => 'Library office',
            'details' => 'December project details.',
            'attachment' => 'proposals/december.pdf',
        ]);
        Proposal::create([
            'title' => 'January project',
            'timeline' => '2027-01-01 to 2027-01-31',
            'timeline_from' => '2027-01-01',
            'timeline_to' => '2027-01-31',
            'destination' => 'Library office',
            'details' => 'January project details.',
            'attachment' => 'proposals/january.pdf',
        ]);

        $this->actingAs($academic)
            ->get(route('proposals.search', ['from' => '2026-12-10', 'to' => '2026-12-12']))
            ->assertOk()
            ->assertSee('December project')
            ->assertSee('01 Dec 2026')
            ->assertSee('31 Dec 2026')
            ->assertDontSee('January project');

        $this->get(route('proposal', ['from' => '2026-12-10', 'to' => '2026-12-12']))
            ->assertOk()
            ->assertSee('December project')
            ->assertDontSee('January project');
    }

    public function test_proposal_date_filter_accepts_a_single_boundary_and_rejects_reversed_dates(): void
    {
        $academic = User::factory()->create(['staff_category' => 'academic']);
        Proposal::create([
            'title' => 'December project',
            'timeline' => '2026-12-01 to 2026-12-31',
            'timeline_from' => '2026-12-01',
            'timeline_to' => '2026-12-31',
            'destination' => 'Library office',
            'details' => 'December project details.',
            'attachment' => 'proposals/december.pdf',
        ]);
        Proposal::create([
            'title' => 'January project',
            'timeline' => '2027-01-01 to 2027-01-31',
            'timeline_from' => '2027-01-01',
            'timeline_to' => '2027-01-31',
            'destination' => 'Library office',
            'details' => 'January project details.',
            'attachment' => 'proposals/january.pdf',
        ]);

        $this->actingAs($academic)
            ->get(route('proposal', ['from' => '2026-12-15']))
            ->assertOk()
            ->assertSee('December project')
            ->assertSee('January project');

        $this->getJson(route('proposals.search', ['from' => '2026-12-20', 'to' => '2026-12-10']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('to');
    }
}
