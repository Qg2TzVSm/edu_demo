<?php


namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Models\EduStudent;
use App\Models\EduTeacher;
use App\Models\LineAuthorize;
use App\Models\LineUser;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Redirect;

class LineAuthController extends Controller
{
    protected function clientId()
    {
        return "1654221299";
    }

    protected function clientSecret()
    {
        return "12181192f6bc704d240483b824b698ac";
    }

    public function index()
    {
        $nonce = request('nonce');
        $line = request('line', 0);
        // 客户端请求的随机码，可以设置规则，这里不校验
        if (empty($nonce) || strlen($nonce)!=8 ){
            //错误处理
            return Redirect::to('www.baidu.com', 302);
        }
        $url = "https://access.line.me/oauth2/v2.1/authorize?";
        $state = csrf_token();
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId(),
            'redirect_uri' => secure_url("/callback?line={$line}"),
            'state' => $state,
            'scope' => "profile openid"
        ], '', '&', PHP_QUERY_RFC3986);
        session(['line_oauth_token' => $state,'nonce' => $nonce]);
        return Redirect::to($url.$query, 302);
    }

    public function callback()
    {
        $code = request('code');
        $state = request('state');
        $auth_type = request('line', 0);
        if (empty($code) || empty($state)){
            $error = request('error');
            if (!empty($error)){
                $err = (string) $error;
            }else{
                $err = '未知错误';
            }
        }else{
            $nonce = session('nonce');
            $session_state = session('line_oauth_token');
            if (empty($nonce) || empty($session_state) || $state !== $session_state){
                $err = '非法请求';
            }else{
                if ($auth_type == 0){ // 绑定line
                    $cache_data = cache("{$nonce}");
                    if (empty($cache_data)){
                        $err = '请求已过期';
                    }else{
                        $result = $this->getToken($code, $nonce, $cache_data['user_type'], $cache_data['user_id']);
                        if ($result !== true){
                            $err = $result;
                        }else{
                            return Redirect::to("https://edu-chat-server.herokuapp.com/?#/bind/?result={$result}", 302);
                        }
                    }
                }else if ($auth_type == 1){ // line 登陆
                    $nonce = str_random(12);
                    $result = $this->loginByLine($code, $nonce);
                    if ($result !== true) {
                        $err = $result;
                    }else{
                        return Redirect::to("https://edu-chat-server.herokuapp.com/?#/login/?nonce={$nonce}", 302);
                    }
                }else{
                    $err = '非法操作';
                }
            }
        }
        // 错误处理
        dd($err);
    }

    protected function getToken($code, $nonce, $user_type, $user_id)
    {
        try {
            $data = $this->authWithLine($code);
            if ($data === false){
                return false;
            }
            $user = $this->decodeJwtGetNameAndPic($data['id_token']);
            $result = [
                'token' => $data['access_token'],
                'name' => $user['name'],
                'avatar' => $user['avatar'],
            ];
            switch ($user_type){
                case 0:
                    EduStudent::find($user_id)->update(['avatar' => $user['avatar']]);
                case 1:
                    EduTeacher::find($user_id)->update(['avatar' => $user['avatar']]);
            }
            $line_user = LineUser::updateOrCreate(['openid'=>$user['openid']], ['avatar'=>$user['avatar']]);
            LineAuthorize::create([
                'line_user_id' => $line_user->id,
                'authorizes_type' => $user_type,
                'edu_teacher_id' => $user_type === 1 ? $user_id : 0,
                'edu_student_id' => $user_type === 0 ? $user_id : 0,
            ]);
            cache(["line_user_type{$user_type}_user_id_{$user_id}" => $result], 60);
            // todo 如果line用户绑定成功后，上面的操作应该后台异步任务取用户openid 更新到数据库，并将token信息存到缓存以及数据库以便使用

            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    protected function loginByLine($code, $nonce)
    {
        try {
            $data = $this->authWithLine($code);
            if ($data === false){
                return false;
            }
            $user = $this->decodeJwtGetNameAndPic($data['id_token']);
            cache(["$nonce" => ['line_user_id' => LineUser::query()->where('openid', $user['openid'])->firstOrFail()->id]]);
            return true;
        }catch (\Throwable $exception){
            return $exception->getMessage();
        }
    }

    protected function decodeJwtGetNameAndPic($jwt)
    {
        try {
            $data = (array) JWT::decode($jwt, $this->clientSecret(), ['HS256']);
            return [
                'openid' => $data['sub'],//暂取sub作为openid
                'name' => $data['name'],
                'avatar' => $data['picture'] ?? null
            ];
        }catch (\Exception $exception){
            dd($exception->getMessage());
        }
    }

    protected function authWithLine($code)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://api.line.me/oauth2/v2.1/token',[
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => secure_url('/callback'),
                    'client_id' => $this->clientId(),
                    'client_secret' => $this->clientSecret()
                ]]);
            $statusCode = $response->getStatusCode(); # 200

            $res = json_decode($response->getBody()->getContents(),true);
            $data = [
                "access_token" => $res['access_token'],
                "expires_in" => $res['expires_in'],
                "id_token" => $res['id_token'],
                "refresh_token" => $res['refresh_token'],
                "scope" => $res['scope'],
                "token_type" => "Bearer"
            ];
            return $data;
        }catch (\Throwable $exception){
            return false;
        }
    }
}