<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">API Tokens</flux:heading>
    </div>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}" wire:poll.5s="$set('message', '')">
            {{ $message }}
        </flux:callout>
    @endif

    @if ($plainTextToken)
        <flux:callout variant="warning">
            <div class="space-y-3 w-full">
                <flux:text weight="semibold">Copy your new token now</flux:text>
                <flux:text size="sm">It will not be shown again.</flux:text>
                <div class="flex items-center gap-3">
                    <code class="flex-1 block p-3 bg-zinc-100 dark:bg-zinc-900 rounded text-sm break-all" id="new-token">{{ $plainTextToken }}</code>
                    <flux:button type="button" size="sm" variant="ghost" onclick="navigator.clipboard.writeText(document.getElementById('new-token').textContent)">
                        Copy
                    </flux:button>
                </div>
                <flux:button type="button" size="sm" variant="outline" wire:click="dismissToken">
                    Done
                </flux:button>
            </div>
        </flux:callout>
    @endif

    <flux:card>
        <flux:heading size="lg" class="mb-4">Create Token</flux:heading>
        <form wire:submit="create" class="flex items-end gap-4">
            <div class="flex-1">
                <flux:input
                    wire:model="name"
                    label="Token name"
                    placeholder="e.g. local-dev, deployment-script"
                    required
                />
            </div>
            <flux:button type="submit" variant="primary" icon="plus">
                Create Token
            </flux:button>
        </form>
    </flux:card>

    <flux:card>
        <flux:heading size="lg" class="mb-4">Active Tokens</flux:heading>
        @if ($tokens->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4 font-semibold">Name</th>
                            <th class="py-3 px-4 font-semibold">Last Used</th>
                            <th class="py-3 px-4 font-semibold">Created</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tokens as $token)
                            <tr class="border-b last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-800" wire:key="token-{{ $token->id }}">
                                <td class="py-3 px-4 font-medium">{{ $token->name }}</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400 text-sm">
                                    {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-sm">
                                    {{ $token->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <flux:button
                                        wire:click="revoke({{ $token->id }})"
                                        wire:confirm="Are you sure you want to revoke '{{ $token->name }}'?"
                                        size="sm"
                                        variant="danger"
                                    >
                                        Revoke
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <flux:icon name="key" class="w-12 h-12 mx-auto mb-4" />
                <p>No active API tokens.</p>
            </div>
        @endif
    </flux:card>
</div>
