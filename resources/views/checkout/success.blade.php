<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Betaling Succesvol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <!-- Success icon -->
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">Betaling Succesvol!</h3>
                    <p class="text-gray-600 mb-6">Je ticket is succesvol betaald en bevestigd.</p>

                    <!-- Ticket informatie -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6 text-left">
                        <h4 class="font-semibold mb-4">Ticket Details</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Event:</span>
                                <span class="font-medium">{{ $ticket->ticketType->event->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ticket Type:</span>
                                <span class="font-medium">{{ $ticket->ticketType->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Bezoeker:</span>
                                <span class="font-medium">{{ $ticket->attendee_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ticket ID:</span>
                                <span class="font-mono text-sm">{{ $ticket->qr_code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Datum:</span>
                                <span class="font-medium">{{ $ticket->ticketType->event->start_date->format('d M Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Locatie:</span>
                                <span class="font-medium">{{ $ticket->ticketType->event->location }}, {{ $ticket->ticketType->event->city }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Betaald bedrag:</span>
                                <span class="font-medium">€{{ number_format($ticket->amount_paid, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2">Je QR Code</h4>
                        <div class="bg-white p-4 border rounded-lg inline-block">
                            <div class="w-48 h-48 bg-gray-100 flex items-center justify-center">
                                <!-- Hier zou de QR code worden weergegeven -->
                                <span class="text-gray-500 text-sm text-center">
                                    QR Code<br>
                                    {{ $ticket->qr_code }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Acties -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('tickets.download', $ticket) }}" 
                           class="bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition-colors">
                            Download Ticket (PDF)
                        </a>
                        <a href="{{ route('tickets.my-tickets') }}" 
                           class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition-colors">
                            Mijn Tickets
                        </a>
                        <a href="{{ route('events.index') }}" 
                           class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition-colors">
                            Meer Events
                        </a>
                    </div>

                    <!-- Informatie -->
                    <div class="mt-8 text-sm text-gray-500">
                        <p>Je ontvangt een bevestigingsemail met alle details.</p>
                        <p>Bewaar je ticket goed - je hebt deze nodig voor toegang tot het event.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 