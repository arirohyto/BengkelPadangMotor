<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bengkel Padang Motor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            /* Primitive Tokens */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            --orange-50: #fff7ed;
            --orange-500: #f97316;
            --orange-600: #ea580c;
            --orange-700: #c2410c;

            --red-50: #fef2f2;
            --red-500: #ef4444;
            --red-600: #dc2626;

            --green-50: #f0fdf4;
            --green-600: #16a34a;

            /* Semantic Tokens */
            --bg-body: var(--gray-50);
            --bg-surface: #ffffff;
            
            --text-primary: var(--gray-900);
            --text-secondary: var(--gray-700);
            --text-muted: var(--gray-500);
            
            --brand-primary: var(--orange-600);
            --brand-primary-hover: var(--orange-700);
            
            --border-color: var(--gray-300);
            --border-focus: var(--orange-500);

            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
        }

        .login-layout {
            display: flex;
            width: 100%;
            max-width: 960px;
            background: var(--bg-surface);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            min-height: 560px;
        }

        /* ── Brand Panel (Left) ── */
        .brand-panel {
            flex: 1;
            background-color: var(--brand-primary);
            background-image: linear-gradient(135deg, var(--orange-500), var(--orange-700));
            padding: 3rem;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        @media (max-width: 768px) {
            .brand-panel {
                display: none;
            }
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        
        .brand-icon {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.625rem;
            border-radius: 8px;
            display: flex;
        }

        .brand-hero {
            margin-top: -2rem;
        }

        .brand-hero h1 {
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
        }

        .brand-hero p {
            font-size: 1rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
        }

        .brand-footer {
            font-size: 0.875rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
        }

        /* ── Form Panel (Right) ── */
        .form-panel {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 480px) {
            .form-panel {
                padding: 2rem 1.5rem;
            }
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .form-header p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.375rem;
        }

        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            box-shadow: var(--shadow-sm);
            transition: all 0.15s ease-in-out;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--orange-50);
        }

        .form-control.is-invalid {
            border-color: var(--red-500);
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px var(--red-50);
        }

        .password-toggle {
            position: absolute;
            right: 0.875rem;
            bottom: 0.5rem;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 1rem;
            padding: 0.25rem;
        }

        .password-toggle:hover {
            color: var(--text-primary);
        }

        /* Checkbox & Links */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: var(--brand-primary);
            border-radius: 4px;
            border: 1px solid var(--border-color);
            cursor: pointer;
        }

        .checkbox-wrapper label {
            color: var(--text-secondary);
            cursor: pointer;
            font-weight: 500;
        }

        .link-text {
            color: var(--brand-primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .link-text:hover {
            text-decoration: underline;
        }

        /* Button */
        .btn-primary {
            width: 100%;
            padding: 0.625rem 1rem;
            background-color: var(--brand-primary);
            color: white;
            border: 1px solid transparent;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: background-color 0.15s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background-color: var(--brand-primary-hover);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 3px var(--orange-50);
        }

        /* Alerts */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-weight: 500;
        }
        
        .alert i {
            font-size: 1rem;
            margin-top: 0.125rem;
        }

        .alert-error {
            background-color: var(--red-50);
            color: var(--red-600);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-success {
            background-color: var(--green-50);
            color: var(--green-600);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        /* ── Modals (Native HTML Dialog) ── */
        dialog {
            margin: auto;
            border: none;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            padding: 0;
            width: calc(100% - 2rem);
            max-width: 440px;
            background: var(--bg-surface);
            color: var(--text-primary);
        }

        dialog::backdrop {
            background: rgba(17, 24, 39, 0.6);
        }

        .dialog-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dialog-header h3 {
            font-size: 1.125rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .dialog-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            color: var(--text-muted);
            cursor: pointer;
        }
        
        .dialog-close:hover {
            color: var(--text-primary);
        }

        .dialog-body {
            padding: 1.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            color: var(--text-secondary);
        }

        .dialog-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            background-color: var(--gray-50);
        }

        .btn-secondary {
            padding: 0.5rem 1rem;
            background-color: var(--bg-surface);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: all 0.15s ease-in-out;
        }

        .btn-secondary:hover {
            background-color: var(--gray-50);
            border-color: var(--gray-300);
        }
    </style>
</head>
<body>

    <div class="login-layout">
        {{-- ── Brand Panel ── --}}
        <div class="brand-panel">
            <div class="brand-logo">
                <span class="brand-icon"><i class="bi bi-gear-wide-connected"></i></span>
                Bengkel Padang Motor
            </div>
            
            <div class="brand-hero">
                <h1>Sistem Manajemen Operasional.</h1>
                <p>Kelola data pelanggan, pantau stok sparepart, dan lacak riwayat servis kendaraan secara efisien dan akurat.</p>
            </div>

            <div class="brand-footer">
                <i class="bi bi-shield-lock me-1"></i> Area Terbatas Staf Internal
            </div>
        </div>

        {{-- ── Form Panel ── --}}
        <div class="form-panel">
            <div class="form-header">
                <h2>Akses Sistem</h2>
                <p>Silakan masuk dengan kredensial Anda</p>
            </div>

            {{-- Flash: success --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- Flash: validation error --}}
            @if($errors->any())
                <div class="alert alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input
                        type="text"
                        class="form-control @error('username') is-invalid @enderror"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Misal: admin"
                        autocomplete="username"
                        autofocus
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan password">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>

                <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Ingat Saya</label>
                    </div>
                    
                    <a href="javascript:void(0)" onclick="document.getElementById('forgotDialog').showModal()" class="link-text">
                        Lupa Password?
                    </a>
                </div>

                <button type="submit" class="btn-primary">
                    Masuk ke Dashboard <i class="bi bi-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Native HTML Dialog for Forgot Password --}}
    <dialog id="forgotDialog">
        <div class="dialog-header">
            <h3><i class="bi bi-shield-lock" style="color: var(--brand-primary);"></i> Informasi Keamanan</h3>
            <button class="dialog-close" onclick="document.getElementById('forgotDialog').close()">&times;</button>
        </div>
        <div class="dialog-body">
            Sistem ini merupakan portal internal. Sesuai protokol keamanan bengkel, fitur reset kata sandi mandiri tidak diaktifkan.
            <br><br>
            Jika Anda melupakan kata sandi, silakan hubungi <strong>Administrator Sistem</strong> atau Kepala Bengkel untuk mengatur ulang akses akun Anda.
        </div>
        <div class="dialog-footer">
            <button class="btn-secondary" onclick="document.getElementById('forgotDialog').close()">Tutup</button>
        </div>
    </dialog>

    <script>
        // Password toggle
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                toggleBtn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
        
        // Close dialog on click outside
        const dialog = document.getElementById('forgotDialog');
        dialog.addEventListener('click', (e) => {
            const dialogDimensions = dialog.getBoundingClientRect();
            if (
                e.clientX < dialogDimensions.left ||
                e.clientX > dialogDimensions.right ||
                e.clientY < dialogDimensions.top ||
                e.clientY > dialogDimensions.bottom
            ) {
                dialog.close();
            }
        });
    </script>
</body>
</html>
