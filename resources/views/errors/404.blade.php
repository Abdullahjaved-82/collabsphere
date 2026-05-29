<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — CollabSphere</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <!-- Scripts & Styling -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%);
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center p-6 text-slate-800">
    <div class="w-full max-w-md error-card p-10 text-center flex flex-col items-center">
        <!-- Floating Illustration -->
        <div class="h-24 w-24 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner animate-bounce">
            🛸
        </div>
        
        <h1 class="text-6xl font-black text-indigo-600 tracking-tight">404</h1>
        <h2 class="text-xl font-bold text-slate-800 mt-2">Lost in the CollabSphere?</h2>
        <p class="text-slate-400 mt-3 text-xs leading-relaxed max-w-xs">The page you are looking for has floated out of range, been deleted, or never existed in this dimension.</p>
        
        <div class="mt-8 w-full">
            <a href="{{ route('dashboard') }}" 
               class="block w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition text-center shadow-lg shadow-indigo-200">
                Go Home
            </a>
        </div>
    </div>
</body>
</html>
