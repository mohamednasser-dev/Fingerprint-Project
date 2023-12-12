<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserResetDeviceIdRequest;
use App\Http\Requests\UsersRequest;
use App\Http\Requests\UsersUpdateRequest;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersDashboardResource;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $rule = [
            'phone' => 'required',
            'password' => 'required|min:6',
            'device_id' => 'nullable|max:255',
        ];
        $validate = Validator::make($request->all(), $rule);
        if ($validate->fails()) {
            return response()->json(msgdata(error(), $validate->messages()->first(), (object)[]));
        } else {

            $credentials = $request->only(['phone', 'password']);
            $token = Auth::attempt($credentials);
            //return token
            if (!$token) {
                return msgdata(not_authoize(), 'رقم الهاتف او كلمه المرور خاطئة', (object)[]);
            }
            $user = Auth::user();
            if ($user->type == 'user') {
                $rule = [
                    'device_id' => 'required',
                ];
                $validate = Validator::make($request->all(), $rule);
                if ($validate->fails()) {
                    return response()->json(msgdata(error(), $validate->messages()->first(), (object)[]));
                }

                if ($user->device_id == null) {
                    $user->device_id = $request->device_id;
                    $user->save();
                } else {
                    if ($user->device_id != null && $user->device_id != $request->device_id) {
                        Auth::logout();
                        return msgdata(failed(), 'تم الدخول من جهاز اخر يرجى تسجيل الدخول من جهازك', (object)[]);

                    }
                }


            }
            User::where('id', $user->id)->update(['jwt' => Str::random(60)]);
            $user = User::whereId($user->id)->first();
            $data = new UserResource($user);

            return response()->json(msgdata(success(), 'تم الدخول بنجاح', $data));
        }
    }

    public function updatePassword(Request $request)
    {
        $jwt = ($request->hasHeader('jwt') ? $request->header('jwt') : "");
        $user = check_jwt($jwt);
        if ($user) {
            $rule = [
                'password' => 'required|confirmed',
                'old_password' => 'required',
            ];
            $validate = Validator::make($request->all(), $rule);
            if ($validate->fails()) {
                return response()->json(msgdata(error(), $validate->messages()->first(), (object)[]));
            } else {
                $pass = $user->password;
                if (\Hash::check($request->old_password, $pass)) {
                    $data = User::find($user->id);
                    $data->password = \Hash::make($request->password);
                    $data->save();
                    return response()->json(msg(success(), 'تم التعديل بنجاح'));
                } else {
                    return response()->json(msg(error(), 'كلمة المرور القديمة غير صحيحة'),error());
                }
            }
        } else {
            return response()->json(msgdata(not_authoize(), 'برجاء تسجيل الدخول', (object)[]),not_authoize());
        }
    }

    public function index(Request $request)
    {
        $users = User::Where('type', 'user')->paginate(10);
        $data = UsersDashboardResource::collection($users)->response()->getData(true);
        return response()->json(msgdata(success(), 'تم عرض البيانات بنجاح', $data));
    }

    public function store(UsersRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);
        $data = new UsersDashboardResource($user);
        return response()->json(msgdata(success(), 'تم الاضافة بنجاح', $data));
    }

    public function update(UsersUpdateRequest $request)
    {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        User::whereId($data['id'])->update($data);
        return response()->json(msgdata(success(), 'تم التعديل بنجاح', (object)[]));
    }

    public function resetDeviceId(UserResetDeviceIdRequest $request)
    {
        $data = $request->validated();
        User::where('id', $data['id'])->update(['device_id' => null]);
        return response()->json(msgdata(success(), 'تم اعادة ضبط رقم الجهاز للمستخدم بنجاح', (object)[]));
    }

    public function delete(UserResetDeviceIdRequest $request)
    {
        $data = $request->validated();
        User::where('id', $data['id'])->delete();
        return response()->json(msgdata(success(), 'تم الحذف بنجاح', (object)[]));
    }

}
