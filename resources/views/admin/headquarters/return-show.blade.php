@extends($layout ?? 'admin.headquarters.layout')

@section('title', 'Return Detail')

@section('content')
<main class="redas-content">
    @include('partials.return-preview', [
        'application' => $application,
        'documentRoute' => $documentRoute ?? 'admin.hq.returns.document',
        'downloadRoute' => $downloadRoute ?? 'admin.hq.returns.download',
        'backRoute' => $backRoute ?? 'admin.hq.returns',
        'backLabel' => 'Back to Returns',
        'canReview' => false,
    ])
</main>
@endsection
