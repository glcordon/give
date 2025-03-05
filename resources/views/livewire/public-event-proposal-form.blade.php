<div>
    <form wire:submit.prevent="submit">
        @foreach ($this->form->getComponents() as $component)
        {!! $component->render() !!}
        @endforeach
        <button type="submit" class="btn btn-primary">Submit Proposal</button>
    </form>
</div>