<?php

namespace App\Services;

use App\Mail\EmailOtpMail;
use App\Models\TbUsers;
use Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use function PHPUnit\Framework\returnArgument;

class EmailOtpService
{
    protected $otp = null;
    public function generate($user)
    {
        $otp = random_int(100000, 999999);
        DB::table('tb_email_otps')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Mail::to($user->email)->send(new EmailOtpMail($otp));
        return $otp;
    }
    public function verify($user, $otp)
    {
        $otpRecord = DB::table('tb_email_otps')->where('is_used', false)->where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
        if (!$otpRecord) {
            return false;
        }

        if (!Hash::check($otp, $otpRecord->otp_hash)) {
            return false;
        }
        DB::transaction(function () use ($user, $otpRecord) {
            DB::table('tb_email_otps')->where('id', $otpRecord->id)->update(['is_used' => true]);

            $user->update([
                'email_verified' => true,
                'email_verified_at' => now(),
            ]);
        });
        return true;
    }
}