<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UserAttendanceReportRequest;
use App\Http\Requests\UserResetDeviceIdRequest;
use App\Http\Requests\UsersRequest;
use App\Http\Requests\UsersUpdateRequest;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersDashboardResource;
use App\Models\Attendance;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{

    public function storeAttendance(StoreAttendanceRequest $request)
    {
        $jwt = ($request->hasHeader('jwt') ? $request->header('jwt') : "");
        $user = check_jwt($jwt);
        if ($user) {
            $data['user_id'] = $user->id;
            $data['notes'] = $request->note;
            $data['type'] = $request->type;
            $data['date'] = Carbon::now()->format("Y-m-d");
            $attendance_today = Attendance::where('user_id', $user->id)
                ->where('date', $data['date'])
                ->first();
            if ($request->type == "check_in") {
                if ($attendance_today) {
                    return msgdata(failed(), ' تم تسجيل حضور اليوم من قبل ', (object)[]);
                }
                $data['in_time'] = Carbon::now()->format("H:i");
                Attendance::create($data);
                return msgdata(success(), 'تم تسجيل الحضور بنجاح', (object)[]);
            } else {

                if (!$attendance_today) {
                    return msgdata(failed(), ' برجاء تسجيل حضور اولآ', (object)[]);
                }

                if ($attendance_today->out_time != null) {
                    return msgdata(failed(), ' تم تسجيل انصراف اليوم من قبل', (object)[]);
                }

                $data['out_time'] = Carbon::now()->format("H:i");
                $attendance_today->update($data);
                return msgdata(success(), 'تم تسجيل انصراف بنجاح', (object)[]);
            }

        } else {
            return msgdata(not_authoize(), 'برجاء تسجيل الدخول', (object)[]);
        }
    }

    public function attendanceReport(UserAttendanceReportRequest $request){

        

    }

}
