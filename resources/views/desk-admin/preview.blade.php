@extends('desk-admin.layout')

@section('content')
<main class="redas-content">
    @include('partials.return-preview', [
        'application' => $application,
        'documentRoute' => 'desk.admin.submissions.document',
        'backRoute' => 'user.desk.home',
        'backLabel' => 'Back to Review Dashboard',
        'canReview' => $canReview ?? false,
        'approveRoute' => $approveRoute ?? 'desk.admin.submissions.approve',
        'rejectRoute' => $rejectRoute ?? 'desk.admin.submissions.reject',
    ])
</main>
@endsection
