<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Atur Ulang Password - Zero Waste Kitchen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-900 mb-1 text-center">Buat Password Baru 🔒</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Silakan masukkan kata sandi baru Anda di bawah ini</p>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm text-center font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="Minimal 6 karakter" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="Ulangi password baru" required>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg transition cursor-pointer">Perbarui Password</button>
        </form>
    </div>
</body>
</html>