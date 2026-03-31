<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600">← Kembali</a>
            <h2 class="text-xl font-semibold text-gray-800">Activity Log</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dilakukan Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktivitas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail Perubahan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $log->causer?->name ?? 'System' }}
                                @if($log->causer)
                                <span class="block text-xs text-gray-400">
                                    {{ $log->causer->getRoleNames()->first() }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $log->description }}</td>
                            <td class="px-6 py-4">
                                @if($log->properties->get('attributes') || $log->properties->get('old'))
                                <button onclick="toggleDetail('log-{{ $log->id }}')"
                                        class="text-xs text-indigo-600 hover:underline">
                                    Lihat Detail
                                </button>
                                <div id="log-{{ $log->id }}" class="hidden mt-2 text-xs space-y-1">
                                    @if($log->properties->get('old'))
                                        <p class="text-red-500 font-medium">Sebelum:</p>
                                        @foreach($log->properties->get('old') as $key => $val)
                                            <p class="text-gray-500">{{ $key }}: {{ $val }}</p>
                                        @endforeach
                                    @endif
                                    @if($log->properties->get('attributes'))
                                        <p class="text-green-600 font-medium mt-1">Sesudah:</p>
                                        @foreach($log->properties->get('attributes') as $key => $val)
                                            <p class="text-gray-500">{{ $key }}: {{ $val }}</p>
                                        @endforeach
                                    @endif
                                </div>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                Belum ada aktivitas tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleDetail(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
</x-app-layout>