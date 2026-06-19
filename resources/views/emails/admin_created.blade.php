<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <x-filament::section
            heading="My Profile"
            description="Manage your account information, profile picture, and security settings."
        >
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <div class="flex justify-end pt-4">
                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-check"
                    >
                        Save Changes
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>