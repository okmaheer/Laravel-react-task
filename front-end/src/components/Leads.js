import React, { useMemo, useEffect, useState } from 'react';
import { useTable, usePagination, useSortBy } from 'react-table';
import axios from 'axios';
import './Leads.css';
import { useNavigate } from 'react-router-dom'; // Corrected here

function Leads() {
  const [data, setData] = useState([]);         // Holds the data from the API
  const [loading, setLoading] = useState(true); // Loading state
  const [error, setError] = useState(null);     // Error state
  const [pageCount, setPageCount] = useState(0); // Page count from API
  const [searchTerm, setSearchTerm] = useState(''); // Search term
  const [controlledSortBy, setControlledSortBy] = useState([{ id: 'id', desc: false }]); // Sorting
  const navigate = useNavigate(); // To navigate between pages

  // React Table instance
  const {
    getTableProps,
    getTableBodyProps,
    headerGroups,
    page,
    prepareRow,
    canPreviousPage,
    canNextPage,
    nextPage,
    previousPage,
    setPageSize,
    state: { pageIndex, pageSize, sortBy },
  } = useTable(
    {
      columns: useMemo(
        () => [
          { Header: 'ID', accessor: 'id' },
          { Header: 'Name', accessor: 'name' },
          { Header: 'Email', accessor: 'email' },
          { Header: 'Phone', accessor: 'phone' },
          {
            Header: 'Status',
            accessor: 'lead_status.name', // Ensure this accessor matches the API response
            disableSortBy: true,     // Disable sorting for this column
          },
          {
            Header: 'Actions',
            Cell: ({ row }) => (
              <>
                <button onClick={() => handleEditLead(row.original.id)}>
                  Edit
                </button>
                <button onClick={() => handleDeleteLead(row.original.id)} style={{marginLeft: '5px'}}  > 
                  Delete
                </button>
              </>
            ), // Adding Edit and Delete buttons for each lead
          },
        ],
        []
      ),
      data,
      manualPagination: true, // Enable server-side pagination
      pageCount,              // Total page count from server
      manualSortBy: true,     // Manual sorting
      initialState: { pageIndex: 0, pageSize: 10, sortBy: controlledSortBy }, // Start with page 0, 10 rows per page
    },
    useSortBy,
    usePagination
  );

  // Fetch leads data from Laravel API
  const fetchLeads = async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await axios.get('http://localhost:8000/api/leads', {
        params: {
          page: pageIndex + 1, // Pagination in API is 1-based
          perPage: pageSize,   // Number of items per page
          search: searchTerm,
          sortBy: sortBy[0]?.id || 'id',
          sortDirection: sortBy[0]?.desc ? 'desc' : 'asc',
        }
      });

      setData(response.data.data); // Set the data from the API
      setPageCount(response.data.last_page); // Set the total number of pages
    } catch (err) {
      setError('Error fetching data');
    } finally {
      setLoading(false);
    }
  };

  // Navigate to the create lead page
  const handleCreateLead = () => {
    navigate('/leads/create'); // Navigate to the LeadFormPage for creating a new lead
  };

  // Navigate to the edit lead page
  const handleEditLead = (id) => {
    navigate(`/leads/edit/${id}`); // Navigate to the LeadFormPage for editing the selected lead
  };

  // Handle lead deletion
  const handleDeleteLead = async (id) => {
    if (window.confirm("Are you sure you want to delete this lead?")) {
      try {
        await axios.delete(`http://localhost:8000/api/leads/${id}`);
        fetchLeads(); // Refetch the leads after deletion
      } catch (err) {
        alert("Error deleting lead.");
        console.error(err);
      }
    }
  };

  // Fetch leads whenever search term, pagination, or sorting changes
  useEffect(() => {
    fetchLeads();
  }, [searchTerm, pageIndex, pageSize, sortBy]);

  return (
    <div className="table-container">
    <div className="header">
      <h1 className="animated-title" >Leads</h1>
      <button onClick={handleCreateLead} className="btn create-btn">
        Create Lead
      </button>
    </div>

    <input
      type="text"
      placeholder="Search by name or email"
      value={searchTerm}
      style={{marginTop: '10px'}} 
      onChange={(e) => setSearchTerm(e.target.value)}
      className="search-input"
    />

    {loading ? (
      <p>Loading...</p>
    ) : error ? (
      <p>{error}</p>
    ) : (
      <>
        <table {...getTableProps()} className="leads-table animated-table">
          <thead>
            {headerGroups.map((headerGroup) => (
              <tr key={headerGroup.id} {...headerGroup.getHeaderGroupProps()} className="table-row">
                {headerGroup.headers.map((column) => {
                  const { key, ...rest } = column.getHeaderProps(column.getSortByToggleProps());
                  return (
                    <th key={key} {...rest}>
                      {column.render('Header')}
                      <span>{column.isSorted ? (column.isSortedDesc ? ' 🔽' : ' 🔼') : ''}</span>
                    </th>
                  );
                })}
              </tr>
            ))}
          </thead>

          <tbody {...getTableBodyProps()} className="table-body">
            {page.map((row) => {
              prepareRow(row);
              return (
                <tr key={row.id} {...row.getRowProps()} className="animated-row">
                  {row.cells.map((cell) => {
                    const { key, ...rest } = cell.getCellProps();
                    return (
                      <td key={key} {...rest}>
                        {cell.render('Cell')}
                      </td>
                    );
                  })}
                </tr>
              );
            })}
          </tbody>
        </table>

        <div className="pagination">
          <button onClick={previousPage} disabled={!canPreviousPage} className="btn">
            Previous
          </button>
          <span>
            Page {pageIndex + 1} of {pageCount}
          </span>
          <button onClick={nextPage} disabled={!canNextPage} className="btn">
            Next
          </button>

          <select value={pageSize} onChange={(e) => setPageSize(Number(e.target.value))} className="select-page-size">
            {[10, 20, 50].map((size) => (
              <option key={size} value={size}>
                Show {size}
              </option>
            ))}
          </select>
        </div>
      </>
    )}
  </div>
  );
}

export default Leads;
