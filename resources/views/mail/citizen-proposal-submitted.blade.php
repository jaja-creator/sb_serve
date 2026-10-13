<x-mail::message>
# New Citizen Proposal Submitted

**Reference No:** SB-P-{{ $proposal->proposal_id }}
**Type:** {{ $proposal->proposal_type }}
**Title:** {{ $proposal->title }}

**Submitted by:** {{ $proposal->proposer_name }} ({{ $proposal->proposer_email }})
@if ($proposal->proposer_phone)
**Phone:** {{ $proposal->proposer_phone }}
@endif
@if ($proposal->proposer_barangay)
**Barangay:** {{ $proposal->proposer_barangay }}
@endif

**Description / Rationale:**
{{ $proposal->description }}

<x-mail::button :url="route('citizen-proposals.index')">
Review in SB-Serve
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>