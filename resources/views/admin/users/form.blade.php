<div class="form-group">
    {{ Form::label('name', 'Name') }}
    {{ Form::text('name', old('name', $user->name ?? ''), ['class' => 'form-control form-control-sm ','placeholder' => 'Nama','autocomplete' => 'off']) }}
</div>
<div class="form-group">
    {{ Form::label('username', 'Username') }}
    {{ Form::text('username', old('username', $user->username ?? ''), ['class' => 'form-control form-control-sm','placeholder' => 'Username','autocomplete' => 'off']) }}
</div>
<div class="form-group">
    {{ Form::label('name', 'Email') }}
    {{ Form::text('email', old('email', $user->email ?? ''), ['class' => 'form-control form-control-sm','placeholder' => 'Email','autocomplete' => 'off']) }}
</div>
@if(Auth::user()->access == 'admin')
    <div class="form-group">
        {{ Form::label('name', 'Access') }}<br> 
        <select class="form-control form-control-sm" name="access">
            <option value="">Pilih Hak Akses</option>
            <option value="admin" @if(old('access', $user->access ?? '') == 'admin') selected @endif>Admin</option>
            <option value="member" @if(old('access', $user->access ?? '') == 'member') selected @endif>Member</option>
        </select>
    </div>
@else
    <input type="hidden" name="access" value="{{ old('access', Auth::user()->access) }}">
@endif

<div class="form-group">
    {{ Form::label('name', 'Password') }}<br>
    <div class="input-group">
        {{ Form::password('password', ['class' => 'form-control form-control-sm','placeholder' => 'Password','id' => 'password','autocomplete' => 'new-password']) }}
        <div class="input-group-append">
            <button class="btn btn-outline-secondary btn-sm toggle-password" type="button" data-target="#password">Show</button>
        </div>
    </div>
</div>
<div class="form-group">
    {{ Form::label('name', 'Konfirmasi Password') }}<br>
    <div class="input-group">
        {{ Form::password('password_confirmation', ['class' => 'form-control form-control-sm','placeholder' => 'Konfirmasi password','id' => 'password_confirmation','autocomplete' => 'new-password']) }}
        <div class="input-group-append">
            <button class="btn btn-outline-secondary btn-sm toggle-password" type="button" data-target="#password_confirmation">Show</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e){
    if(!e.target) return;
    if(e.target.classList && e.target.classList.contains('toggle-password')){
        var btn = e.target;
        var selector = btn.getAttribute('data-target');
        var input = document.querySelector(selector);
        if(!input) return;
        if(input.type === 'password'){
            input.type = 'text';
            btn.textContent = 'Hide';
        } else {
            input.type = 'password';
            btn.textContent = 'Show';
        }
    }
});
</script>

