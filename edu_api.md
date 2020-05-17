### 公共部分
#### 1.用户登陆：

##### URL:

- `/api/login`

##### 请求方式：

- POST

##### 需要授权：

- 否


##### 请求参数:

|参数名|是否必须|类型|说明|
|:---- |:---|:----- |----- |
|auth_type |是 |int | 用户类型0学生1老师 |
|email |是 |string | 邮箱 |
|password|是|string|密码|

 

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "token_type": "Bearer",
    "expires_in": 1295999,
    "access_token": "...",
    "refresh_token": "..."
  }
}
```
#### 2.用户绑定line：

##### URL:

- `/api/bind/prepare`

##### 请求方式：

- GET

##### 需要授权：

- 是

##### 请求参数:

- 无

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": "abc123"
}
```
##### 备注:

绑定流程：

- 首先请求该接口获取一个随机字符串，这个接口会校验用户是否以及绑定过，等限制。
- 前端再跳转到后端的 `baseURL + /auth` 地址，这里进行预处理。
- 302跳转到line，让用户授权，授权回来进入到 `baseURL + /callback` 。
- 这里会进行用户与line账号的绑定，以及更新头像等操作。
- 最后携带绑定结果跳转回前端。

### 老师部分
#### 1.老师注册：

##### URL:

- `/api/register`

##### 请求方式：

- POST

##### 需要授权：

- 否

##### 请求参数:

|参数名|是否必须|类型|说明|
|:---- |:---|:----- |----- |
|name |是 |string | 老师名字 |
|email |是 |string | 邮箱 |
|password|是|string|密码|

 

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "statusCode": 200,
    "message": "操作成功"
  }
}
```

#### 2.老师申请学校：

##### URL:

- `/api/school/register`

##### 请求方式：

- POST

##### 需要授权：

- 是

##### 请求参数:

|参数名|是否必须|类型|说明|
|:---- |:---|:----- |----- |
|name |是 |string | 学校名字 |

 
##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "statusCode": 200,
    "message": "操作成功"
  }
}
```

#### 3.老师获取自己创建的且是已通过审核的学校列表：

##### URL:

- `/api/q`

##### 请求方式：

- GET

##### 需要授权：

- 是

##### 请求参数:

- 无

 
##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": [
    {
      "id": 1,
      "name": "testSchool"
    }
  ]
}
```

#### 4.老师通过邮箱地址邀请其它老师成为自己创建的某一学校的老师：

##### URL:

- `/api/invite`

##### 请求方式：

- POST

##### 需要授权：

- 是

##### 请求参数:

|参数名|是否必须|类型|说明|
|:---- |:---|:----- |----- |
|email |是 |string | 被邀请者email |
|school_id |是 |int | 邀请者指定的学校的id |
 
##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "statusCode": 200,
    "message": "操作成功"
  }
}
```
##### 备注:


#### 5.老师获取个人信息：

##### URL:

- `/api/teacher/profile`

##### 请求方式：

- GET

##### 需要授权：

- 是

##### 请求参数:

无
 
##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "id": 1,
    "avatar": "https:\/\/profile.line-scdn.net\/0h2sKM-uT7bVx2NEX7vTkSC0pxYzEBGmsUDgAhPFE3NGtYBC4KTFIgalc3NmhfUCMLS1UjPQYyN25e",
    "name": "test",
    "email": "test@test.com",
    "schools": [
      {
        "id": 1,
        "name": "testSchool"
      },
      {
        "id": 3,
        "name": "test4444"
      }
    ]
  }
}
```
**这里返回值内的schools是这个老师所属的所有学校列表，包括不是自己创建的学校。**


#### 6.老师获取指定学校的所有学生（为了简单未考虑分页）：

##### URL:

- `/api/school/{school}/students`

##### 请求方式：

- GET

##### 需要授权：

- 是

##### 请求参数:

- `{shcool}` 为对应学校的id
 
##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": [
    {
      "id": 3,
      "name": "hello1",
      "email": "hello1@test.com",
      "avatar": null
    },
    {
      "id": 4,
      "name": "hello1",
      "email": "prettydoge@yahoo.com",
      "avatar": null
    }
  ]
}
```

#### 7.老师获取指定学校的关注自己的学生列表（为了简单未考虑分页）：

##### URL:

- `/api/school/{school}/follows`

##### 请求方式：

- GET

##### 需要授权：

- 是

##### 请求参数:

- `{shcool}` 为对应学校的id
 
##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": [
    {
      "id": 3,
      "name": "hello1",
      "email": "hello1@test.com",
      "avatar": null
    }
  ]
}
```


### 学生部分

#### 1.获取个人资料：

##### URL:

- `/api/student/profile`

##### 请求方式：

- GET

##### 需要授权：

- 是

##### 请求参数:


|参数名|是否必须|类型|说明|
|:---- |:---|:----- |----- |
|phone |是 |string | 手机号码 |
|name|是|string|用户名|

 

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "id": 1,
    "avatar": null,
    "name": null,
    "email": "test1@test.com",
    "school": {
      "id": 3,
      "name": "test4444"
    }
  }
}
```

#### 2.获取学校所有老师：

##### URL:

- `/api/student/teachers`

##### 请求方式：

- GET

##### 需要授权：

- 是

##### 请求参数:

- 无

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": [
    {
      "id": 1,
      "name": "test",
      "email": "test@test.com",
      "avatar": null,
      "followed_count": 1
    },
    {
      "id": 3,
      "name": "doge",
      "email": "dog@test.com",
      "avatar": null,
      "followed_count": 0
    }
  ]
}
```
**返回值内的`followed_count`为自己是否已关注这个老师，1为已关注0为未关注**

#### 3.获取关注的所有老师：

##### URL:

- `/api/student/follows`

##### 请求方式：

- GET

##### 需要授权：

- 是

##### 请求参数:

- 无

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": [
    {
      "id": 1,
      "name": "test",
      "email": "test@test.com",
      "avatar": null
    }
  ]
}
```

#### 4.关注一个老师：

##### URL:

- `/api/student/teacher/{{$teacher}}/follow`

##### 请求方式：

- POST

##### 需要授权：

- 是

##### 请求参数:

- `{{$teacher}}` 为老师的ID

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "statusCode": 200,
    "message": "操作成功"
  }
}
```
#### 5.取消关注一个老师：

##### URL:

- `/api/student/teacher/{{$teacher}}/un-follow`

##### 请求方式：

- POST

##### 需要授权：

- 是

##### 请求参数:

- `{{$teacher}}` 为老师的ID

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "statusCode": 200,
    "message": "操作成功"
  }
}
```