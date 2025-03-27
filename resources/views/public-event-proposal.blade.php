<!DOCTYPE html>
<html>

<head>
    <title>Propose an Event</title>
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        input,
        textarea,
        select {
            width: 100% !important;
            margin-bottom: 1rem;
        }

        .fi-fo-field-wrp-error-message {
            color: red;
            text-decoration: underline;
        }

        input[type="checkbox"] {
            width: 40px !important;
            border: 0 !important;
            padding-bottom: 0;
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

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="form-container">
        @livewire('public-event-proposal-form')
    </div>
    <!-- Optional: Full-screen loading overlay -->
    <div wire:loading class="loading-overlay" wire:target="submit">
        <div class="flex items-center text-white">
            <svg class="animate-spin h-8 w-8 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-lg">Processing your proposal...</span>
        </div>
    </div>
    @livewireScripts
</body>

</html>