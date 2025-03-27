<div class="space-y-6">
    <form wire:submit.prevent="submit" class="space-y-4">
        @foreach ($this->form->getComponents() as $component)
        <div class="flex flex-col w-full">
            {!! $component->render() !!}
        </div>
        @endforeach
        <button
            type="submit"
            class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition duration-300 flex items-center justify-center"
            wire:loading.attr="disabled">
            <span wire:loading.remove>Submit Proposal</span>
            <span wire:loading class="flex items-center">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
            </span>
        </button>
    </form>
</div>