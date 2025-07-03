<?php

namespace App\Http\Controllers;

use App\Models\PasswordEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PasswordEntryController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth:api');

    }

    public function index()
    {

        $entries = auth()->user()->passwordEntries()->get();

        return response()->json($entries);

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'website' => 'nullable',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);

        }

        $entry = auth()->user()->passwordEntries()->create($request->all());

        return response()->json($entry, 201);

    }

    public function show($id)
    {

        $entry = auth()->user()->passwordEntries()->findOrFail($id);

        return response()->json($entry->makeVisible('password'));

    }

    public function update(Request $request, $id)
    {

        $entry = auth()->user()->passwordEntries()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'website' => 'nullable',
            'username' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);

        }

        $entry->update($request->all());

        return response()->json($entry);

    }

    public function destroy($id)
    {

        $entry = auth()->user()->passwordEntries()->findOrFail($id);

        $entry->delete();

        return response()->json(null, 204);

    }

    public function generatePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'length' => 'sometimes|integer|min:8|max:32',
            'include_numbers' => 'sometimes|boolean',
            'include_symbols' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);

        }

        $length = $request->input('length', 12);

        $includeNumbers = $request->input('include_numbers', true);

        $includeSymbols = $request->input('include_symbols', true);

        $password = $this->generateRandomPassword($length, $includeNumbers, $includeSymbols);

        return response()->json(['password' => $password]);

    }

    public function checkPasswordStrength(Request $request)
    {

        $request->validate([
            'password' => 'required|string',
        ]);

        $password = $request->password;

        $strength = $this->calculatePasswordStrength($password);

        return response()->json(['strength' => $strength]);

    }

    private function generateRandomPassword($length = 12, $includeNumbers = true, $includeSymbols = true)
    {

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($includeNumbers) {

            $chars .= '0123456789';

        }
        if ($includeSymbols) {

            $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';

        }

        $password = '';

        $charsLength = strlen($chars);

        for ($i = 0; $i < $length; $i++) {

            $password .= $chars[rand(0, $charsLength - 1)];

        }

        return $password;

    }

    private function calculatePasswordStrength($password)
    {

        $score = 0;

        $length = strlen($password);

        $score += min(4, floor($length / 3));

        if (preg_match('/[0-9]/', $password)) {

            $score += 1;

        }

        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {

            $score += 1;

        }

        if (preg_match('/[^a-zA-Z0-9]/', $password)) {

            $score += 1;

        }

        if (preg_match('/password|123456|qwerty/i', $password)) {

            $score -= 2;

        }

        $score = min(10, max(0, $score * 2));

        return $score;

    }

}
