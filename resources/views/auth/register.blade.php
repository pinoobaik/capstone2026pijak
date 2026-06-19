<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun - Zero Waste Kitchen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-900 mb-1 text-center">Buat Akun Baru 🌱</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Bergabung bersama kami menjaga bumi dari food waste</p>
        
        <form action="{{ route('register.proses') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" required value="{{ old('name') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" required value="{{ old('email') }}">
                @error('email')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" required>
                @error('password')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" required>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg transition mt-2 cursor-pointer">Daftar Akun</button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-6">Sudah punya akun? <a href="{{ route('login') }}" class="text-green-600 font-semibold hover:underline">Masuk disini</a></p>
    </div>
</body>
</html>