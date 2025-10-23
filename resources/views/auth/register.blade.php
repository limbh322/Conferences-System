@extends('layout.app')

@section('title', 'Register')

@section('content')
<style>
  .auth-container {
    display: flex;
    min-height: 80vh;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  }
  .left-half {
    flex: 1;
    background-color: #2196F3;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    padding: 40px;
  }
  .right-half {
    flex: 1;
    background-color: #4CAF50;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    padding: 40px;
  }
  .auth-form {
    width: 100%;
    max-width: 350px;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    color: #333;
  }
  .toggle-btn {
    margin-top: 10px;
    width: 100%;
    background: transparent;
    border: 2px solid #333;
    font-weight: bold;
    cursor: pointer;
  }
</style>

<div class="auth-container">
  <div class="left-half">
    <div class="auth-form">
      <h2>Register</h2>
      <form method="POST" action="{{ route('user.store') }}">
        @csrf
        <div class="mb-3">
          <label>Name</label>
          <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Confirm Password</label>
          <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-select" required>
            <option value="">Select Role</option>
            <option value="Admin">Admin</option>
            <option value="Author">Author</option>
            <option value="Reviewer">Reviewer</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
      </form>
      <a href="{{ route('login') }}" class="btn toggle-btn">Sign In</a>
    </div>
  </div>
  <div class="right-half">
    <h1>Welcome!</h1>
  </div>
</div>
@endsection
