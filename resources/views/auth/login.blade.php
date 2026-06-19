<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Masuk - Zero Waste Kitchen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-900 mb-1 text-center">Selamat Datang Kembali 👋</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Silakan masuk ke akun Zero Waste Kitchen kalian</p>
        
        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" required>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <label class="text-sm font-medium text-gray-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-green-600 hover:underline">Lupa Password?</a>
                </div>
                <input type="password" name="password" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" required>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg transition">Masuk</button>
        </form>
        <p class="text-sm text-center text-gray-600 mt-6">Belum punya akun? <a href="{{ route('register') }}" class="text-green-600 font-semibold hover:underline">Daftar sekarang</a></p>
    </div>
</body>
</html>