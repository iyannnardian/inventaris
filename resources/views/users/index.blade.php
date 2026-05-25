<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - StockFlow SPPG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; overflow-x: hidden; }
        .sidebar { height: 100vh; background-color: #1a1d2d; color: #fff; position: fixed; width: 250px; z-index: 100; }
        .sidebar-brand { padding: 20px; font-weight: bold; font-size: 1.2rem; }
        .sidebar-user { padding: 15px 20px; background-color: #24283b; margin-bottom: 20px; }
        .nav-link { color: #a9b1d6; padding: 12px 20px; }
        .nav-link:hover, .nav-link.active { background-color: #414868; color: #fff; border-radius: 5px; margin: 0 10px; }
        .main-content { margin-left: 250px; padding: 20px; min-height: 100vh; }
        .header-banner { background-color: #1a1d2d; color: white; padding: 20px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; }
        .data-card { background: white; border-radius: 10px; padding: 20px; margin-top: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
        .btn-action { font-size: 0.85rem; padding: 5px 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-box-seam"></i> StockFlow
            <div class="fs-6 fw-normal text-secondary">Sistem Kelola Barang</div>
        </div>
        
        <div class="sidebar-user">
            <div><i class="bi bi-person-circle"></i> {{ Auth::user()->name }}</div>
            <span class="badge bg-warning text-dark mt-1">{{ ucfirst(Auth::user()->role) }}</span>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a>
            <a class="nav-link" href="{{ route('kategori.index') }}"><i class="bi bi-tags me-2"></i> Kategori Barang</a>
            <a class="nav-link" href="{{ route('barang.index') }}"><i class="bi bi-box me-2"></i> Daftar Barang</a>
            <a class="nav-link" href="{{ route('transaksi.index') }}"><i class="bi bi-arrow-left-right me-2"></i> Transaksi</a>
            <a class="nav-link" href="{{ route('laporan.index') }}"><i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Stok</a>
            <a class="nav-link" href="{{ route('supplier.index') }}"><i class="bi bi-truck me-2"></i> Supplier</a>
            <a class="nav-link active" href="{{ route('users.index') }}"><i class="bi bi-people me-2"></i> Kelola User</a>
        </nav>

        <div class="position-absolute bottom-0 w-100 p-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100 text-start border-0"><i class="bi bi-box-arrow-left me-2"></i> Keluar Sistem</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <!-- Notifikasi Status -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Gagal memproses data:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="header-banner">
            <div>
                <h3 class="mb-1"><i class="bi bi-people"></i> Kelola Pengguna</h3>
                <small class="text-secondary">Daftar akun pengguna sistem dan kewenangannya</small>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-person-plus"></i> Tambah Pengguna</button>
        </div>

        <div class="data-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="bi bi-card-list"></i> Akun Pengguna <span class="badge bg-secondary">{{ $users->count() }} Pengguna</span></h6>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th style="width: 25%">Nama Lengkap</th>
                            <th style="width: 25%">Alamat Email</th>
                            <th style="width: 20%">Peran / Role</th>
                            <th style="width: 15%">Tanggal Dibuat</th>
                            <th style="width: 10%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">
                                {{ $user->name }}
                                @if(Auth::id() == $user->id)
                                    <span class="badge bg-info-subtle text-info ms-1 border border-info-subtle">Anda</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-danger text-white text-uppercase"><i class="bi bi-shield-fill"></i> Admin</span>
                                @elseif($user->role == 'ahli gizi')
                                    <span class="badge bg-success text-white text-uppercase"><i class="bi bi-activity"></i> Ahli Gizi</span>
                                @else
                                    <span class="badge bg-primary text-white text-uppercase"><i class="bi bi-egg-fried"></i> Kepala Dapur</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-warning btn-action text-white btn-edit" 
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEdit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    
                                    @if(Auth::id() != $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini dari sistem?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-action text-white"><i class="bi bi-trash"></i> Hapus</button>
                                        </form>
                                    @else
                                        <button class="btn btn-light btn-action text-muted" disabled title="Anda tidak dapat menghapus diri sendiri"><i class="bi bi-trash"></i> Hapus</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-person-plus"></i> Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama lengkap" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Contoh: user@sppg.com" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Peran / Role <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Peran --</option>
                                <option value="admin">Admin</option>
                                <option value="ahli gizi">Ahli Gizi</option>
                                <option value="kepala dapur">Kepala Dapur</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi (min. 6 karakter)" required autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-white border-0">
                    <h5 class="modal-title" id="modalEditLabel"><i class="bi bi-pencil-square"></i> Edit Data Pengguna</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditUser" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Peran / Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit_role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="ahli gizi">Ahli Gizi</option>
                                <option value="kepala dapur">Kepala Dapur</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Kata Sandi Baru</label>
                            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Masukkan kata sandi baru (kosongkan jika tidak diubah)" autocomplete="off">
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Biarkan kolom kata sandi kosong jika tidak ingin mengubahnya.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.btn-edit');
            const formEdit = document.getElementById('formEditUser');
            const inputName = document.getElementById('edit_name');
            const inputEmail = document.getElementById('edit_email');
            const selectRole = document.getElementById('edit_role');
            const inputPassword = document.getElementById('edit_password');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const role = this.getAttribute('data-role');

                    formEdit.setAttribute('action', `/users/${id}`);
                    inputName.value = name;
                    inputEmail.value = email;
                    selectRole.value = role;
                    inputPassword.value = ''; // Reset password input
                });
            });
        });
    </script>
</body>
</html>
