<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAllUsersReportRequest;
use App\Http\Requests\SearchByUserRequest;
use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDF;

class ReportsController extends Controller
{

    public function searchByUser(SearchByUserRequest $request)
    {
        $data = $request->validated();

        $state = Attendance::where('user_id', $data['user_id'])->get();
        $pdf = PDF::loadView('attendance_by_user', ['data' => $data]);
        $name = rand(1000, 9999) . time() . '.pdf';
        $pdf->save(public_path() . '/reports/' . $name);

        return msgdata(success(), trans('lang.success'), url('/') . '/reports/' . $name);

    }

    public function allUsersReport(AdminAllUsersReportRequest $request)
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
                $data[$key]["in_time"] = $attend ? $attend->in_time : null;
                $data[$key]["out_time"] = $attend ? $attend->out_time : null;
                $data[$key]["notes"] = $attend ? $attend->notes : null;
                $data[$key]["lat"] = $attend ? $attend->lat : null;
                $data[$key]["lng"] = $attend ? $attend->lng : null;
            }
            $response[$key1]['user'] = $item->name;
            $response[$key1]['report'] = $data;
        }

        $pdf = PDF::loadView('attendance_by_all_user', ['data' => $response, 'from' => $request->from_date, 'to' => $request->to_date]);
        $name = rand(1000, 9999) . time() . '.pdf';
        $pdf->save(public_path() . '/reports/' . $name);
        return msgdata(success(), trans('lang.success'), url('/') . '/reports/' . $name);

    }

    public function userReport(AdminAllUsersReportRequest $request)
    {
        $from_date = Carbon::parse($request->from_date)->endOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();
        $period = CarbonPeriod::create($from_date, $to_date);

        $dateStrings = [];
        foreach ($period as $date) {
            $dateStrings[] = $date->toDateString();
        }
        $response['user'] = User::where('id',$request->user_id)->first();
        foreach ($dateStrings as $key => $date) {
            $attend = Attendance::where('user_id', $request->user_id)->whereDate('date', $date)->first();
            $data[$key]["date"] = $date;
            $data[$key]["in_time"] = $attend ? $attend->in_time : null;
            $data[$key]["out_time"] = $attend ? $attend->out_time : null;
            $data[$key]["notes"] = $attend ? $attend->notes : null;
            $data[$key]["lat"] = $attend ? $attend->lat : null;
            $data[$key]["lng"] = $attend ? $attend->lng : null;
        }
        $response['report'] = $data;


        $pdf = PDF::loadView('attendance_by_user', ['data' => $response, 'from' => $request->from_date, 'to' => $request->to_date]);
        $name = rand(1000, 9999) . time() . '.pdf';
        $pdf->save(public_path() . '/reports/' . $name);
        return msgdata(success(), trans('lang.success'), url('/') . '/reports/' . $name);

    }

}
