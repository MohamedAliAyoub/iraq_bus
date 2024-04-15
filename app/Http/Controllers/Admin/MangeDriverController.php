<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\DriverStoreRequest;
use App\Models\DriverCarImage;
use App\Models\DriverDetails;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MangeDriverController extends Controller
{

    public function getAllDrivers()
    {
        $pageTitle = 'Manage Drivers';
        $emptyMessage = 'No Manger found';
        $users = User::where("type", User::DRIVER)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.drivers.index', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function create()
    {
        $pageTitle = 'Drivers List';
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.users.drivers.create', compact('pageTitle', 'countries'));
    }

    public function store(DriverStoreRequest $request)
    {

        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $countryCode = $request->country;
        $user = new User();
        $user->mobile = $request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->username = $request->username;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country,
        ];
        $user->status = 1;
        $user->country_code = 'IQ';
        $user->type = User::DRIVER;
        $user->ev = 1;
        $user->sv = 1;
        $user->credit_limit = $request->credit_limit;
        $user->save();


        if (!$user->pocket) {
            $user->pocket()->create([
                'credit_limit' => $request->credit_limit,
            ]);
        }

        try {
            $first_id_card_image_path = uploadImage($request->first_id_card_image, imagePath()['driver']['path']);
            $last_id_card_image_path = uploadImage($request->last_id_card_image, imagePath()['driver']['path']);
            $first_residence_card_image_path = uploadImage($request->first_residence_card_image, imagePath()['driver']['path']);
            $last_residence_card_image_path = uploadImage($request->last_residence_card_image, imagePath()['driver']['path']);
            $first_license_image_path = uploadImage($request->first_license_image, imagePath()['driver']['path']);
            $last_license_image_path = uploadImage($request->last_license_image, imagePath()['driver']['path']);

        } catch (\Exception $exp) {
            $notify[] = ['error', 'Image could not be uploaded.'];
            return back()->withNotify($notify);
        }
        if (!$user->driverDetails) {
            $driverDetails = DriverDetails::query()->create([
                'user_id' => $user->id,
                'first_id_card_image' => $first_id_card_image_path,
                'last_id_card_image' => $last_id_card_image_path,
                'first_residence_card_image' => $first_residence_card_image_path,
                'last_residence_card_image' => $last_residence_card_image_path,
                'first_license_image' => $first_license_image_path,
                'last_license_image' => $last_license_image_path,
                'record' => $request->record ?? null,
                'pdf' => $request->pdf ?? null,
                'image' => $request->image,
                'status' => 1,
            ]);
        }
        if (!is_null($request->car_images) && is_array($request->car_images)) {
            foreach ($request->car_images as $image) {
                try {
                    $image_path = uploadImage($image, imagePath()['driver']['path']);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Image could not be uploaded.'];
                    return back()->withNotify($notify);
                }
//                dd($user, $driverDetails, $image_path);
                DriverCarImage::query()->create([
                    'user_id' => $user->id,
                    'driver_details_id' => $driverDetails->id,
                    'image' => $image_path,
                ]);
            }
        }

        $notify[] = ['success', 'User has been created'];
        return redirect()->back()->withNotify($notify);
    }

    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $users = User::where('type', User::DRIVER)->where(function ($user) use ($search) {
            $user->where('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        });
        $pageTitle = '';
        if ($scope == 'active') {
            $pageTitle = 'Active ';
            $users = $users->where('status', 1);
        } elseif ($scope == 'banned') {
            $pageTitle = 'Banned';
            $users = $users->where('status', 0);
        } elseif ($scope == 'emailUnverified') {
            $pageTitle = 'Email Unverified ';
            $users = $users->where('ev', 0);
        } elseif ($scope == 'smsUnverified') {
            $pageTitle = 'SMS Unverified ';
            $users = $users->where('sv', 0);
        } elseif ($scope == 'withBalance') {
            $pageTitle = 'With Balance ';
            $users = $users->where('balance', '!=', 0);
        }

        $users = $users->paginate(getPaginate());
        $pageTitle .= 'User Search - ' . $search;
        $emptyMessage = 'No search result found';
        return view('admin.users.list', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'users'));
    }


    public function update(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));


        $request->validate([
//
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'email' => 'required|email|max:90|unique:users,email,' . $user->id,
            'mobile' => 'required|unique:users,mobile,' . $user->id,
            'address' => 'required',
            'status' => 'nullable',
            'pdf' => [
                'nullable',
                'file',
                'mimes:pdf',
                Rule::requiredIf(isset($request->status)),
            ],
            'image' => [
                'nullable',
                'image',
                Rule::requiredIf(isset($request->status)),
            ],
            'record' => [
                'nullable',
                'file',
                'mimes:mp3',
                Rule::requiredIf(isset($request->status)),
            ],
        ]);


        $countryCode = $request->country;
        $user->mobile = $request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$countryData->$countryCode->country,
        ];
        $user->status = $request->status ? 1 : 0;
        $user->ev = $request->ev ? 1 : 0;
        $user->sv = $request->sv ? 1 : 0;
        $user->save();

        $path = imagePath()['driver']['path'];

        if ($request->status == 1) {

            try {
                $image_path = uploadImage($request->image, imagePath()['driver']['path'], imagePath()['driver']['size'], $user->driverDetails->image);
                $pdf_path = uploadFile($request->pdf, $path, imagePath()['driver']['size'], $user->driverDetails->pdf);
                $record_path = uploadFile($request->record, $path, imagePath()['driver']['size'], $user->driverDetails->record);
                DriverDetails::query()->where('user_id', $user->id)->update([
                    'pdf' => $record_path,
                    'record' => $pdf_path,
                    'image' => $image_path
                ]);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'User detail has been updated'];
        return redirect()->back()->withNotify($notify);
    }

    public function detail($id)
    {
        $pageTitle = 'Driver Detail';
        $user = User::with(['driverDetails', 'driverCarImage'])->findOrFail($id);
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.users.drivers.detail', compact('pageTitle', 'user', 'countries'));
    }

}
