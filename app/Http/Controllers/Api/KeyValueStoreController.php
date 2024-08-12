<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeyValueStore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeyValueStoreController extends Controller
{
    public function addKeyValue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'ttl' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $key = $request->input('key');
        $value = $request->input('value');
        $ttl = $request->input('ttl');

        $keyValue = KeyValueStore::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'expires_at' => $ttl ? Carbon::now()->addSeconds($ttl) : null]
        );

        return response()->json(['message' => 'Key-Value pair added']);
    }

    public function getKeyValue($key)
    {
        $keyValue = KeyValueStore::where('key', $key)->first();

        if ($keyValue) {
            if ($keyValue->expires_at && Carbon::now()->greaterThan($keyValue->expires_at)) {
                return response()->json(['value' => null], 404);
            }

            return response()->json(['value' => $keyValue->value]);
        }

        return response()->json(['value' => null], 404);
    }

    public function deleteKeyValue($key)
    {
        $keyValue = KeyValueStore::where('key', $key)->first();

        if ($keyValue) {
            $keyValue->delete();
            return response()->json(['message' => 'Key-Value pair deleted']);
        }

        return response()->json(['message' => 'Key not found'], 404);
    }
}
