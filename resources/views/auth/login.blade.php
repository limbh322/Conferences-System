@extends('layout.app')

@section('title', 'Login / Register')

@section('content')
<style>
body, html {
  margin: 0;
  font-family: "Segoe UI", Roboto, Arial, sans-serif;
  height: 100%;
  background: #f5f7fa;
  overflow: hidden;
}

/* Slim fixed navbar */
nav.navbar {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 60px;
  background-color: #1f2937;
  display: flex;
  align-items: center;
  padding: 0 30px;
  color: white;
  font-size: 18px;
  font-weight: 500;
  z-index: 100;
}

/* Full-page color overlay */
.color-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #3b82f6; /* default blue */
  z-index: -1;
  transition: background-color 0.8s ease;
}

/* Auth container */
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 90%;
  max-width: 1000px;
  margin: 100px auto;
  min-height: 70vh;
  position: relative;
}

/* Panels */
.panel {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}

/* Buttons */
.panel-button {
  padding: 15px 30px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  border: none;
  color: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  transition: 0.3s ease;
}
.panel-button:hover { opacity: 0.9; }
.panel-button.d-none { display: none !important; }

/* Forms card */
.auth-form {
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
  padding: 40px 30px;
  width: 100%;
  max-width: 380px;
  transition: all 0.5s ease;
  z-index: 2;
}

/* Form headings */
.auth-form h2 {
  margin-bottom: 25px;
  font-weight: 600;
  color: #1f2937;
  text-align: center;
}

/* Input fields */
.auth-form input {
  width: 100%;
  padding: 12px 15px;
  margin-bottom: 18px;
  border-radius: 6px;
  border: 1px solid #cbd5e1;
  outline: none;
  font-size: 14px;
  transition: 0.3s;
}

.auth-form input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 5px rgba(59,130,246,0.3);
}

/* Form buttons */
.auth-form button {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
}

/* Login and Register Button Colors */
#btnLogin { background-color: #2563eb; }   /* Deep Blue */
#btnRegister { background-color: #3b82f6; } /* Lighter Blue */

#loginForm button {
  background-color: #2563eb;
  color: white;
}
#loginForm button:hover {
  background-color: #1d4ed8;
}

#registerForm button {
  background-color: #3b82f6;
  color: white;
}
#registerForm button:hover {
  background-color: #2563eb;
}

/* Hide forms initially */
.auth-form.d-none { display: none; }

/* Footer */
footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  text-align: center;
  color: white;
  background: transparent;
  padding: 10px 0;
  font-size: 14px;
  letter-spacing: 0.3px;
  z-index: 50;
  opacity: 0.85;
}

/* Responsive */
@media (max-width: 768px) {
  .auth-container { flex-direction: column; }
  .panel { margin-bottom: 30px; }
}
</style>

<div class="color-overlay" id="colorOverlay"></div>
<nav class="navbar">
  Conference Paper Submission
</nav>

<div class="auth-container">

  <!-- Left Panel: Login -->
  <div class="panel left-panel">
    <button class="panel-button" id="btnLogin" onclick="showLogin()">Login</button>
    <div class="auth-form d-none" id="loginForm">
      <h2>Login</h2>
      <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
      </form>
    </div>
  </div>

  <!-- Right Panel: Register -->
  <div class="panel right-panel">
    <button class="panel-button" id="btnRegister" onclick="showRegister()">Register</button>
    <div class="auth-form d-none" id="registerForm">
      <h2>Register</h2>
      <form method="POST" action="{{ route('user.store') }}">
        @csrf
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        <button type="submit">Sign Up</button>
      </form>
    </div>
  </div>

</div>

<script>
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const btnLogin = document.getElementById('btnLogin');
const btnRegister = document.getElementById('btnRegister');
const colorOverlay = document.getElementById('colorOverlay');

function showLogin() {
  loginForm.classList.remove('d-none');
  registerForm.classList.add('d-none');
  btnLogin.classList.add('d-none');
  btnRegister.classList.remove('d-none');
  colorOverlay.style.backgroundColor = '#2563eb'; // Deep blue
}

function showRegister() {
  registerForm.classList.remove('d-none');
  loginForm.classList.add('d-none');
  btnRegister.classList.add('d-none');
  btnLogin.classList.remove('d-none');
  colorOverlay.style.backgroundColor = '#3b82f6'; // Lighter blue
}

// Default: login form
showLogin();

// Auto switch to login after registration success
@if(session('success'))
  showLogin();
@endif
</script>

<footer>
  <span style="color: #6b7280;">© {{ date('Y') }} Conference Paper Submission System. All rights reserved.</span>
</footer>


@endsection
