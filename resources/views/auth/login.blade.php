<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — {{ config('app.name', 'Sistema de Préstamos') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-800 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-slate-900 to-slate-950">
    <div class="w-full max-w-md space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 items-center justify-center text-white shadow-xl shadow-blue-500/25 mb-2">
                <i data-lucide="boxes" class="w-8 h-8"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Sistema de Préstamos</h1>
            <p class="text-sm text-slate-400">Control de equipos, herramientas y accesorios</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 sm:p-8 shadow-2xl border border-slate-100/10 space-y-6">
            @if(session('info'))
                <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-center space-x-2">
                    <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                    <div class="font-semibold flex items-center space-x-1.5 text-rose-900">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                        <span>No se pudo iniciar sesión</span>
                    </div>
                    <ul class="list-disc list-inside pl-1 text-[11px] space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="ejemplo@prestamos.gob.pe"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center space-x-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded-md text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span>Recordar sesión</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-600/30 transition-all flex items-center justify-center space-x-2 cursor-pointer">
                    <span>Ingresar al Sistema</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Acceso Rápido Demo -->
            <div class="pt-4 border-t border-slate-100 text-center space-y-2.5">
                <p class="text-xs text-slate-500 font-medium">Accesos rápidos de demostración:</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            onclick="fillCredentials('carlos@prestamos.gob.pe', 'encargado123')"
                            class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50 hover:border-blue-300 text-left transition-colors cursor-pointer">
                        <div class="font-semibold text-xs text-slate-800 flex items-center">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-blue-600 mr-1.5"></i>
                            Encargado
                        </div>
                        <div class="text-[11px] text-slate-500 truncate">carlos@prestamos...</div>
                    </button>
                    <button type="button" 
                            onclick="fillCredentials('admin@prestamos.gob.pe', 'admin123')"
                            class="p-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 text-left transition-colors cursor-pointer">
                        <div class="font-semibold text-xs text-slate-800 flex items-center">
                            <i data-lucide="shield" class="w-3.5 h-3.5 text-indigo-600 mr-1.5"></i>
                            Administrador
                        </div>
                        <div class="text-[11px] text-slate-500 truncate">admin@prestamos...</div>
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Sistema de Control de Préstamos Institucional.
        </p>
    </div>

    <script>
        function fillCredentials(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
