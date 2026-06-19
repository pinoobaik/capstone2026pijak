<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Zero Waste Kitchen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-900 mb-1 text-center">Lupa Password? 🔑</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Masukkan email terdaftar kamu, kami akan kirimkan tautan pemulihan sandi</p>
        
        @if (session('status'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->has('email'))
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium text-center">
                {{ $errors->first('email') }}
            </div>
        @endif
        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Anda</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="contoh@email.com" required>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg transition cursor-pointer">Kirim Link Pemulihan</button>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-green-600 font-medium transition">Kembali ke Halaman Masuk</a>
        </div>
    </div>
</body>
</html>