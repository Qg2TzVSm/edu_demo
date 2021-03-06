<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\EduTeacher;
use App\Models\LineAuthorize;
use App\Models\LineUser;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\Client;
use Validator;

class EduAuthController extends Controller
{
    use AuthenticatesUsers;

    protected $authType = 1;


    public function model()
    {
        return $this->authType ? app('\App\Models\EduTeacher') : app('\App\Models\EduStudent');
    }
    public function table()
    {
        return $this->authType ? 'edu_teachers' : 'edu_students';
    }
    public function authGuard()
    {
        return $this->authType ? 'users' : 'edu_students';
    }
    public function login(Request $request)
    {
        $auth_type = $request->get('auth_type', 1);
        $this->authType = $auth_type;

        $validator = Validator::make($request->all(), [
            "{$this->username()}"    => "required|exists:{$this->table()}",
            'password' => 'required|between:5,32',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors()->toArray(),401);
        }

        $credentials = $this->credentials($request);

        return $this->authenticate($credentials, 'password','password_client',$this->authGuard());
    }


    public function authenticate($credentials, $grant_type, $client_type, $guard = '')
    {
        $client = new \GuzzleHttp\Client();

        try {
            $url = request()->root() . '/api/oauth/token';
            $oauth_client = Client::query()->where($client_type,1)->latest()->first();
            $config = [
                'grant_type' => $grant_type,
                'client_id' => $oauth_client->id,
                'client_secret' => $oauth_client->secret,
                'scope' => ''
            ];
            if ($guard) {
                $params = array_merge($config, [
                    'username' => $credentials[$this->username()],
                    'password' => $credentials['password'],
                    'provider' => $guard
                ]);
            } else {
                $params = array_merge($config, [
                    'username' => $credentials[$this->username()],
                    'password' => $credentials['password'],
                ]);
            }

            $respond = $client->request('POST', $url, ['form_params' => $params]);
        } catch (RequestException $exception) {
            return $this->failed('账号或密码错误'.$exception->getMessage(),401);
        }

        if ($respond->getStatusCode() !== 401) {
            return $this->message(json_decode($respond->getBody()->getContents(), true));
        }

        return $this->failed('请求失败，服务器错误',401);
    }

    public function bindPrepare()
    {
        $user = \request()->user();
        $user_type = 1;
        // 检测当前用户是否是老师，老师只能绑定一次
        if ($user instanceof EduTeacher){
            if (LineAuthorize::teacher()->where('edu_teacher_id', $user->id)->count()){
                return $this->failed('该账号已与line绑定');
            }
        }else{
            if (LineAuthorize::student()->where('edu_student_id', $user->id)->count()){
                return $this->failed('该账号已与line绑定');
            }
            $user_type = 0;
        }
        $nonce = str_random(8);
        cache(["$nonce" => [
            'user_type' => $user_type,
            'user_id' => $user->id,
        ]], 30);
        return $this->message($nonce);
    }


    public function loginByLinePrepare()
    {
        $verify_params = \request('nonce');
        if (empty($verify_params) || !is_string($verify_params) || strlen($verify_params) != 12){
            return $this->failed('system error');
        }
        $cache = cache($verify_params);
        if (empty($cache)){
            return $this->failed('登陆已过期');
        }
        $user = LineUser::query()->findOrFail($cache['line_user_id']);
        // 返回所有与该账号绑定的老师用户与学生
        return $this->message([
            'teachers' => $user->teacherAuthorizes()->with(["teachers" => function($q){
            $q->select("id", 'name');
        }])->get()->pluck('teachers')->toArray(),
            'students' => $user->studentAuthorizes()->with(["students" => function($q){
                $q->select("id", 'name');
            }])->get()->pluck('students')->toArray(),
        ]);
    }

    public function loginByLine()
    {
        $auth_type = \request('auth_type') ?? 1;
        $this->authType = $auth_type;
        // todo 这里没有校验用户，其实应该校验该登陆账号是否经由line授权
        $user = $this->model()->findOrFail(intval(\request('user_id')));
        Config::set('auth.guards.api.provider', $this->authGuard());
        $token = $user->createToken("auth_by_line")->accessToken;
        return $this->message([
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }
}