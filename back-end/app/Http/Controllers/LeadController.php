<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LeadController extends Controller
{
    /**
     * Display a listing of the leads with pagination, search, sorting, and filtering.
     */
    public function index(Request $request)
    {
        try {
            // Check if we have cached results for the current query string
            return Cache::remember('leads_' . $request->getQueryString(), 60, function () use ($request) {

                // Get query parameters for search, sorting, and pagination
                $search = $request->input('search');
                $sortBy = $request->input('sortBy', 'id'); // Default sorting by ID
                $sortDirection = $request->input('sortDirection', 'asc'); // Default sort direction (ascending)
                $perPage = $request->input('perPage', 50); // Items per page, you can adjust this

                // Query the leads table with eager loading of leadStatus
                $query = Lead::with('leadStatus');

                // Apply search filter if provided
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
                }

                // Check if the sorting is by lead status name
                if ($sortBy == 'lead_status.name') {
                    $query->join('lead_statuses', 'leads.lead_status_id', '=', 'lead_statuses.id')
                        ->orderBy('lead_statuses.name', $sortDirection);
                } else {
                    // Apply sorting for other fields
                    $query->orderBy($sortBy, $sortDirection);
                }

                // Apply pagination
                return $query->paginate($perPage);
            });
        } catch (\Exception $e) {
            Log::error('Error fetching leads: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch leads at the moment. Please try again later.'], 500);
        }
    }

    /**
     * Store a newly created lead in the database.
     */
    public function store(Request $request)
    {
        try {
            // Validate incoming request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:leads,email',
                'phone' => 'nullable|string',
                'lead_status_id' => 'required|exists:lead_statuses,id',
            ]);

            // Create and save the lead
            $lead = Lead::create($validated);

            return response()->json($lead, 201); // Return the created lead
        } catch (\Exception $e) {
            Log::error('Error creating lead: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to create lead at the moment. Please try again later.'], 500);
        }
    }

    /**
     * Edit the specified lead in the database.
     */
    public function edit($id)
    {
        try {
            // Fetch the lead by ID
            $lead = Lead::findOrFail($id);

            return response()->json($lead, 200); // Return the lead
        } catch (ModelNotFoundException $e) {
            Log::error('Lead not found for ID: ' . $id);
            return response()->json(['error' => 'Lead not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching lead: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch lead at the moment. Please try again later.'], 500);
        }
    }

    /**
     * Update the specified lead in the database.
     */
    public function update(Request $request, $id)
    {
        try {
            $lead = Lead::findOrFail($id);

            // Validate request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string',
                'lead_status_id' => 'required|exists:lead_statuses,id',
            ]);

            // Update the lead
            $lead->update($validated);

            return response()->json($lead);
        } catch (ModelNotFoundException $e) {
            Log::error('Lead not found for update. ID: ' . $id);
            return response()->json(['error' => 'Lead not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating lead: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to update lead at the moment. Please try again later.'], 500);
        }
    }

    /**
     * Remove the specified lead from the database.
     */
    public function destroy($id)
    {
        try {
            $lead = Lead::findOrFail($id);

            // Delete the lead
            $lead->delete();

            // Invalidate all cache related to leads (since pagination, filters, etc. may differ)
            Cache::flush();  // This clears all cache, or use a more targeted approach to clear only leads-related cache

            return response()->json(['message' => 'Lead deleted successfully']);
        } catch (ModelNotFoundException $e) {
            Log::error('Lead not found for deletion. ID: ' . $id);
            return response()->json(['error' => 'Lead not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting lead: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to delete lead at the moment. Please try again later.'], 500);
        }
    }

    /**
     * Get lead statuses for the form.
     */
    public function getLeadStatus()
    {
        try {
            return LeadStatus::select('id', 'name')->get();
        } catch (\Exception $e) {
            Log::error('Error fetching lead statuses: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch lead statuses. Please try again later.'], 500);
        }
    }
}
