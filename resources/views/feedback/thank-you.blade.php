@extends('layouts.app')

@section('title', 'Bedankt voor je Feedback')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="text-6xl mb-4">🎉</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Bedankt voor je Feedback!</h1>
            <p class="text-gray-600 mb-6">
                Je feedback is succesvol verzonden en wordt binnenkort beoordeeld door ons team. 
                We waarderen je tijd en input om onze events te verbeteren.
            </p>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-blue-900 mb-2">Wat gebeurt er nu?</h2>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Je feedback wordt beoordeeld door ons team</li>
                    <li>• Goedgekeurde feedback wordt zichtbaar op de eventpagina</li>
                    <li>• We gebruiken je input om toekomstige events te verbeteren</li>
                </ul>
            </div>

            <div class="flex justify-center space-x-4">
                <a href="{{ route('events.show', $event) }}" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                    Terug naar Event
                </a>
                <a href="{{ route('events.index') }}" 
                    class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                    Bekijk Andere Events
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 