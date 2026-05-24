<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Activity Log</flux:heading>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-end gap-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search activities..."
            class="max-w-md"
        />

        <flux:select wire:model.live="causerId" label="User" class="w-48">
            <option value="">All users</option>
            @foreach ($this->causers as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="event" label="Event" class="w-40">
            <option value="">All events</option>
            @foreach ($this->events as $evt)
                <option value="{{ $evt }}">{{ ucfirst($evt) }}</option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="subjectType" label="Subject" class="w-56">
            <option value="">All subjects</option>
            @foreach ($this->subjectTypes as $type => $label)
                <option value="{{ $type }}">{{ $label }}</option>
            @endforeach
        </flux:select>
    </div>

    {{-- Activity Table --}}
    <flux:card>
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Time</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Event</flux:table.column>
                    <flux:table.column>Subject</flux:table.column>
                    <flux:table.column>Description</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($activities as $activity)
                        <flux:table.row>
                            <flux:table.cell>
                                <span title="{{ $activity->created_at }}">
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $activity->causer?->name ?? 'System' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge
                                    size="sm"
                                    color="{{ match ($activity->event) {
                                        'created' => 'green',
                                        'updated' => 'blue',
                                        'deleted' => 'red',
                                        default => 'zinc',
                                    } }}"
                                >
                                    {{ ucfirst($activity->event ?? '—') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $this->subjectTypes[$activity->subject_type] ?? class_basename($activity->subject_type) }}
                                @if ($activity->subject)
                                    <span class="text-zinc-500">#{{ $activity->subject_id }}</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="max-w-md truncate">
                                {{ $activity->description }}
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-zinc-500 py-8">
                                No activity found.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    </flux:card>
</div>
