<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold mb-6">Betaling</h3>

                    <!-- Ticket informatie -->
                    <div class="bg-gray-50 p-6 rounded-lg mb-6">
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
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">{{ $ticket->attendee_email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Datum:</span>
                                <span class="font-medium">{{ $ticket->ticketType->event->start_date->format('d M Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Locatie:</span>
                                <span class="font-medium">{{ $ticket->ticketType->event->location }}, {{ $ticket->ticketType->event->city }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Betalingsmethode -->
                    <form method="POST" action="{{ route('checkout.process', $ticket) }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <h4 class="font-semibold mb-4">Kies betalingsmethode</h4>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="payment_method" value="ideal" checked
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                    <div class="ml-3">
                                        <p class="font-medium">iDEAL</p>
                                        <p class="text-sm text-gray-600">Direct betalen via je bank</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="payment_method" value="creditcard"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                    <div class="ml-3">
                                        <p class="font-medium">Creditcard</p>
                                        <p class="text-sm text-gray-600">Visa, Mastercard, American Express</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="payment_method" value="paypal"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                    <div class="ml-3">
                                        <p class="font-medium">PayPal</p>
                                        <p class="text-sm text-gray-600">Betaling via PayPal account</p>
                                    </div>
                                </label>
                            </div>
                            @error('payment_method')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Totaal -->
                        <div class="border-t pt-6">
                            <div class="flex justify-between items-center text-xl font-semibold">
                                <span>Totaal te betalen:</span>
                                <span>€{{ number_format($ticket->amount_paid, 2) }}</span>
                            </div>
                        </div>

                        @error('error')
                            <div class="bg-red-50 border border-red-200 rounded-md p-3">
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            </div>
                        @enderror

                        <!-- Acties -->
                        <div class="flex space-x-4">
                            <a href="{{ route('checkout.cancel', $ticket) }}" 
                               class="flex-1 bg-gray-300 text-gray-700 px-4 py-3 rounded-md hover:bg-gray-400 transition-colors text-center">
                                Annuleren
                            </a>
                            <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-3 rounded-md hover:bg-indigo-700 transition-colors">
                                Betalen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 