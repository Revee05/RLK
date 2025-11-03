<div class="form-group">
    {{ Form::label('name', 'Name') }}
    {{ Form::text('name', $user->name ?? '', array('class' => 'form-control form-control-sm ','placeholder' => 'Nama')) }}
</div>
<div class="form-group">
    {{ Form::label('name', 'Email') }}
    {{ Form::text('email', $user->email ?? '', array('class' => 'form-control form-control-sm','placeholder' => 'Email')) }}
</div>
@if(Auth::user()->access == 'admin')
    <div class="form-group">
        {{ Form::label('name', 'Access') }}<br> 
        <select class="form-control form-control-sm" name="access">
            <option>Pilih Hak Akses</option>
            <option value="admin" @if(isset($user->access)  && $user->access == 'admin') selected @endif>Admin</option>
            <option value="member" @if(isset($user->access)  && $user->access == 'member') selected @endif>Member</option>
        </select>
    </div>
@else
    <input type="hidden" name="access" value="{{Auth::user()->access}}">
@endif

<div class="form-group">
    {{ Form::label('name', 'Password') }}<br>
    {{ Form::password('password', array('class' => 'form-control form-control-sm','placeholder' => 'Password')) }}
</div>
<div class="form-group">
    {{ Form::label('name', 'Konfirmasi Password') }}<br>
    {{ Form::password('password_confirmation', array('class' => 'form-control form-control-sm','placeholder' => 'Konfirmasi password')) }}
</div>

