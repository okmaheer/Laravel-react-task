import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useParams, useNavigate } from 'react-router-dom'; // Corrected here
import './LeadFormPage.css'; // Assuming you create this file for styling

function LeadFormPage() {
  const { id } = useParams(); // Get lead ID from URL for editing
  const navigation = useNavigate(); // To navigate back to the leads page
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [statusId, setStatusId] = useState('');
  const [statuses, setStatuses] = useState([]); // Available statuses for dropdown
  const [loading, setLoading] = useState(false);
  const [isEditing, setIsEditing] = useState(false); // Flag to check if it's editing mode

  // Fetch statuses for the dropdown
  useEffect(() => {
    axios.get('http://localhost:8000/api/lead-statuses')
      .then(response => setStatuses(response.data))
      .catch(error => console.error('Error fetching lead statuses:', error));
  }, []);

  // Fetch the lead data if an ID is provided (for editing)
  useEffect(() => {
    if (id) {
      setIsEditing(true);
      axios.get(`http://localhost:8000/api/leads/edit/${id}`)
        .then(response => {
          const lead = response.data;
          setName(lead.name);
          setEmail(lead.email);
          setPhone(lead.phone);
          setStatusId(lead.lead_status_id);
        })
        .catch(error => console.error('Error fetching lead:', error));
    }
  }, [id]);

  // Handle form submission for create/update
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    const leadData = {
      name,
      email,
      phone,
      lead_status_id: statusId,
    };

    try {
      if (isEditing) {
        // Update existing lead
        await axios.put(`http://localhost:8000/api/leads/${id}`, leadData);
      } else {
        // Create new lead
        await axios.post('http://localhost:8000/api/leads', leadData);
      }
      navigation('/leads'); // Navigate back to the leads page after saving
    } catch (error) {
      console.error('Error saving lead:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="lead-form-container">
      <h2>{isEditing ? 'Update Lead' : 'Create Lead'}</h2>
      <form onSubmit={handleSubmit} className="lead-form">
        <div className="form-group">
          <label>Name</label>
          <input
            type="text"
            value={name}
            onChange={(e) => setName(e.target.value)}
            required
            className="form-input"
          />
        </div>

        <div className="form-group">
          <label>Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            className="form-input"
          />
        </div>

        <div className="form-group">
          <label>Phone</label>
          <input
            type="text"
            value={phone}
            onChange={(e) => setPhone(e.target.value)}
            required
            className="form-input"
          />
        </div>

        <div className="form-group">
          <label>Status</label>
          <select
            value={statusId}
            onChange={(e) => setStatusId(e.target.value)}
            required
            className="form-select"
          >
            <option value="">Select Status</option>
            {statuses.map((status) => (
              <option key={status.id} value={status.id}>
                {status.name}
              </option>
            ))}
          </select>
        </div>

        <div className="form-actions">
          <button type="submit" disabled={loading} className="btn btn-primary">
            {loading ? 'Saving...' : 'Save'}
          </button>
          <button
            type="button"
            onClick={() => navigation('/leads')}
            className="btn btn-secondary"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  );
}

export default LeadFormPage;
