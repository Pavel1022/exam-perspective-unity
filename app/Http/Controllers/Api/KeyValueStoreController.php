<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeyValueStore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeyValueStoreController extends Controller
{
    /**
     * Add or update a key-value pair with an optional TTL (Time To Live).
     *
     * This endpoint allows clients to add a new key-value pair to the key-value store.
     * If a key already exists, it will be updated with the new value and TTL.
     * The TTL determines how long the key-value pair will be valid. After the TTL expires,
     * the key-value pair will be considered expired but not removed from the database.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

     /**
     * Retrieve the value for a given key.
     *
     * This endpoint retrieves the value associated with a specified key. If the key exists and 
     * has not expired, the value is returned. If the key does not exist or has expired, a 
     * `null` value is returned with a 404 status code.
     *
     * @param string $key The key for which the value is to be retrieved. Example: "username"
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Delete a key-value pair from the store.
     *
     * This endpoint deletes the key-value pair associated with the specified key.
     * If the key exists and is successfully deleted, a confirmation message is returned.
     * If the key does not exist, a "Key not found" message is returned with a 404 status code.
     *
     * @param string $key The key of the key-value pair to be deleted. Example: "username"
     * @return \Illuminate\Http\JsonResponse
     */
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
