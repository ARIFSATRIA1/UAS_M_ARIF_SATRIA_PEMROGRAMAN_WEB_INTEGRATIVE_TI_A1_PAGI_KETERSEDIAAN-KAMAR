<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Patient;
use App\Models\Hospitalization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    public function checkAvailability()
    {
        try {
            $patients = Patient::all(); // Retrieve all patients
            return response()->json($patients);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving patients', 'error' => $e->getMessage()], 500);
        }
    }

    public function admitPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'admission_date' => 'required|date',
            'room_id' => 'required|rooms,id',
        ]);


        try {
            $patient = Patient::create([
                'name' => $request->name,
                'admission_date' => $request->admission_date,
            ]);

            $room = Room::find($request->room_id);

            if ($room && $room->is_available) {
                $hospitalization = Hospitalization::create([
                    'room_id' => $room->id,
                    'patient_id' => $patient->id,
                    'admission_date' => $request->admission_date,
                ]);

                $room->update(['is_available' => false]);

                return response()->json(['message' => 'Patient admitted successfully', 'data' => $hospitalization]);
            }

            return response()->json(['message' => 'Room is not available'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to admit patient', 'error' => $e->getMessage()], 500);
        }
    }

    public function dischargePatient($id)
    {
        try {
            $patient = Patient::find($id);
    
            if ($patient) {
                // Free up the room associated with the patient if they have a hospitalization record
                $hospitalization = Hospitalization::where('patient_id', $id)->first();
                if ($hospitalization) {
                    $hospitalization->room->update(['is_available' => true]);
                    $hospitalization->delete();
                }
    
                $patient->delete();
    
                return response()->json(['message' => 'Patient deleted successfully']);
            }
    
            return response()->json(['message' => 'Patient not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete patient', 'error' => $e->getMessage()], 500);
        }
    }
}
