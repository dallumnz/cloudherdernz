<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Newsletter Subscribers</flux:heading>
        <flux:button wire:click="create" variant="primary">
            Add Subscriber
        </flux:button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <flux:card>
            <flux:heading size="sm">Total</flux:heading>
            <flux:text variant="strong" class="text-2xl">{{ $counts['all'] }}</flux:text>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">Active</flux:heading>
            <flux:text variant="strong" class="text-2xl text-green-600">{{ $counts['active'] }}</flux:text>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">Pending</flux:heading>
            <flux:text variant="strong" class="text-2xl text-yellow-600">{{ $counts['pending'] }}</flux:text>
        </flux:card>
        <flux:card>
            <flux:heading size="sm">Unsubscribed</flux:heading>
            <flux:text variant="strong" class="text-2xl text-red-600">{{ $counts['unsubscribed'] }}</flux:text>
        </flux:card>
    </div>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}" wire:poll.5s="$set('message', '')">
            {{ $message }}
        </flux:callout>
    @endif

    {{-- Search --}}
    <div class="flex items-center space-x-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search subscribers..."
            class="max-w-md"
        />
    </div>

    {{-- Form --}}
    @if ($showForm)
        <flux:card>
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit Subscriber' : 'Add Subscriber' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        wire:model="email"
                        label="Email"
                        type="email"
                        placeholder="subscriber@example.com"
                        required
                    />
                    <flux:input
                        wire:model="name"
                        label="Name"
                        placeholder="Subscriber name (optional)"
                    />
                </div>

                <flux:field label="Status">
                    <flux:select wire:model="status" placeholder="Choose status">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="unsubscribed">Unsubscribed</flux:select.option>
                    </flux:select>
                </flux:field>

                <div class="flex items-center space-x-3">
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </flux:button>
                    <flux:button type="button" wire:click="cancel" variant="ghost">
                        Cancel
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Subscribers List --}}
    <flux:card>
        @if ($subscribers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4 font-semibold">Email</th>
                            <th class="py-3 px-4 font-semibold">Name</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold">Subscribed</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscribers as $subscriber)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $subscriber->email }}</td>
                                <td class="py-3 px-4">{{ $subscriber->name ?? '—' }}</td>
                                <td class="py-3 px-4">
                                    @switch($subscriber->status)
                                        @case('active')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                            @break
                                        @case('pending')
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @break
                                        @case('unsubscribed')
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Unsubscribed</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $subscriber->subscribed_at?->format('M j, Y') ?? '—' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <flux:button size="sm" wire:click="edit({{ $subscriber->id }})" variant="ghost">
                                        Edit
                                    </flux:button>
                                    <flux:button size="sm" wire:click="delete({{ $subscriber->id }})" variant="ghost" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscribers->links() }}
            </div>
        @else
            <flux:heading size="lg" class="text-center py-8 text-gray-500">
                No subscribers found.
            </flux:heading>
        @endif
    </flux:card>
</div>
