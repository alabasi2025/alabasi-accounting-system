<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - نظام الأباسي المحاسبي</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Cairo', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
        }
        
        .form-input {
            padding-right: 40px;
        }
        
        .step-indicator {
            transition: all 0.3s ease;
        }
        
        .step-active {
            background: #667eea;
            color: white;
        }
        
        .step-completed {
            background: #10b981;
            color: white;
        }
        
        .step-inactive {
            background: #e5e7eb;
            color: #9ca3af;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    
    <div class="login-card rounded-2xl shadow-2xl p-8 w-full max-w-md">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
                <i class="fas fa-user-shield text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">نظام الأباسي المحاسبي</h1>
            <p class="text-gray-600">تسجيل الدخول إلى لوحة التحكم</p>
        </div>

        <!-- Step Indicators -->
        <div class="flex justify-between mb-8">
            <div class="flex items-center gap-2">
                <div id="step1-indicator" class="step-indicator step-active w-10 h-10 rounded-full flex items-center justify-center font-bold">1</div>
                <span id="step1-text" class="text-sm font-semibold text-gray-700">الوحدة</span>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-2 mt-5"></div>
            <div class="flex items-center gap-2">
                <div id="step2-indicator" class="step-indicator step-inactive w-10 h-10 rounded-full flex items-center justify-center font-bold">2</div>
                <span id="step2-text" class="text-sm text-gray-400">المؤسسة</span>
            </div>
            <div class="flex-1 h-1 bg-gray-300 mx-2 mt-5"></div>
            <div class="flex items-center gap-2">
                <div id="step3-indicator" class="step-indicator step-inactive w-10 h-10 rounded-full flex items-center justify-center font-bold">3</div>
                <span id="step3-text" class="text-sm text-gray-400">الدخول</span>
            </div>
        </div>

        <!-- Error Messages -->
        @if(session('error'))
            <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <!-- Step 1: Select Unit -->
            <div id="step1" class="step-content">
                <div class="mb-6">
                    <label for="unit_id" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-building ml-2 text-blue-600"></i>
                        اختر الوحدة
                    </label>
                    <select name="unit_id" id="unit_id" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        <option value="">-- اختر الوحدة --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" onclick="goToStep2()" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg">
                    <i class="fas fa-arrow-left ml-2"></i>
                    التالي
                </button>
            </div>

            <!-- Step 2: Select Company -->
            <div id="step2" class="step-content hidden">
                <div class="mb-6">
                    <label for="company_id" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-briefcase ml-2 text-green-600"></i>
                        اختر المؤسسة
                    </label>
                    <select name="company_id" id="company_id"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                        <option value="">-- اختر المؤسسة --</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-2" id="company_note">
                        <i class="fas fa-info-circle ml-1"></i>
                        اختر الوحدة أولاً لعرض المؤسسات التابعة لها
                    </p>
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="goToStep1()" 
                            class="flex-1 bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded-lg hover:bg-gray-400 transition-all duration-300">
                        <i class="fas fa-arrow-right ml-2"></i>
                        السابق
                    </button>
                    <button type="button" onclick="goToStep3()" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg">
                        <i class="fas fa-arrow-left ml-2"></i>
                        التالي
                    </button>
                </div>
            </div>

            <!-- Step 3: Login Credentials -->
            <div id="step3" class="step-content hidden">
                <!-- Email -->
                <div class="mb-6 relative">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-envelope ml-2 text-orange-600"></i>
                        البريد الإلكتروني
                    </label>
                    <input type="email" name="email" id="email" required
                           placeholder="example@alabasi.es"
                           class="form-input w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    <i class="fas fa-at input-icon"></i>
                </div>

                <!-- Password -->
                <div class="mb-6 relative">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-lock ml-2 text-red-600"></i>
                        كلمة المرور
                    </label>
                    <input type="password" name="password" id="password" required
                           placeholder="••••••••"
                           class="form-input w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition-colors">
                    <i class="fas fa-key input-icon"></i>
                </div>

                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="ml-2 w-4 h-4 text-blue-600">
                    <label for="remember" class="text-gray-700 cursor-pointer">تذكرني</label>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button type="button" onclick="goToStep2()" 
                            class="flex-1 bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded-lg hover:bg-gray-400 transition-all duration-300">
                        <i class="fas fa-arrow-right ml-2"></i>
                        السابق
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg">
                        <i class="fas fa-sign-in-alt ml-2"></i>
                        تسجيل الدخول
                    </button>
                </div>
            </div>

        </form>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <i class="fab fa-laravel text-red-500"></i> Laravel 12.39.0 | 
            نظام الأباسي المحاسبي v2.0
        </div>
    </div>

    <script>
        let currentStep = 1;
        const units = @json($units);
        const companies = @json($companies ?? []);

        // Load companies when unit is selected
        document.getElementById('unit_id').addEventListener('change', function() {
            const unitId = this.value;
            const companySelect = document.getElementById('company_id');
            const companyNote = document.getElementById('company_note');
            
            // Clear previous options
            companySelect.innerHTML = '<option value="">-- اختر المؤسسة --</option>';
            
            if (unitId) {
                // Filter companies by unit
                const unitCompanies = companies.filter(c => c.unit_id == unitId);
                
                if (unitCompanies.length > 0) {
                    unitCompanies.forEach(company => {
                        const option = document.createElement('option');
                        option.value = company.id;
                        option.textContent = company.name;
                        companySelect.appendChild(option);
                    });
                    companyNote.innerHTML = '<i class="fas fa-check-circle ml-1 text-green-600"></i> تم تحميل ' + unitCompanies.length + ' مؤسسة';
                } else {
                    companyNote.innerHTML = '<i class="fas fa-info-circle ml-1 text-orange-600"></i> لا توجد مؤسسات في هذه الوحدة';
                }
            } else {
                companyNote.innerHTML = '<i class="fas fa-info-circle ml-1"></i> اختر الوحدة أولاً لعرض المؤسسات التابعة لها';
            }
        });

        function goToStep1() {
            showStep(1);
        }

        function goToStep2() {
            const unitId = document.getElementById('unit_id').value;
            if (!unitId) {
                alert('الرجاء اختيار الوحدة أولاً');
                return;
            }
            showStep(2);
        }

        function goToStep3() {
            const companyId = document.getElementById('company_id').value;
            const companySelect = document.getElementById('company_id');
            
            // Check if company selection is required
            if (companySelect.options.length > 1 && !companyId) {
                alert('الرجاء اختيار المؤسسة');
                return;
            }
            showStep(3);
        }

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
            
            // Show current step
            document.getElementById('step' + step).classList.remove('hidden');
            
            // Update indicators
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById('step' + i + '-indicator');
                const text = document.getElementById('step' + i + '-text');
                
                if (i < step) {
                    indicator.className = 'step-indicator step-completed w-10 h-10 rounded-full flex items-center justify-center font-bold';
                    indicator.innerHTML = '<i class="fas fa-check"></i>';
                    text.className = 'text-sm font-semibold text-green-600';
                } else if (i === step) {
                    indicator.className = 'step-indicator step-active w-10 h-10 rounded-full flex items-center justify-center font-bold';
                    indicator.textContent = i;
                    text.className = 'text-sm font-semibold text-gray-700';
                } else {
                    indicator.className = 'step-indicator step-inactive w-10 h-10 rounded-full flex items-center justify-center font-bold';
                    indicator.textContent = i;
                    text.className = 'text-sm text-gray-400';
                }
            }
            
            currentStep = step;
        }
    </script>

</body>
</html>
