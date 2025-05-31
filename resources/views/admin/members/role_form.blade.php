<!-- Role Update Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Update Role</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('members.updateRole', $member->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="member" {{ $member->user_role === 'member' ? 'selected' : '' }}>Member</option>
                    <option value="Secretary" {{ $member->user_role === 'Secretary' ? 'selected' : '' }}>Secretary</option>
                    <option value="Treasurer" {{ $member->user_role === 'Treasurer' ? 'selected' : '' }}>Treasurer</option>
                    <option value="Auditor" {{ $member->user_role === 'Auditor' ? 'selected' : '' }}>Auditor</option>
                    <option value="PIO" {{ $member->user_role === 'PIO' ? 'selected' : '' }}>PIO</option>
                    <option value="BM" {{ $member->user_role === 'BM' ? 'selected' : '' }}>Business Manager</option>
                    <option value="superadmin" {{ $member->user_role === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>

            <div class="mb-3">
                <small class="text-muted">
                    <strong>Role Permissions:</strong><br>
                    • <strong>Secretary:</strong> Manage members, events, announcements, and reports<br>
                    • <strong>Treasurer/Auditor:</strong> Manage payments only<br>
                    • <strong>Business Manager:</strong> Manage payments only<br>
                    • <strong>PIO:</strong> Manage events and announcements only<br>
                    • <strong>Super Admin:</strong> Full access to all features<br>
                    • <strong>Member:</strong> Basic access only
                </small>
            </div>

            <button type="submit" class="btn btn-primary">Update Role</button>
        </form>
    </div>
</div>
