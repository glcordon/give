<h1><strong>Name:</strong> {{ $record->name }}</h1>
<table style="width:400px">
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Date: </strong></td>
        <td>
            <div>{{ $record->date }}</div>
        </td>
    </tr>
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Event Name:</strong></td>
        <td>
            <div> {{ $record->activityPlan->event_name }}</div>
        </td>
    </tr>
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Campaign: </strong></td>
        <td>
            <div>{{ $record->campaign->name }}</div>
        </td>
    </tr>
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Total Cash:</strong></td>
        <td>
            <div> @formatMoney($record->total_cash)</div>
        </td>
    </tr>
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Total Coin:</strong></td>
        <td>
            <div> @formatMoney($record->total_coin)</div>
        </td>
    </tr>
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Total Cash + Coin:</strong></td>
        <td>
            <div> @formatMoney($record->total_cash_coin)</div>
        </td>
    </tr>
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Total Checks:</strong></td>
        <td>
            <div> @formatMoney($record->total_checks)</div>
        </td>
    </tr>
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Total Giving:</strong></td>
        <td>
            <div> @formatMoney($record->total_giving)</div>
        </td>
    </tr>
    <tr style="border-bottom: 1px solid black;padding-bottom: 10px;">
        <td><strong>Total Deposit:</strong></td>
        <td>
            <div> @formatMoney($record->total_bank_deposit)</div>
        </td>
    </tr>
</table>