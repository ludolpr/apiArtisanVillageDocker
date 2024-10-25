<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\FicheEmail;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;


class CompanyController extends Controller
{

    
    public function index()
    {
        $company = Company::all();
        return response()->json($company);
    }

    
    public function getLatestIds()
    {
        $latestIds = Company::orderBy('id', 'desc')->take(2)->get(['id', 'name_company', 'description_company', 'picture_company']);
        return response()->json($latestIds);
    }


 
    public function store(Request $request)
    {
        $request->validate([
            'name_company' => 'required|max:50',
            'description_company' => 'required|max:400',
            'picture_company' => 'required|image|max:10000',
            'zipcode' => 'required|max:5',
            'phone' => 'required|max:50',
            'address' => 'required|max:150',
            'siret' => 'required',
            'town' => 'required|max:50',
        ]);

        $filename = "";
        if ($request->hasFile('picture_company')) {
            $filenameWithExt = $request->file('picture_company')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_company')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_company')->storeAs('public/uploads/companies', $filename);
        }

        $user = Auth::user();

        $company = Company::create([
            'name_company' => $request->name_company,
            'description_company' => $request->description_company,
            'picture_company' => $filename,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'address' => $request->address,
            'siret' => $request->siret,
            'town' => $request->town,
            'lat' => $request->lat,
            'long' => $request->long,
            'id_user' => $user->id,
        ]);

        // Changement du rôle de l'utilisateur 
        if ($user->id_role == 1) {
            $user->id_role = 2;
            $user->save();
        }

        return response()->json([
            'status' => 'Success',
            'data' => $company,
        ]);
    }


   
    public function show(Company $company)
    {
        return response()->json($company);
    }



    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name_company' => 'required|max:50',
            'description_company' => 'required|max:400',
            'zipcode' => 'required|max:5',
            'phone' => 'required|max:50',
            'address' => 'required|max:150',
            'siret' => 'required',
            'town' => 'required|max:50',
        ]);

        $filename = $company->picture_company;
        if ($request->hasFile('picture_company')) {
            if ($company->picture_company) {
                Storage::delete('public/uploads/companies/' . $company->picture_company);
            }

            $filenameWithExt = $request->file('picture_company')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_company')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_company')->storeAs('public/uploads/companies', $filename);

            $company->picture_company = $filename;
        }

        $user = Auth::user();

        $company->update([
            'name_company' => $request->name_company,
            'description_company' => $request->description_company,
            'picture_company' => $filename,
            'zipcode' => $request->zipcode,
            'phone' => $request->phone,
            'address' => $request->address,
            'siret' => $request->siret,
            'town' => $request->town,
            'lat' => $request->lat,
            'long' => $request->long,
            'id_user' => $user->id,
        ]);

        if ($user->id_role == 1) {
            $user->id_role = 2;
            $user->save();
        }

        return response()->json([
            'status' => 'Update OK',
            'data' => $company,
        ]);
    }


    public function destroy(Company $company)
    {
        $company->delete();
        $user = Auth::user();
        if ($user->id_role == 2) {
            $user->id_role = 1;
            $user->save();
        }

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}