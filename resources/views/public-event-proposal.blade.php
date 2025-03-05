<!DOCTYPE html>
<html>

<head>
    <title>Propose an Event</title>
    @livewireStyles
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Optional: Custom CSS for additional tweaks -->
    <style>
        input,
        textarea,
        select {
            width: 100% !important;
            margin-bottom: 1rem;
        }

        .filament-forms-component-container>div {
            flex-direction: column;
        }

        .form-container {
            max-width: 100%;
            padding: 1rem;
            margin: 0 auto;
        }

        @media (min-width: 640px) {
            .form-container {
                max-width: 640px;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="form-container">
        @livewire('public-event-proposal-form')
    </div>
    @livewireScripts
</body>

</html>