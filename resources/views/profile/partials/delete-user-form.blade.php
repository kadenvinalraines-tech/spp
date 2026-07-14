<section>
    <p class="text-muted small mb-3">Setelah akun Anda dihapus, semua data dan informasi akan dihapus secara permanen. Pastikan Anda mengunduh data penting sebelum menghapus akun.</p>

    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        <i class="bi bi-trash3 me-1"></i> Hapus Akun
    </button>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header border-danger">
                        <h5 class="modal-title text-danger fw-bold">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Konfirmasi Hapus Akun
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Semua data Anda akan dihapus permanen. Masukkan password Anda untuk konfirmasi.</p>
                        <div class="mb-3">
                            <label for="delete_password" class="form-label">Password</label>
                            <input id="delete_password" name="password" type="password" class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif" placeholder="Masukkan password Anda">
                            @if($errors->userDeletion->has('password'))
                                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash3 me-1"></i> Ya, Hapus Akun Saya
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
