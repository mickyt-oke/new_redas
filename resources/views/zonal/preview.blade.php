@extends('desk-admin.layout')

@section('content')
<main class="redas-content">
    @include('partials.return-preview', [
        'application' => $application,
        'documentRoute' => 'zonal.submissions.document',
        'downloadRoute' => 'zonal.submissions.download',
        'backRoute' => 'user.zonal.home',
        'backLabel' => 'Back to Zonal Dashboard',
        'canReview' => $canReview ?? false,
        'approveRoute' => $approveRoute ?? 'zonal.submissions.approve',
        'rejectRoute' => $rejectRoute ?? 'zonal.submissions.reject',
    ])
</main>
@endsection
