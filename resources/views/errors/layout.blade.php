{{-- resources/views/errors/layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Error') . ' - ' . __('Tamman'))</title>
    <meta name="description" content="@yield('description', __('Something went wrong'))">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,100..900&family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @stack('styles')
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        body.rtl {
            font-family: 'Cairo', 'Inter', system-ui, sans-serif;
            direction: rtl;
            text-align: right;
        }
        
        /* Shared Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.4s ease forwards;
        }
        
        /* Shared Error Card Styles */
        .error-container {
            width: 100%;
            max-width: 550px;
            margin: 0 auto;
        }
        
        .error-card {
            background: white;
            border-radius: 40px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            overflow: hidden;
        }
        
        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
        }
        
        /* Shared Error Code */
        .error-code {
            font-size: 5rem;
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #1f2937, #4b5563);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        /* Shared Error Title */
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
        }
        
        /* Shared Error Description */
        .error-description {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        /* Shared Button Styles */
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        
        .btn-back, .btn-home, .btn-dashboard, .btn-refresh, .btn-retry, .btn-check, .btn-support {
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
        }
        
        .btn-back {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-back:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }
        
        .btn-home {
            background: #7c3aed;
            color: white;
        }
        
        .btn-home:hover {
            background: #6d28d9;
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-dashboard {
            background: #10b981;
            color: white;
        }
        
        .btn-dashboard:hover {
            background: #059669;
            transform: translateY(-2px);
            color: white;
        }
        
        /* Shared Emergency Note */
        .emergency-note {
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        .emergency-note a {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 600;
        }
        
        .emergency-note a:hover {
            text-decoration: underline;
        }
        
        /* Shared Icon Styles */
        .error-icon {
            position: relative;
            display: inline-block;
            margin-bottom: 25px;
        }
        
        .error-icon .icon-bg {
            width: 100px;
            height: 100px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-icon .icon-bg i {
            font-size: 3rem;
            color: white;
        }
        
        .error-icon .icon-small {
            position: absolute;
            bottom: -10px;
            right: -10px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .error-icon .icon-small i {
            font-size: 1.2rem;
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .error-card {
                padding: 30px 20px;
            }
            
            .error-code {
                font-size: 3.5rem;
            }
            
            .error-title {
                font-size: 1.2rem;
            }
            
            .error-actions {
                flex-direction: column;
            }
            
            .btn-back, .btn-home, .btn-dashboard {
                justify-content: center;
            }
        }
        
        /* RTL Support */
        body.rtl .error-actions {
            flex-direction: row;
        }
        
        @media (max-width: 480px) {
            body.rtl .error-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="{{ app()->getLocale() === 'ar' ? 'rtl' : '' }}">
    @yield('content')
    
    @stack('scripts')
</body>
</html>