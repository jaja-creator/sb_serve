<x-mail::message>
# Citizen Proposal Forwarded to SB

**Type:** {{ $proposal->proposal_type }}

**Title:** {{ $proposal->title }}

**Description:**
{{ $proposal->description }}

---

**Submitted by:** {{ $proposal->proposer_name }}
**Email:** {{ $proposal->proposer_email }}
@if ($proposal->proposer_phone)
**Phone:** {{ $proposal->proposer_phone }}
@endif
@if ($proposal->proposer_barangay)
**Barangay:** {{ $proposal->proposer_barangay }}
@endif

**Submitted on:** {{ $proposal->created_at->format('F j, Y') }}

<x-mail::button :url="route('citizen-proposals.index')">
Review in SB-Serve
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>