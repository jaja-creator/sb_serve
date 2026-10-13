<x-mail::message>
# Status Update on Your Proposal

Dear {{ $proposal->proposer_name }},

The status of your submitted proposal has been updated by the Sangguniang Bayan office.

**Reference Number:** SB-P-{{ $proposal->proposal_id }}  
**Measure Title:** {{ $proposal->title }}  
**Type:** {{ $proposal->proposal_type }}  
**New Status:** **{{ $proposal->status }}**  
**Date Updated:** {{ $proposal->reviewed_at?->format('F j, Y') ?? now()->format('F j, Y') }}  

@if ($proposal->admin_remarks)
### Remarks from the Sanggunian:
> {{ $proposal->admin_remarks }}
@endif

---

You can track the ongoing status of your measure at any time on the SB-Serve portal.

<x-mail::button :url="route('public.propose')">
Track Proposal on SB-Serve
</x-mail::button>

Sincerely,<br>
**Office of the Sangguniang Bayan**<br>
{{ \App\Models\SystemSetting::get('municipality', 'Municipality of Allacapan') }}
</x-mail::message>
