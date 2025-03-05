<div style="background-color: oklch(0.21 0.034 264.665);">
    <form wire:submit.prevent="submit">
        @foreach ($this->form->getComponents() as $component)
        {!! $component->render() !!}
        @endforeach
        <button type="submit" class="btn btn-primary cursor-pointer" style="margin:5px 10px; border-radius:10px; color:white; padding:20px 10px; background-color:blue;">Submit Proposal</button>
    </form>
    <style>
        .fi-fo-field-wrp-error-message {
            color: red;
        }
    </style>
</div>