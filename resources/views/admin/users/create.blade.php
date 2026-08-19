@if(auth()->user()->primary_location_code === 'VISA')
    @include('partials.visa-header')
@elseif(auth()->user()->primary_location_code === 'ICT')
    @include('partials.ict-header')
@else
    @include('partials.header3')
@endif
    <!-- ─── Page Content ─── -->
    <main class="redas-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Add New User</h1>
                <p class="page-subtitle">
                    Register a new user account with role privileges &bull; {{ now()->format('l, d F Y') }}
                </p>
            </div>
            <div>
                <a href="{{ route('admin.users') }}" class="btn-nis btn-ghost">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>

        <style>
        .redas-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            overflow: hidden;
        }
        .redas-card:hover {
            box-shadow: 0 8px 12px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
        }
        .ni {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            background-color: #ffffff;
            color: #1e293b;
        }
        .ni:focus {
            border-color: var(--nis-600, #0B6B3A);
            box-shadow: 0 0 0 3px rgba(11, 107, 58, 0.15);
            outline: none;
        }
        .report-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }
        .btn-nis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.9rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }
        .btn-primary-nis {
            background-color: var(--nis-600, #0B6B3A);
            color: #ffffff;
            border: 1px solid var(--nis-700, #09552E);
        }
        .btn-primary-nis:hover {
            background-color: var(--nis-700, #09552E);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-ghost {
            background-color: transparent;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn-ghost:hover {
            background-color: #f8fafc;
            color: #1e293b;
            border-color: #94a3b8;
        }
        </style>

        @if ($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;padding:12px;border-radius:8px;margin-bottom:20px;font-size:0.9rem;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="redas-card animate-fade-up" style="max-width: 600px; margin: 0 auto;">
            <div class="card-head">
                <div class="card-head-title">
                    <div class="card-head-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-user-plus"></i></div>
                    User Credentials &amp; Role Details
                </div>
            </div>
            
            <form action="{{ route('admin.users.store') }}" method="POST" style="padding: 24px; display: grid; gap: 18px;">
                @csrf
                
                <div class="report-group">
                    <label class="report-label">Full Name</label>
                    <input type="text" name="name" class="ni" required placeholder="e.g. John Doe" autocomplete="off">
                </div>

                <div class="report-group">
                    <label class="report-label">Email Address</label>
                    <input type="email" name="email" class="ni" required placeholder="e.g. j.doe@nis.gov.ng" autocomplete="off">
                </div>

                <div class="report-group">
                    <label class="report-label">Service Number</label>
                    <input type="text" name="service_number" class="ni" required placeholder="e.g. 12345" autocomplete="off">
                </div>

                <div class="report-group">
                    <label class="report-label">Password</label>
                    <input type="password" name="password" class="ni" required placeholder="Min 6 characters" autocomplete="new-password">
                </div>

                <div class="report-group">
                    <label class="report-label">Role</label>
                    <select name="role" class="ni" required>
                        <option value="officer">Desk Officer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn-nis btn-primary-nis" style="width: 100%; margin-top: 10px;">
                    <i class="fas fa-check-circle"></i> Create User Account
                </button>
            </form>
        </div>

    </main>
@if(auth()->user()->primary_location_code === 'VISA')
    @include('partials.footer')
@elseif(auth()->user()->primary_location_code === 'ICT')
    @include('partials.footer')
@else
    @include('partials.footer3')
@endif
