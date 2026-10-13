<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-widest text-steel font-mono mb-1">
            {{ $document ? 'Amend Registry Entry' : 'New Registry Entry' }}
        </p>
        <flux:heading size="lg" class="font-serif text-ink">{{ $document ? 'Edit Document' : 'Encode New Document' }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="border border-gray-300 rounded-lg p-6">
            <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-4">
                Identification
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input wire:model="series_number" label="Series Number" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                <flux:select wire:model="document_type" label="Document Type" class="[&_button]:!text-ink [&_button]:!border [&_button]:!border-gray-300">
                    <flux:select.option value="Ordinance">Ordinance</flux:select.option>
                    <flux:select.option value="Resolution">Resolution</flux:select.option>
                </flux:select>
                <div class="sm:col-span-2">
                    <flux:input wire:model="title" label="Title" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                </div>
            </div>
        </div>

        <div class="border border-gray-300 rounded-lg p-6">
            <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-4">
                Particulars
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($document)
                    <flux:input :value="$status" label="Status" readonly disabled class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                    <p class="text-xs text-steel self-end pb-2">Status changes are made from the document's detail page.</p>
                @else
                   <div>
    <flux:input value="Filed" label="Status" readonly disabled class="[&_[data-flux-label]]:!text-ink [&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
    <p class="text-xs text-steel mt-1">New documents are automatically filed. Use "Save as Draft" to park it as a draft instead.</p>
</div>
                @endif

                <flux:input wire:model="sponsor" label="Sponsor" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />

                <div class="sm:col-span-2">
                    <flux:textarea wire:model="subject_matter" label="Subject Matter" class="[&_textarea]:!text-ink [&_textarea]:!border [&_textarea]:!border-gray-300" />
                </div>
            </div>
        </div>

        <div class="border border-gray-300 rounded-lg p-6">
            <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-4">
                Dates on Record
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <flux:input wire:model="date_filed" label="Date Filed" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
                <flux:input wire:model="date_approved_mayor" label="Date Approved by Mayor" type="date" class="[&_input]:!text-ink [&_input]:!border [&_input]:!border-gray-300" />
            </div>
        </div>

        <div class="border border-gray-300 rounded-lg p-6">
            <p class="text-xs uppercase tracking-widest text-steel font-mono font-semibold mb-4">
                Supporting Attachment
            </p>

            <label for="scannedFile" class="photo-dropzone !border !border-gray-300 {{ $scannedFile || $existingFilePath ? 'photo-dropzone-filled' : '' }}">
                @if ($scannedFile)
                    <flux:icon name="document-text" class="size-8 text-ledger-green" />
                    <span class="text-sm text-ink mt-2">{{ $scannedFile->getClientOriginalName() }}</span>
                @elseif ($existingFilePath)
                    <flux:icon name="document-text" class="size-8 text-ledger-green" />
                    <span class="text-sm text-ink mt-2">Current attachment (PDF)</span>
                @else
                    <flux:icon name="document-text" class="size-6 text-steel" />
                    <span class="text-sm text-ink mt-2">Click to add a scan of the document</span>
                    <span class="text-xs text-steel mt-0.5">PDF only &middot; up to 15MB</span>
                @endif

                <input id="scannedFile" type="file" accept="application/pdf" wire:model="scannedFile" class="sr-only" />
            </label>

            <div wire:loading wire:target="scannedFile" class="text-xs text-steel mt-2">Uploading file...</div>

            @error('scannedFile') <p class="text-xs text-seal-red mt-2">{{ $message }}</p> @enderror

            @if ($scannedFile || $existingFilePath)
                <div class="flex items-center gap-3 mt-2">
                    @if ($scannedFile)
                        <span class="text-xs text-ink truncate">{{ $scannedFile->getClientOriginalName() }}</span>
                    @elseif ($existingFilePath)
                        <a href="{{ asset('storage/' . $existingFilePath) }}" target="_blank" class="text-xs text-ledger-green underline">
                            View full size
                        </a>
                    @endif
                    <button type="button" wire:click="removeExistingFile" class="text-sm font-medium text-red-600 hover:text-red-700 ml-auto">Remove</button>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,saveDraft">
                <span wire:loading.remove wire:target="save">Save Document</span>
                <span wire:loading wire:target="save">Saving...</span>
            </flux:button>

            <flux:button
                type="button"
                wire:click="saveDraft"
                variant="filled"
                wire:loading.attr="disabled"
                wire:target="save,saveDraft"
            >
                <span wire:loading.remove wire:target="saveDraft">Save as Draft</span>
                <span wire:loading wire:target="saveDraft">Saving draft...</span>
            </flux:button>

            <flux:button href="{{ route('documents.index') }}" variant="ghost" class="ml-auto !text-ink">Cancel</flux:button>
        </div>
    </form>
</div>