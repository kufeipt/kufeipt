<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Models\IyuuAuthRecord;

class IyuuAuthController extends Controller
{
    public function validateGetRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'id' => 'required|int|min:1',
                'verity' => 'required|string',
                'provider' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return ['success' => false, 'msg' => 'Invalid data format.'];
        }

        try {
            $this->processGetRequest($validated);
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
        return ['success' => true];
    }

    public function processGetRequest($data)
    {
        $user = User::find($data['id']);
        if (!$user) {
            throw new \InvalidArgumentException("Invalid uid or passkey.");
        }
        if ($user->enabled == User::ENABLED_NO) {
            throw new \InvalidArgumentException("User has been banned.");
        }
        if ($user->status == User::STATUS_PENDING) {
            throw new \InvalidArgumentException("User not confirmed.");
        }
        if ($user->parked == 'yes') {
            throw new \InvalidArgumentException("User has been parked.");
        }

        $secret = env('IYUU_SECRET');
        $verity = md5($data['token'] . $data['id'] . sha1($user->passkey) . $secret);

        if ($data['verity'] !== $verity) {
            throw new \InvalidArgumentException("Invalid uid or passkey.");
        }
        $existingRecord = IyuuAuthRecord::where('userid', $data['id'])->first();
        
        if ($existingRecord) {
            // 如果记录已存在，则更新iyuuid
            $existingRecord->update([
                'iyuuid' => $data['provider'],
            ]);
        } else {
            // 如果记录不存在，则创建新记录
            IyuuAuthRecord::create([
                'userid' => $data['id'],
                'iyuuid' => $data['provider'],
            ]);
        }
        // 验证成功，更新iyuuauth字段为 'yes'
        $user->iyuuauth = 'yes';
        $user->save();
        return true;
    }
}
