<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600">
                ← Kembali
            </a>
            <h2 class="text-xl font-semibold text-gray-800">Tambah User</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-xl p-6">

                @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Masukkan nama lengkap">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Masukkan email">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" required
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Min. 8 karakter">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ulangi password">
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>