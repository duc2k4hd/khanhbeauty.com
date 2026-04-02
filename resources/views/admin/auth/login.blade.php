<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản Trị Hệ Thống - Khánh Beauty</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Quicksand:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #d4a373;
            --secondary: #e5b0b3;
            --dark: #222;
            --bg-color: #fafafa;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Quicksand', sans-serif; }
        body {
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at top right, rgba(229,176,179,0.1) 0%, transparent 40%),
                              radial-gradient(circle at bottom left, rgba(212,163,115,0.1) 0%, transparent 40%);
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
        }
        .login-card {
            background: #fff; width: 100%; max-width: 420px;
            padding: 40px; border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05); text-align: center;
        }
        .logo { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; margin-bottom: 5px; color: var(--dark); letter-spacing: 2px;}
        .logo span { color: var(--secondary); font-weight: 400; }
        .subtitle { color: #666; font-size: 14px; margin-bottom: 30px; letter-spacing: 1px; }

        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;}
        .form-group input { 
            width: 100%; padding: 14px; border: 1.5px solid #eaeaea; 
            border-radius: 8px; font-size: 15px; outline: none; transition: all 0.3s;
        }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(212,163,115,0.1); }
        
        .error-message { color: #e74c3c; font-size: 13px; margin-top: 5px; }
        .alert-error {
            background: #fdf0f0; color: #e74c3c; padding: 12px; border-radius: 8px;
            font-size: 14px; margin-bottom: 20px; border: 1px solid #fadcdc;
        }
        
        .btn-submit {
            width: 100%; padding: 15px; background: var(--dark); color: #fff;
            border: none; border-radius: 8px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: 0.3s; margin-top: 10px; letter-spacing: 1px;
        }
        .btn-submit:hover { background: var(--primary); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(212,163,115,0.2); }
        
        .remember-wrap {
            display: flex; align-items: center; justify-content: flex-start; gap: 8px;
            margin-bottom: 25px; cursor: pointer;
        }
        .remember-wrap input { width: 16px; height: 16px; cursor: pointer; accent-color: var(--dark); }
        .remember-wrap label { font-size: 14px; color: #555; cursor: pointer; user-select: none; margin: 0; text-transform: none;font-weight: 500;}

    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">KHÁNH <span>BEAUTY</span></div>
        <div class="subtitle">Hệ thống quản trị Administration</div>

        @if (session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Tài khoản Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@khanhbeauty.com" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group remember-wrap">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Ghi nhớ đăng nhập</label>
            </div>

            <button type="submit" class="btn-submit">ĐĂNG NHẬP HỆ THỐNG</button>
        </form>
    </div>
</body>
</html>
