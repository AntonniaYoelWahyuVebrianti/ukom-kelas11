<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | K-Wave</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-100 via-white to-indigo-100">
    <div class="absolute top-6 left-6">
        <a href="/" class="flex items-center gap-2 text-sky-700 font-semibold text-lg">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/80 shadow">
                KW
            </span>
            <span>K-Wave</span>
        </a>
    </div>

    <div class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="max-w-5xl w-full grid gap-10 md:grid-cols-[1.2fr_1fr]">
            <div class="bg-white/80 backdrop-blur rounded-3xl shadow-xl border border-white/40 p-10 sm:p-12">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Welcome Back</h1>
                        <p class="text-sm text-gray-500 mt-2">Enter your account to keep exploring the K-pop universe.</p>
                    </div>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-blue-500 text-white text-xl font-bold shadow-inner">KW</span>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                <a href="{{ route('oauth.google.redirect') }}" class="w-full flex items-center justify-center gap-3 border border-sky-200 rounded-full py-3 text-sm font-semibold text-sky-700 bg-white hover:bg-sky-50 transition">
                    <svg class="h-5 w-5" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M533.5 278.4c0-17.4-1.6-34.1-4.7-50.4H272v95.3h147.3c-6.4 34.6-25.7 63.9-54.7 83.6v69.4h88.4c51.7-47.6 80.5-117.8 80.5-198.2z" fill="#4285f4"/>
                        <path d="M272 544.3c73.5 0 135.2-24.4 180.2-66.1l-88.4-69.4c-24.6 16.5-56.1 26.2-91.8 26.2-70.6 0-130.4-47.7-151.8-111.6h-92.9v70.4C72.5 490.6 165.1 544.3 272 544.3z" fill="#34a853"/>
                        <path d="M120.2 323.4c-5.7-16.5-9-34.1-9-52.1s3.3-35.6 9-52.1v-70.4h-92.9C9.5 195 0 232.5 0 271.3s9.5 76.3 27.3 109.5l92.9-70.4z" fill="#fbbc04"/>
                        <path d="M272 107.7c40 0 76 13.7 104.3 40.4l78.1-78.1C407 24.6 345.3 0 272 0 165.1 0 72.5 53.7 27.3 161.8l92.9 70.4C141.6 155.4 201.4 107.7 272 107.7z" fill="#ea4335"/>
                    </svg>
                    Continue with Google
                </a>

                <div class="flex items-center my-8">
                    <span class="flex-1 h-px bg-gradient-to-r from-transparent via-sky-200 to-transparent"></span>
                    <span class="px-6 text-xs uppercase text-gray-400 tracking-[0.2em]">Or login with email</span>
                    <span class="flex-1 h-px bg-gradient-to-r from-transparent via-sky-200 to-transparent"></span>
                </div>

                <form action="{{ route('login.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <div class="relative">
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border border-sky-100 bg-white/70 px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="you@example.com">
                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sky-400">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                        </div>
                        @error('email')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-sky-100 bg-white/70 px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="Enter your password">
                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sky-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-gray-600">
                            <input type="checkbox" name="remember" class="rounded border-sky-200 text-sky-600 focus:ring-sky-400" {{ old('remember') ? 'checked' : '' }}>
                            Remember me
                        </label>
                        <a href="#" class="text-sky-600 font-medium hover:text-sky-700">Forgot password?</a>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-sky-600 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 hover:bg-sky-700 transition">
                        Login to account
                    </button>
                </form>

                <p class="mt-10 text-center text-sm text-gray-500">
                    New to K-Wave?
                    <a href="{{ route('register') }}" class="font-semibold text-sky-600 hover:text-sky-700">Create an account</a>
                </p>
            </div>

            <div class="hidden md:flex flex-col justify-between rounded-3xl border border-white/40 bg-white/30 backdrop-blur px-10 py-12 text-sky-800 shadow-lg">
                <div>
                    <h2 class="text-2xl font-bold">Exclusive access</h2>
                    <p class="mt-4 text-sm text-sky-900/80 leading-relaxed">
                        Track limited drops, reserve merch, and join community events with a single login. Stay in sync with every beat.
                    </p>
                </div>
                <div class="space-y-6 text-sm">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 text-sky-600 font-semibold">01</span>
                        <p>Get notified instantly when your favorite group releases new merchandise.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 text-sky-600 font-semibold">02</span>
                        <p>Manage orders, wishlists, and fan club perks without missing a beat.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 text-sky-600 font-semibold">03</span>
                        <p>Sync your K-Wave profile across devices with secure cloud backup.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" defer></script>
</body>
</html>
