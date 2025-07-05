@extends('layouts.app')

@section('title', 'Feedback - ' . $event->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>
                <p class="text-gray-600 mb-4">{{ $event->description }}</p>
                <div class="flex justify-center items-center space-x-6 text-sm text-gray-500">
                    <span>📅 {{ $event->start_date->format('d M Y H:i') }}</span>
                    <span>📍 {{ $event->location }}, {{ $event->city }}</span>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Feedback Overzicht</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['average_rating'], 1) }}</div>
                    <div class="text-sm text-gray-600">Gemiddelde Beoordeling</div>
                    <div class="text-2xl mt-1">{{ str_repeat('⭐', round($stats['average_rating'])) }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['total_feedback'] }}</div>
                    <div class="text-sm text-gray-600">Totaal Feedback</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ count($stats['rating_distribution']) }}</div>
                    <div class="text-sm text-gray-600">Rating Niveaus</div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Rating Verdeling</h3>
                <div class="space-y-2">
                    @for ($i = 5; $i >= 1; $i--)
                        @php
                            $count = $stats['rating_distribution'][$i] ?? 0;
                            $percentage = $stats['total_feedback'] > 0 ? ($count / $stats['total_feedback']) * 100 : 0;
                        @endphp
                        <div class="flex items-center">
                            <span class="w-8 text-sm">{{ $i }}⭐</span>
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mx-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="w-12 text-sm text-gray-600">{{ $count }}</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Feedback List -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Alle Feedback</h2>
                <a href="{{ route('feedback.create', $event) }}" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Feedback Geven
                </a>
            </div>

            @if ($feedback->count() > 0)
                <div class="space-y-4">
                    @foreach ($feedback as $item)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $item->attendee_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $item->submitted_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-2xl">{{ str_repeat('⭐', $item->rating) }}</div>
                            </div>
                            
                            @if ($item->comment)
                                <p class="text-gray-700 mt-2">{{ $item->comment }}</p>
                            @endif

                            @if ($item->categories)
                                <div class="mt-3">
                                    <span class="text-sm text-gray-500">Categorieën:</span>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach ($item->categories as $category)
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                {{ ucfirst($category) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $feedback->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">Nog geen feedback beschikbaar.</p>
                    <p class="text-sm text-gray-400 mt-2">Wees de eerste om feedback te geven!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 