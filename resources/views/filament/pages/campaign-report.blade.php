<x-filament::page>
    <h2>Total Donations: ${{ number_format($totalGifts, 2) }}</h2>
    <h3>Total Campaigns: {{ $totalCampaigns }}</h3>

    <h3>Campaigns Overview</h3>
    <table class="table-auto w-full">
        <thead>
            <tr>
                <th>Campaign Name</th>
                <th>Gifts Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($campaignStats as $campaign)
                <tr>
                    <td>{{ $campaign->name }}</td>
                    <td>{{ $campaign->gifts_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-filament::page>
