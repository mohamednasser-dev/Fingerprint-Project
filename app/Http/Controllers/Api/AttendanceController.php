<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAllUsersReportRequest;
use App\Http\Requests\AdminAttendanceReportRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UserAttendanceReportRequest;
use App\Http\Resources\AdminAttendanceReportResource;
use App\Http\Resources\UserAttendanceReportResource;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{

    public function storeAttendance(StoreAttendanceRequest $request)
    {
        $jwt = ($request->hasHeader('jwt') ? $request->header('jwt') : "");
        $user = check_jwt($jwt);

        $data['user_id'] = $user->id;
        $data['type'] = $request->type;
        if($data['type'] == 'check_in'){
            $data['notes'] = $request->note;
            $data['lat'] = $request->lat;
            $data['lng'] = $request->lng;
        }else{
            $data['out_notes'] = $request->note;
            $data['out_lat'] = $request->lat;
            $data['out_lng'] = $request->lng;
        }


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


                $data['out_time'] = Carbon::now()->format("H:i");
                $attendance_today->update($data);
           

            return msgdata(success(), 'تم تسجيل انصراف بنجاح', (object)[]);
        }
    }

    public function attendanceReport(UserAttendanceReportRequest $request)
    {
        $jwt = ($request->hasHeader('jwt') ? $request->header('jwt') : "");
        $user = check_jwt($jwt);

        $from_date = Carbon::parse($request->from_date)->endOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();
        $period = CarbonPeriod::create($from_date, $to_date);


        $dateStrings = [];
        foreach ($period as $date) {
            $dateStrings[] = $date->toDateString();
        }

        foreach ($dateStrings as $key => $date) {
            $attend = Attendance::where('user_id', $user->id)
                ->whereDate('date', $date)->first();
            $data[$key]["date"] = $date;
            $data[$key]["in_time"] = $attend ? $attend->in_time : "" ;
            $data[$key]["out_time"] = $attend ? $attend->out_time : "" ;
            $data[$key]["notes"] = $attend ? $attend->notes : "" ;
            $data[$key]["lat"] = $attend ? $attend->lat : "" ;
            $data[$key]["lng"] = $attend ? $attend->lng : "" ;

            $data[$key]["out_notes"] = $attend ? $attend->out_notes : "" ;
            $data[$key]["out_lat"] = $attend ? $attend->out_lat : "" ;
            $data[$key]["out_lng"] = $attend ? $attend->out_lng : "" ;
        }
        $data = array_reverse($data);
        $data = UserAttendanceReportResource::collection(collect($data));
        return msgdata(success(), 'تم بنجاح', $data);
    }

    public function AdminAttendanceByUserReport(AdminAttendanceReportRequest $request)
    {
        $from_date = Carbon::parse($request->from_date)->endOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();
        $period = CarbonPeriod::create($from_date, $to_date);

        $dateStrings = [];
        foreach ($period as $date) {
            $dateStrings[] = $date->toDateString();
        }

        $user_data = User::whereId($request->user_id)->first();
        foreach ($dateStrings as $key => $date) {
            $attend = Attendance::where('user_id', $request->user_id)
                ->whereDate('date', $date)->first();

            $data[$key]["date"] = $date;
            $data[$key]["in_time"] = $attend ? $attend->in_time : "" ;
            $data[$key]["out_time"] = $attend ? $attend->out_time : "" ;
            $data[$key]["notes"] = $attend ? $attend->notes : "" ;
            $data[$key]["lat"] = $attend ? $attend->lat : "" ;
            $data[$key]["lng"] = $attend ? $attend->lng : "" ;

            $data[$key]["out_notes"] = $attend ? $attend->out_notes : "" ;
            $data[$key]["out_lat"] = $attend ? $attend->out_lat : "" ;
            $data[$key]["out_lng"] = $attend ? $attend->out_lng : "" ;
        }
        $response['user'] = $user_data->name;
        $response['report'] = $data;
        return msgdata(success(), 'تم بنجاح', $response);
    }

    public function AdminAttendanceAllUsersReport(AdminAllUsersReportRequest $request)
    {
        $from_date = Carbon::parse($request->from_date)->endOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();
        $period = CarbonPeriod::create($from_date, $to_date);

        $dateStrings = [];
        foreach ($period as $date) {
            $dateStrings[] = $date->toDateString();
        }

        $users = User::whereType('user')->get();
        foreach ($users as $key1 => $item) {
            foreach ($dateStrings as $key => $date) {
                $attend = Attendance::where('user_id', $item->id)->whereDate('date', $date)->first();
                $data[$key]["date"] = $date;
                $data[$key]["in_time"] = $attend ? $attend->in_time : "" ;
                $data[$key]["out_time"] = $attend ? $attend->out_time : "" ;

                $data[$key]["notes"] = $attend ? $attend->notes : "" ;
                $data[$key]["lat"] = $attend ? $attend->lat : "" ;
                $data[$key]["lng"] = $attend ? $attend->lng : "" ;

                $data[$key]["out_notes"] = $attend ? $attend->out_notes : "" ;
                $data[$key]["out_lat"] = $attend ? $attend->out_lat : "" ;
                $data[$key]["out_lng"] = $attend ? $attend->out_lng : "" ;
            }
            $response[$key1]['user'] = $item->name;
            $response[$key1]['report'] = $data;
        }
        return msgdata(success(), 'تم بنجاح', $response);
    }

}
