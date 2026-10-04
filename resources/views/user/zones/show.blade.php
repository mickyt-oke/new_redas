@include('shared.single-return.show', [
    'application' => $application,
    'commandName' => $commandName,
    'commandLabel' => 'Zone',
    'backRoute' => 'user.zones.returns.index',
    'docRoute' => 'user.zones.returns.document',
])
