<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Manajemen User</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola akun pengguna aplikasi</p>
            </div>
            @role('superadmin|admin')
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User
            </a>
            @endrole
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Alert Success --}}
            @if(session('success'))
                <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filter & Search --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-4">
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Search --}}
                        <div class="relative flex-1 min-w-[220px]">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama / email..."
                                   class="w-full pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                        </div>

                        {{-- Filter Role --}}
                        <select name="role"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                            <option value="">Semua Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Tombol --}}
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 dark:bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M8 12h8M11 20h2"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ $loop->iteration }}</td>

                            {{-- Nama + Avatar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold text-sm shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $user->email }}</td>

                            {{-- Role Badge --}}
                            <td class="px-6 py-4">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs rounded-full font-medium
                                        {{ $role->name === 'superadmin' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $role->name === 'admin'      ? 'bg-blue-100 text-blue-700'   : '' }}
                                        {{ $role->name === 'sales'      ? 'bg-green-100 text-green-700' : '' }}">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>

                            {{-- Toggle Status --}}
                            <td class="px-6 py-4">
                                <form action="{{ route('users.toggle-active', $user) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-full font-medium transition
                                                {{ $user->is_active
                                                    ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-700'
                                                    : 'bg-red-100 text-red-700 hover:bg-green-100 hover:text-green-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    <button onclick="openResetModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 01-2 2m2-2H9m6 0V7m0 4v2m0 4H9m6 0a2 2 0 01-2 2m2-2a2 2 0 002-2"/>
                                        </svg>
                                        Reset PW
                                    </button>

                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus user {{ addslashes($user->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-xs font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m0 0A4 4 0 1112 7a4 4 0 01-2 6.13M15 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-sm">Tidak ada user ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
                @endif
            </div>

            {{-- Activity Log Link --}}
            @role('superadmin')
            <div class="flex justify-end">
                <a href="{{ route('activity.log') }}"
                   class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Lihat Activity Log
                </a>
            </div>
            @endrole

        </div>
    </div>

    {{-- Modal Reset Password --}}
    <div id="resetModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden px-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Reset Password</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Untuk: <span id="resetUserName" class="font-medium text-gray-700 dark:text-gray-300"></span></p>
                </div>
                <button onclick="closeResetModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="resetForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Baru</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg text-sm px-3 py-2 bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                           placeholder="Min. 8 karakter">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg text-sm px-3 py-2 bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                           placeholder="Ulangi password baru">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeResetModal()"
                            class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openResetModal(userId, userName) {
            document.getElementById('resetUserName').textContent = userName;
            document.getElementById('resetForm').action = `/users/${userId}/reset-password`;
            document.getElementById('resetModal').classList.remove('hidden');
        }
        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
            document.getElementById('resetForm').reset();
        }
        // Tutup modal saat klik backdrop
        document.getElementById('resetModal').addEventListener('click', function(e) {
            if (e.target === this) closeResetModal();
        });
    </script>
</x-app-layout>