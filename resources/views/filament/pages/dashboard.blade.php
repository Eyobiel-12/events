<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Widgets -->
        @if ($this->getHeaderWidgets())
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-4">
                @foreach ($this->getHeaderWidgets() as $widget)
                    @livewire($widget)
                @endforeach
            </div>
        @endif

        <!-- Export Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Data</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select id="exportType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="events">Events</option>
                        <option value="tickets">Tickets</option>
                        <option value="revenue">Omzet</option>
                        <option value="feedback">Feedback</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formaat</label>
                    <select id="exportFormat" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="exportData()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Widgets -->
        @if ($this->getFooterWidgets())
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @foreach ($this->getFooterWidgets() as $widget)
                    @livewire($widget)
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function exportData() {
            const type = document.getElementById('exportType').value;
            const format = document.getElementById('exportFormat').value;
            
            const url = new URL('/dashboard/export', window.location.origin);
            url.searchParams.append('type', type);
            url.searchParams.append('format', format);
            
            window.location.href = url.toString();
        }
    </script>
</x-filament-panels::page> 