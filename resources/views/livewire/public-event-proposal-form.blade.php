<div class="space-y-6">
    <form wire:submit.prevent="submit" class="space-y-4">
        @foreach ($this->form->getComponents() as $component)
        <div class="flex flex-col w-full">
            {!! $component->render() !!}
        </div>
        @endforeach
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
            Submit Proposal
        </button>
    </form>
</div>