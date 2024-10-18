import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Login from './components/Login';
import Register from './components/Register';
import Leads from './components/Leads';
import LeadFormPage from './components/LeadFormPage'; // The page for creating/updating a lead


function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/leads" element={<Leads />} />
        <Route path="/leads/create" element={<LeadFormPage />} />  {/* Route for creating a lead */}
        <Route path="/leads/edit/:id"  element={<LeadFormPage />} /> 
      </Routes>
    </Router>
  );
}

export default App;
