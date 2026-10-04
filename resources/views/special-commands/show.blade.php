@include('shared.single-return.show', [
    'application' => $application,
    'commandName' => $commandName,
    'commandLabel' => 'Special Command',
    'backRoute' => 'special-commands.returns.index',
    'docRoute' => 'special-commands.returns.document',
])
