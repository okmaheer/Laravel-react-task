import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import './LoginRegister.css'; // Import specific CSS for login

function Login() {
  const [email, setEmail] = useState('maheer@gmail.com');
  const [password, setPassword] = useState('123456');
  const navigate = useNavigate();

  const handleLogin = (e) => {
    e.preventDefault();
    // Dummy login logic, can be replaced with API call
    if (email === 'maheer@gmail.com' && password === '123456') {
      navigate('/leads');
    } else {
      alert('Invalid login credentials');
    }
  };

  return (
    <div className="login-container"> {/* Add a unique class name */}

      <div className="login-form-box"> {/* Add unique class names */}
      <p className="info-message">
          This app has cache implemented, sorting implemented, unit tests written. 
          Authentication is missing because the document did not specify whether we could modify the database or not.
        </p>
        <h2>Login</h2>
        <form onSubmit={handleLogin}>
          <input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            className="login-input"  
          />
          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            className="login-input"  
          />
          <button type="submit" className="login-button">Login</button> {/* Unique class */}
        </form>
        <a href="/register" className="login-register-link">Don't have an account? Register</a>
      </div>
    </div>
  );
}

export default Login;
